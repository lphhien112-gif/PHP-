<?php

namespace App\Core;

/**
 * EduCRM - Env loader (khong phu thuoc thu vien ngoai)
 *
 * Doc file .env (dinh dang KEY=VALUE) o goc project va nap vao bo nho. Bien
 * moi truong THAT (do web server / Docker / PaaS dat) LUON duoc uu tien hon
 * file .env, nen production co the cau hinh hoan toan qua bien moi truong ma
 * khong can .env.
 *
 * Ho tro:
 *  - dong trong / dong bat dau bang '#' (comment) -> bo qua
 *  - gia tri boc trong "..." hoac '...' -> tu dong go dau nhay
 *  - ep "true"/"false" -> bool
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class Env
{
    /** Cac gia tri doc tu file .env (chi khi chua co trong moi truong that). */
    private static array $data = [];

    private static bool $loaded = false;

    /**
     * Nap file .env (idempotent). Goi 1 lan luc bootstrap (public/index.php)
     * hoac dau moi script CLI trong bin/.
     */
    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }
        self::$loaded = true;

        if (!is_file($path) || !is_readable($path)) {
            return; // Khong co .env -> dung gia tri mac dinh trong config/*
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Go dau nhay boc quanh gia tri (neu co)
            $len = strlen($value);
            if ($len >= 2
                && ($value[0] === '"' || $value[0] === "'")
                && $value[$len - 1] === $value[0]
            ) {
                $value = substr($value, 1, -1);
            }

            // Bien moi truong THAT thang the -> KHONG ghi de bang file .env
            if (getenv($key) === false && !isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                self::$data[$key] = $value;
            }
        }
    }

    /**
     * Lay gia tri env theo thu tu uu tien:
     *   getenv() -> $_ENV / $_SERVER -> file .env -> $default
     * Tu ep chuoi "true"/"false" thanh bool.
     */
    public static function get(string $key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $_SERVER[$key] ?? self::$data[$key] ?? null;
        }
        if ($value === null) {
            return $default; // key khong ton tai o bat ky nguon nao
        }
        if (is_string($value)) {
            $lower = strtolower($value);
            if ($lower === 'true') {
                return true;
            }
            if ($lower === 'false') {
                return false;
            }
        }
        return $value;
    }
}
