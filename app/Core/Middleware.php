<?php

namespace App\Core;

/**
 * Middleware — kontrak dasar untuk seluruh middleware.
 * Middleware dijalankan sebelum Controller, dan dapat menghentikan
 * request (redirect/403/dll) dengan memanggil exit di dalam handle().
 */
abstract class Middleware
{
    abstract public function handle(): void;
}
