<?php

namespace ClinicDesk\Core;

/**
 * Flash - Thong bao mot lan (one-time) qua session.
 *
 * Dung cho PRG pattern: sau khi redirect, trang dich doc flash de hien
 * thong bao success/error roi xoa ngay.
 */
class Flash
{
    private static function boot(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function set(string $type, string $message): void
    {
        self::boot();
        $_SESSION['_flash'][$type] = $message;
    }

    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('error', $message);
    }

    /**
     * Lay va xoa flash theo type. Tra null neu khong co.
     */
    public static function pull(string $type): ?string
    {
        self::boot();
        if (!isset($_SESSION['_flash'][$type])) {
            return null;
        }
        $msg = $_SESSION['_flash'][$type];
        unset($_SESSION['_flash'][$type]);
        return $msg;
    }
}
