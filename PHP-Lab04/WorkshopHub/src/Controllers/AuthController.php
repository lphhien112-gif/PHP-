<?php

namespace WorkshopHub\Controllers;

use WorkshopHub\Response;
use WorkshopHub\Support\Session;
use WorkshopHub\Support\Storage;
use WorkshopHub\Support\Validator;

/**
 * WorkshopHub — AuthController
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien — MSSV: 22110059
 * Mon hoc   : Lap trinh Web     — GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 *   GET  /login  -> form dang nhap.
 *   POST /login  -> validate + verify password + regenerate session + PRG.
 *   POST /logout -> logout sach (clear data, destroy, xoa cookie).
 */
class AuthController
{
    /** GET /login — form dang nhap. Neu da login thi ve dashboard. */
    public function loginForm(): void
    {
        if (Session::isLoggedIn()) {
            redirect('/dashboard');
        }
        Response::view('auth/login', [
            'title' => 'Dang nhap nhan vien — WorkshopHub',
        ]);
        old_flush();
    }

    /**
     * POST /login — xac thuc.
     * Sai -> flash error + redirect /login (giu lai email).
     * Dung -> Session::login() (co session_regenerate_id(true)) + redirect /dashboard.
     */
    public function handleLogin(): void
    {
        $input = [
            'email'    => trim($_POST['email'] ?? ''),
            'password' => (string) ($_POST['password'] ?? ''),
        ];

        // Validate dinh dang truoc khi truy van.
        $v = new Validator($input);
        $v->required('email', 'Email')->email('email', 'Email');
        $v->required('password', 'Mat khau');

        if ($v->fails()) {
            flash_keep_input(['email' => $input['email']], $v->errors());
            flash_set('error', 'Vui long nhap dung email va mat khau.');
            redirect('/login');
        }

        // Tim user trong storage/users.json.
        $user = $this->findUserByEmail($input['email']);

        // Verify password bang password_verify (hash bcrypt da seed).
        if ($user === null || !password_verify($input['password'], $user['password_hash'])) {
            // Thong bao chung chung — khong tiet lo email co ton tai hay khong.
            flash_keep_input(['email' => $input['email']], []);
            flash_set('error', 'Email hoac mat khau khong dung.');
            redirect('/login');
        }

        // Dang nhap thanh cong: regenerate id + luu session.
        Session::login($user);

        flash_set('success', 'Xin chao ' . $user['name'] . '! Ban da dang nhap thanh cong.');
        redirect('/dashboard');
    }

    /** POST /logout — logout sach roi ve /login. */
    public function logout(): void
    {
        Session::logoutClean();
        // logoutClean huy session + xoa cookie; tao session moi (cookie hop le)
        // de flash hien tren /login sau khi redirect.
        Session::startFresh();
        flash_set('success', 'Ban da dang xuat. Hen gap lai!');
        redirect('/login');
    }

    private function findUserByEmail(string $email): ?array
    {
        $users = (new Storage('users.json'))->all();
        foreach ($users as $user) {
            if (strcasecmp($user['email'] ?? '', $email) === 0) {
                return $user;
            }
        }
        return null;
    }
}
