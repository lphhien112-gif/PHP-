<?php

namespace App\Controllers;

use App\Services\AuthService;

/**
 * EduCRM - AuthController
 *
 * THIN controller: doc request -> goi AuthService -> render/redirect.
 * KHONG viet SQL, KHONG xu ly password/session logic dai dong (o Service).
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    /** GET /login - hien form dang nhap (khong yeu cau session). */
    public function loginForm(): void
    {
        // Da dang nhap roi thi vao thang dashboard.
        if (current_user()) {
            redirect('/dashboard');
        }
        echo render('auth/login', ['title' => 'Dang nhap'], 'layouts/auth');
        clear_old();
    }

    /** POST /login - validate + verify password + regenerate + redirect. */
    public function handleLogin(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Phien lam viec khong hop le (CSRF). Vui long thu lai.');
            redirect('/login');
        }

        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $remember = !empty($_POST['remember']); // F6b: checkbox "ghi nho dang nhap"

        $result = $this->auth->attempt($username, $password);

        if (!$result['ok']) {
            set_old(['username' => trim($username)], ['login' => $result['error']]);
            flash('error', $result['error']);
            redirect('/login');
        }

        $this->auth->login($result['user']);

        // F6b: chi phat remember token khi user chu dong tick.
        if ($remember) {
            $this->auth->issueRememberToken((int) $result['user']['id']);
        }

        flash('success', 'Xin chao ' . $result['user']['full_name'] . ', dang nhap thanh cong!');
        redirect('/dashboard');
    }

    /** POST /logout - dang xuat sach + redirect /login. */
    public function logout(): void
    {
        $this->auth->logout();
        // Tao session moi de mang flash thong bao.
        session_start();
        flash('success', 'Ban da dang xuat thanh cong.');
        redirect('/login');
    }
}
