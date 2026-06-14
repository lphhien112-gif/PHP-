<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;
use WorkshopHub\Support\Session;
use WorkshopHub\Support\Storage;

/**
 * WorkshopHub — DashboardController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * Mon hoc   : Lap trinh Web     — GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 *   GET /dashboard     -> chi cho user da dang nhap (require_login).
 *   GET /session-demo  -> hien thi thong tin session phuc vu debug.
 */
class DashboardController
{
    /** GET /dashboard — bao ve bang require_login(). */
    public function index(): void
    {
        require_login(); // chua login hoac het phien -> redirect /login + flash

        $registrations = (new Storage('registrations.json'))->all();
        $stats = [
            'total'     => count($registrations),
            'pending'   => count(array_filter($registrations, fn($r) => ($r['status'] ?? '') === 'pending')),
            'confirmed' => count(array_filter($registrations, fn($r) => ($r['status'] ?? '') === 'confirmed')),
        ];

        Response::view('dashboard', [
            'title'         => 'Bang dieu khien — WorkshopHub',
            'user'          => current_user(),
            'stats'         => $stats,
            'registrations' => $registrations,
            'idleTimeout'   => Session::idleTimeout(),
        ]);
    }

    /** GET /session-demo — debug session (cung can dang nhap). */
    public function sessionDemo(): void
    {
        require_login();

        // Loc bo flash noi bo, chi hien du lieu session co y nghia debug.
        $safe = $_SESSION;
        unset($safe['_flash'], $safe['_old'], $safe['_errors']);

        Response::view('session-demo', [
            'title'       => 'Session Demo — WorkshopHub',
            'sessionId'   => session_id(),
            'sessionData' => $safe,
            'idleTimeout' => Session::idleTimeout(),
        ]);
    }
}
