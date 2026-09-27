<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Validator;
use App\Models\RoleModel;
use App\Models\UserModel;

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function showLogin(): void
    {
        // 1. Cek Cookie Remember Me jika sesi kosong
        if (!Auth::check() && !empty($_COOKIE['lau_remember_token'])) {
            $token = $_COOKIE['lau_remember_token'];
            $parts = explode(':', base64_decode($token));
            if (count($parts) === 2) {
                [$cUserId, $cHash] = $parts;
                $user = $this->userModel->find((int)$cUserId);
                if ($user && hash_equals(hash('sha256', $user['password']), $cHash)) {
                    $this->setSessionLogin($user);
                    $this->redirectToDashboard(Auth::role() ?? 'customer');
                    return;
                }
            }
        }

        if (Auth::check()) {
            $this->redirectToDashboard(Auth::role() ?? 'customer');
            return;
        }

        $this->view('auth.login', ['title' => 'Login — ' . config('app.name')]);
    }

    public function login(): void
    {
        $validator = new Validator($this->request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->renderLoginWithErrors($validator->errors());
            return;
        }

        $user = $this->userModel->findByEmail($this->input('email'));

        if (!$user || !password_verify((string) $this->input('password'), $user['password'])) {
            $this->renderLoginWithErrors(['email' => ['Email atau password salah.']]);
            return;
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            $this->renderLoginWithErrors(['email' => ['Akun Anda nonaktif. Hubungi toko untuk info lebih lanjut.']]);
            return;
        }

        // Set Sesi Login
        $roleName = $this->setSessionLogin($user);

        // Set Cookie Remember Me jika dicentang (aktif selama 30 hari)
        if (!empty($this->input('remember_me'))) {
            $cookiePayload = base64_encode($user['id'] . ':' . hash('sha256', $user['password']));
            setcookie(
                'lau_remember_token',
                $cookiePayload,
                [
                    'expires'  => time() + (30 * 24 * 60 * 60),
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                ]
            );
        }

        $this->redirectToDashboard($roleName);
    }

    private function setSessionLogin(array $user): string
    {
        $roleName = 'customer';
        if (!empty($user['role_name'])) {
            $roleName = strtolower(trim((string)$user['role_name']));
        } elseif (!empty($user['role_id'])) {
            try {
                $roleModel = new RoleModel();
                $roleData = $roleModel->find((int)$user['role_id']);
                if ($roleData && !empty($roleData['name'])) {
                    $roleName = strtolower(trim((string)$roleData['name']));
                }
            } catch (\Throwable $th) {
                $roleName = ((int)$user['role_id'] === 1) ? 'owner' : 'customer';
            }
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        Auth::login([
            'id'        => (int)$user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $roleName,
            'role_name' => $roleName,
            'role_id'   => $user['role_id'] ?? null,
        ]);

        return $roleName;
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard(Auth::role() ?? 'customer');
            return;
        }

        $this->view('auth.register', ['title' => 'Daftar Akun — ' . config('app.name')]);
    }

    public function register(): void
    {
        $name            = trim((string) $this->input('name'));
        $email           = trim((string) $this->input('email'));
        $phone           = trim((string) $this->input('phone'));
        $password        = (string) $this->input('password');
        $passwordConfirm = (string) ($this->input('password_confirmation') ?? $this->input('confirm_password') ?? '');

        $errors = [];
        if (empty($name)) {
            $errors['name'] = ['Nama lengkap wajib diisi.'];
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['Format alamat email tidak valid.'];
        }

        // Validasi syarat password terstruktur
        if (strlen($password) < 8) {
            $errors['password'][] = 'Password minimal harus 8 karakter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors['password'][] = 'Password harus mengandung minimal satu huruf kapital (A-Z).';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors['password'][] = 'Password harus mengandung minimal satu angka (0-9).';
        }

        if (empty($passwordConfirm)) {
            $errors['password_confirmation'] = ['Konfirmasi password wajib diisi.'];
        } elseif ($password !== $passwordConfirm) {
            $errors['password_confirmation'] = ['Konfirmasi password tidak cocok.'];
        }

        if (empty($errors) && $this->userModel->findByEmail($email)) {
            $errors['email'] = ['Alamat email ini sudah terdaftar. Silakan login.'];
        }

        if (!empty($errors)) {
            $this->renderRegisterWithErrors($errors);
            return;
        }

        $cleanUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', $name)[0]));
        $username = $cleanUsername . rand(100, 999);

        $roleId = 2;
        try {
            $roleModel = new RoleModel();
            $customerRole = $roleModel->findByName('customer');
            if ($customerRole && isset($customerRole['id'])) {
                $roleId = (int)$customerRole['id'];
            }
        } catch (\Throwable $th) {
            $roleId = 2;
        }

        try {
            $userData = [
                'role_id'   => $roleId,
                'name'      => $name,
                'username'  => $username,
                'email'     => $email,
                'password'  => password_hash($password, PASSWORD_BCRYPT),
                'is_active' => 1,
            ];

            if (!empty($phone)) {
                $userData['phone'] = $phone;
            }

            $userId = $this->userModel->create($userData);

            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            Auth::login([
                'id'        => (int)$userId,
                'name'      => $name,
                'email'     => $email,
                'role'      => 'customer',
                'role_name' => 'customer',
                'role_id'   => $roleId,
            ]);

            $this->redirect('/');
            return;
        } catch (\Throwable $e) {
            $this->renderRegisterWithErrors([
                'general' => ['Pendaftaran gagal: ' . $e->getMessage()]
            ]);
        }
    }

    public function logout(): void
    {
        Auth::logout();

        // Hapus Cookie Remember Me
        if (isset($_COOKIE['lau_remember_token'])) {
            setcookie('lau_remember_token', '', time() - 3600, '/');
            unset($_COOKIE['lau_remember_token']);
        }

        // Bersihkan session secara total dari memori server dan cookie browser
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
        }

        $this->redirect('login');
    }

    private function renderLoginWithErrors(array $errors): void
    {
        $this->view('auth.login', [
            'title'  => 'Login — ' . config('app.name'),
            'errors' => $errors,
            'old'    => $this->request->only(['email']),
        ]);
    }

    private function renderRegisterWithErrors(array $errors): void
    {
        $this->view('auth.register', [
            'title'  => 'Daftar Akun — ' . config('app.name'),
            'errors' => $errors,
            'old'    => $this->request->only(['name', 'email', 'phone']),
        ]);
    }

    private function redirectToDashboard(string $role): void
    {
        match (strtolower($role)) {
            'owner'      => $this->redirect('owner/dashboard'),
            'programmer' => $this->redirect('programmer/dashboard'),
            default      => $this->redirect('/'),
        };
    }

    public function showForgotPassword(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard(Auth::role() ?? 'customer');
            return;
        }

        $this->view('auth.forgot_password', [
            'title' => 'Lupa Password — ' . config('app.name')
        ]);
    }

    public function sendResetLink(): void
    {
        $email = trim((string) $this->input('email'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth.forgot_password', [
                'title' => 'Lupa Password — ' . config('app.name'),
                'error' => 'Format alamat email tidak valid.',
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $this->view('auth.forgot_password', [
                'title' => 'Lupa Password — ' . config('app.name'),
                'error' => 'Email tidak terdaftar dalam sistem.',
            ]);
            return;
        }

        // Buat token unik 32 karakter
        $token = bin2hex(random_bytes(32));

        // Simpan token ke database password_resets
        try {
            $db = new \PDO(
                "mysql:host=" . config('database.host', '127.0.0.1') . ";dbname=" . config('database.database', 'lau_store') . ";charset=utf8mb4",
                config('database.username', 'root'),
                config('database.password', '')
            );
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Hapus token lama untuk email ini jika ada
            $del = $db->prepare("DELETE FROM password_resets WHERE email = :email");
            $del->execute(['email' => $email]);

            // Masukkan token baru
            $stmt = $db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())");
            $stmt->execute(['email' => $email, 'token' => $token]);
        } catch (\Throwable $th) {
            $this->view('auth.forgot_password', [
                'title' => 'Lupa Password — ' . config('app.name'),
                'error' => 'Gagal memproses token reset: ' . $th->getMessage(),
            ]);
            return;
        }

        $resetLink = base_url('reset-password?token=' . $token);

        // Jika PHPMailer tersedia di vendor, kirimkan email asli
        $mailSent = false;
        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
                $mail->SMTPAuth   = true;
                $mail->Username   = env('MAIL_USERNAME', '');
                $mail->Password   = env('MAIL_PASSWORD', '');
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)env('MAIL_PORT', 587);

                $mail->setFrom(env('MAIL_USERNAME', 'no-reply@udlaustore.test'), env('MAIL_FROM_NAME', 'UD. LAU STORE'));
                $mail->addAddress($email, $user['name'] ?? 'Pelanggan');

                $mail->isHTML(true);
                $mail->Subject = 'Reset Password Akun - ' . config('app.name');
                $mail->Body    = "
                    <div style='font-family: sans-serif; max-width: 480px; margin: auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;'>
                        <h2 style='color: #065f46;'>UD. LAU STORE</h2>
                        <p>Halo <b>" . htmlspecialchars($user['name'] ?? 'Pelanggan') . "</b>,</p>
                        <p>Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah untuk membuat password baru:</p>
                        <p style='text-align: center; margin: 25px 0;'>
                            <a href='{$resetLink}' style='background: #065f46; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Buat Password Baru</a>
                        </p>
                        <p style='font-size: 0.8rem; color: #64748b;'>Link ini hanya berlaku selama 30 menit. Jika Anda tidak meminta reset password, abaikan email ini.</p>
                    </div>";

                $mail->send();
                $mailSent = true;
            } catch (\Throwable $e) {
                $mailSent = false;
            }
        }

        $this->view('auth.forgot_password', [
            'title'     => 'Lupa Password — ' . config('app.name'),
            'success'   => 'Link reset password telah dikirim ke email Anda! Silakan cek kotak masuk (atau folder spam).',
            'resetLink' => !$mailSent ? $resetLink : null,
            'email'     => $email
        ]);
    }

    public function showResetPassword(): void
    {
        $token = trim((string) $this->input('token'));
        if (empty($token)) {
            $this->redirect('login');
            return;
        }

        $this->view('auth.reset_password', [
            'title' => 'Buat Password Baru — ' . config('app.name'),
            'token' => $token
        ]);
    }

    public function resetPassword(): void
    {
        $token           = trim((string) $this->input('token'));
        $password        = (string) $this->input('password');
        $passwordConfirm = (string) $this->input('password_confirmation');

        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal harus 8 karakter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kapital.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (!empty($errors)) {
            $this->view('auth.reset_password', [
                'title'  => 'Buat Password Baru — ' . config('app.name'),
                'token'  => $token,
                'errors' => $errors
            ]);
            return;
        }

        try {
            $db = new \PDO(
                "mysql:host=" . config('database.host', '127.0.0.1') . ";dbname=" . config('database.database', 'lau_store') . ";charset=utf8mb4",
                config('database.username', 'root'),
                config('database.password', '')
            );
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Validasi token dan masa berlaku (30 menit)
            $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = :token AND created_at >= NOW() - INTERVAL 30 MINUTE LIMIT 1");
            $stmt->execute(['token' => $token]);
            $resetRecord = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$resetRecord) {
                $this->view('auth.reset_password', [
                    'title'  => 'Buat Password Baru — ' . config('app.name'),
                    'token'  => $token,
                    'errors' => ['Token reset tidak valid atau sudah kedaluwarsa. Silakan ajukan ulang.']
                ]);
                return;
            }

            // Update password user di tabel users
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $upd = $db->prepare("UPDATE users SET password = :p WHERE email = :e");
            $upd->execute(['p' => $newHash, 'e' => $resetRecord['email']]);

            // Hapus token yang sudah digunakan
            $del = $db->prepare("DELETE FROM password_resets WHERE email = :e");
            $del->execute(['e' => $resetRecord['email']]);

            $this->view('auth.login', [
                'title'   => 'Login — ' . config('app.name'),
                'success' => 'Password berhasil diperbarui! Silakan masuk dengan password baru Anda.'
            ]);
        } catch (\Throwable $th) {
            $this->view('auth.reset_password', [
                'title'  => 'Buat Password Baru — ' . config('app.name'),
                'token'  => $token,
                'errors' => ['Gagal menyimpan password: ' . $th->getMessage()]
            ]);
        }
    }
}