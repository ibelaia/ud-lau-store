<?php

namespace App\Core;

/**
 * Validator — validasi input sederhana ala Laravel, tanpa dependency.
 *
 * Contoh pemakaian di Controller:
 *   $validator = new Validator($this->request->all(), [
 *       'email'    => 'required|email',
 *       'password' => 'required|min:8',
 *   ]);
 *   if ($validator->fails()) {
 *       return $this->view('auth.login', ['errors' => $validator->errors()]);
 *   }
 *
 * Rule yang didukung: required, email, min:x, max:x, numeric, confirmed,
 * unique:table,column (cek ke database lewat Database::connection()).
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->runValidation();
    }

    private function runValidation(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        switch ($ruleName) {
            case 'required':
                if ($value === null || trim((string) $value) === '') {
                    $this->addError($field, "Kolom {$field} wajib diisi.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Format email tidak valid.");
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "Kolom {$field} harus berupa angka.");
                }
                break;

            case 'min':
                if ($value !== null && mb_strlen((string) $value) < (int) $param) {
                    $this->addError($field, "Kolom {$field} minimal {$param} karakter.");
                }
                break;

            case 'max':
                if ($value !== null && mb_strlen((string) $value) > (int) $param) {
                    $this->addError($field, "Kolom {$field} maksimal {$param} karakter.");
                }
                break;

            case 'confirmed':
                $confirmField = "{$field}_confirmation";
                if (($this->data[$confirmField] ?? null) !== $value) {
                    $this->addError($field, "Konfirmasi {$field} tidak cocok.");
                }
                break;

            case 'unique':
                // Format: unique:table,column
                [$table, $column] = array_pad(explode(',', (string) $param, 2), 2, 'id');
                if ($value !== null && $value !== '' && $this->existsInDatabase($table, $column, $value)) {
                    $this->addError($field, "Kolom {$field} sudah terdaftar.");
                }
                break;
        }
    }

    private function existsInDatabase(string $table, string $column, mixed $value): bool
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        $stmt = Database::connection()->prepare(
            "SELECT 1 FROM {$table} WHERE {$column} = :value LIMIT 1"
        );
        $stmt->execute(['value' => $value]);

        return (bool) $stmt->fetch();
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Ambil pesan error pertama untuk satu field — memudahkan tampilan di View. */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
