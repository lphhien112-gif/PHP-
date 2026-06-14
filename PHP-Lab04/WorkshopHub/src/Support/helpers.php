<?php

/**
 * WorkshopHub — Global helper functions
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * h()/e()        : escape output (chong XSS).
 * redirect()     : PRG redirect roi exit.
 * flash_set/get  : flash message hien dung mot lan.
 * old()/errors() : giu lai input cu + loi theo field khi validate that bai.
 * is_logged_in() : trang thai dang nhap.
 * require_login(): chan route can auth -> redirect /login + flash.
 */

use WorkshopHub\Response;
use WorkshopHub\Support\Session;

if (!function_exists('h')) {
    /** Escape mot gia tri de in ra HTML an toan. */
    function h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('e')) {
    /** Alias cua h(). */
    function e(?string $value): string
    {
        return h($value);
    }
}

if (!function_exists('redirect')) {
    /** Redirect (PRG) roi dung script ngay. */
    function redirect(string $url, int $code = 303): void
    {
        Response::redirect($url, $code);
        exit;
    }
}

if (!function_exists('flash_set')) {
    /** Dat flash message ($type: success|error|warning). */
    function flash_set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }
}

if (!function_exists('flash_get')) {
    /** Lay & xoa flash message (hien dung mot lan). */
    function flash_get(string $type): ?string
    {
        if (!empty($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }
}

if (!function_exists('flash_keep_input')) {
    /** Luu old input + field errors vao session de re-render form. */
    function flash_keep_input(array $old, array $errors): void
    {
        $_SESSION['_old']    = $old;
        $_SESSION['_errors'] = $errors;
    }
}

if (!function_exists('old')) {
    /** Lay gia tri old input cho mot field (sau redirect khi loi). */
    function old(string $field, string $default = ''): string
    {
        return $_SESSION['_old'][$field] ?? $default;
    }
}

if (!function_exists('old_flush')) {
    /** Xoa old input + errors sau khi da render xong form. */
    function old_flush(): void
    {
        unset($_SESSION['_old'], $_SESSION['_errors']);
    }
}

if (!function_exists('field_error')) {
    /** Lay thong bao loi cho mot field cu the (rong neu khong co). */
    function field_error(string $field): string
    {
        return $_SESSION['_errors'][$field] ?? '';
    }
}

if (!function_exists('is_logged_in')) {
    /** Da dang nhap chua? */
    function is_logged_in(): bool
    {
        return Session::isLoggedIn();
    }
}

if (!function_exists('current_user')) {
    /** Thong tin user dang dang nhap (rong neu chua login). */
    function current_user(): array
    {
        if (!is_logged_in()) {
            return [];
        }
        return [
            'id'    => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'name'  => $_SESSION['user_name'] ?? null,
            'role'  => $_SESSION['role'] ?? null,
        ];
    }
}

if (!function_exists('require_login')) {
    /**
     * Chan route can dang nhap. Neu chua login (hoac het phien) ->
     * flash + redirect ve /login.
     */
    function require_login(): void
    {
        // Kiem tra idle timeout truoc; neu het han -> da logout sach.
        if (Session::checkIdleTimeout()) {
            // logoutClean() da destroy session cu (xoa cookie "deleted");
            // tao session moi voi cookie hop le de luu flash hien tren /login.
            Session::startFresh();
            flash_set('warning', 'Phien dang nhap da het han do khong hoat dong. Vui long dang nhap lai.');
            redirect('/login');
        }
        if (!is_logged_in()) {
            flash_set('error', 'Ban can dang nhap de truy cap khu vuc nay.');
            redirect('/login');
        }
    }
}
