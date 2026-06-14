<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;

/**
 * WorkshopHub — HomeController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * ------------------------------------------------------------------
 * GET / : trang tong quan ung dung.
 */
class HomeController
{
    public function index(): void
    {
        Response::view('home', [
            'title' => 'WorkshopHub — Cong dang ky Workshop',
        ]);
    }
}
