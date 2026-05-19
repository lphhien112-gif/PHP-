<?php

namespace BookNest\Controllers;

use BookNest\Response;

/**
 * AuthController
 * 
 * Xử lý login/logout demo
 */
class AuthController
{
    /**
     * GET /login → Form đăng nhập (200 OK, HTML)
     */
    public function loginForm(): void
    {
        $content = Response::renderView('auth/login', [
            'title' => 'Đăng nhập — BookNest',
        ]);
        Response::html($content);
    }

    /**
     * POST /login → Xử lý đăng nhập demo
     * Nếu đúng username/password → redirect về /
     * Nếu sai → hiện lại form + lỗi
     */
    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Demo: chấp nhận admin/admin
        if ($username === 'admin' && $password === 'admin') {
            Response::redirect('/');
            return;
        }

        // Sai thông tin → hiện lại form
        $content = Response::renderView('auth/login', [
            'title' => 'Đăng nhập — BookNest',
            'error' => 'Sai tên đăng nhập hoặc mật khẩu. (Gợi ý: admin/admin)',
            'old'   => ['username' => $username],
        ]);
        Response::html($content, 401);
    }

    /**
     * GET /logout → Redirect về trang chủ (302 Found)
     */
    public function logout(): void
    {
        Response::redirect('/');
    }
}
