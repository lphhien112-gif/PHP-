<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\LoginAttemptRepository;
use App\Repositories\RememberTokenRepository;

/**
 * EduCRM - AuthService
 *
 * Chua business rule cua dang nhap / dang xuat / timeout / lockout / remember-me.
 * Controller chi goi service, khong tu xu ly password hay session logic dai dong.
 *
 * F6a Lockout: dem dang nhap sai theo username+IP (bang login_attempts); >= N lan
 *   trong cua so X phut -> khoa Y phut. Ghi audit login_failed.
 * F6b Remember-me: tao token ngau nhien, luu SHA-256 HASH vao remember_tokens,
 *   set cookie HttpOnly+SameSite=Lax+Secure-per-env dang "selector:token";
 *   auto-login khi revisit (verify hash), ROTATE moi lan dung, REVOKE khi logout.
 *   KHONG BAO GIO luu password.
 *
 * Sinh vien: Le Pham Hong Hien - MSSV: 22110059
 */
class AuthService
{
    private UserRepository $users;
    private AuditService $audit;
    private LoginAttemptRepository $attempts;
    private RememberTokenRepository $remember;

    public function __construct(
        ?UserRepository $users = null,
        ?AuditService $audit = null,
        ?LoginAttemptRepository $attempts = null,
        ?RememberTokenRepository $remember = null
    ) {
        $this->users    = $users ?? new UserRepository();
        $this->audit    = $audit ?? new AuditService();
        $this->attempts = $attempts ?? new LoginAttemptRepository();
        $this->remember = $remember ?? new RememberTokenRepository();
    }

    /** IP client (an toan, gioi han do dai). */
    private function clientIp(): string
    {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }

    /**
     * F6a: kiem tra tai khoan/IP co dang bi khoa khong.
     * Tra ['locked'=>bool, 'retry_after'=>int giay con lai].
     */
    public function lockStatus(string $username): array
    {
        $username = trim($username);
        $ip       = $this->clientIp();
        $window   = (int) config('login_attempt_window', 600);
        $maxTries = (int) config('login_max_attempts', 5);
        $lockTime = (int) config('login_lockout_time', 900);

        $count = $this->attempts->countRecent($username, $ip, $window);
        if ($count < $maxTries) {
            return ['locked' => false, 'retry_after' => 0];
        }

        // Da vuot nguong: tinh thoi gian con lai cua lockout ke tu lan sai dau.
        $first = $this->attempts->firstAttemptAt($username, $ip, $window);
        $retry = $first ? ($first + $lockTime - time()) : $lockTime;
        if ($retry <= 0) {
            // Cua so lockout da het -> coi nhu mo lai (cac ban ghi cu se tu roi
            // ra ngoai cua so $window theo thoi gian).
            return ['locked' => false, 'retry_after' => 0];
        }
        return ['locked' => true, 'retry_after' => $retry];
    }

    /**
     * Xac thuc dang nhap. Tra:
     *   ['ok'=>true, 'user'=>[...]]                       hoac
     *   ['ok'=>false, 'error'=>'...']                     hoac
     *   ['ok'=>false, 'locked'=>true, 'retry_after'=>N, 'error'=>'...']
     *
     * Thong bao loi chung chung (khong phan biet sai user hay sai pass) de
     * tranh user enumeration.
     */
    public function attempt(string $username, string $password): array
    {
        $username = trim($username);
        $ip       = $this->clientIp();

        if ($username === '' || $password === '') {
            return ['ok' => false, 'error' => 'Vui long nhap day du ten dang nhap va mat khau.'];
        }

        // F6a: chan som neu dang bi khoa.
        $lock = $this->lockStatus($username);
        if ($lock['locked']) {
            $mins = (int) ceil($lock['retry_after'] / 60);
            return [
                'ok'          => false,
                'locked'      => true,
                'retry_after' => $lock['retry_after'],
                'error'       => 'Tai khoan tam thoi bi khoa do dang nhap sai nhieu lan. Vui long thu lai sau '
                                 . $mins . ' phut.',
            ];
        }

        $user = $this->users->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            log_app('warning', 'Login failed for username=' . $username);
            // F6a: ghi nhan 1 lan sai (username + IP).
            $this->attempts->record($username, $ip);
            // F3: ghi audit login_failed (user_id = null, KHONG log mat khau).
            $this->audit->log('login_failed', 'auth', null, 'Dang nhap that bai cho username=' . $username, null);

            // Sau khi ghi, neu vua cham nguong -> bao khoa luon.
            $lock = $this->lockStatus($username);
            if ($lock['locked']) {
                $mins = (int) ceil($lock['retry_after'] / 60);
                return [
                    'ok'          => false,
                    'locked'      => true,
                    'retry_after' => $lock['retry_after'],
                    'error'       => 'Ban da dang nhap sai qua nhieu lan. Tai khoan bi khoa '
                                     . $mins . ' phut.',
                ];
            }
            return ['ok' => false, 'error' => 'Ten dang nhap hoac mat khau khong dung.'];
        }

        // Dung: don sach cac lan sai cua username+IP.
        $this->attempts->clear($username, $ip);

        return [
            'ok'   => true,
            'user' => [
                'id'        => (int) $user['id'],
                'username'  => $user['username'],
                'full_name' => $user['full_name'],
                'role'      => $user['role'],
            ],
        ];
    }

    /**
     * Khoi tao session sau khi xac thuc thanh cong:
     *   regenerate id (chong session fixation) + luu thong tin user.
     */
    public function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user']          = $user;
        $_SESSION['login_at']      = time();
        $_SESSION['last_activity'] = time();

        $this->audit->log('login', 'auth', null, $user['username'] . ' dang nhap he thong');
    }

    /**
     * Dang xuat sach: revoke remember-me, xoa toan bo session data, xoa cookie,
     * destroy session.
     */
    public function logout(): void
    {
        $u = current_user();
        if ($u) {
            $this->audit->log('logout', 'auth', null, ($u['username'] ?? 'user') . ' dang xuat', (int) $u['id']);
            // F6b: revoke TAT CA remember token cua user nay.
            $this->remember->deleteAllForUser((int) $u['id']);
        }

        // F6b: xoa cookie remember-me phia trinh duyet.
        $this->clearRememberCookie();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();
    }

    // =================================================================
    // F6b - Remember-me
    // =================================================================

    private function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) == 443);
    }

    private function cookieName(): string
    {
        return (string) config('remember_cookie', 'eduremember');
    }

    /**
     * F6b: phat hanh 1 remember token moi cho user + set cookie.
     * selector (cong khai, dung tra cuu) : token (bi mat, so hash).
     */
    public function issueRememberToken(int $userId): void
    {
        $selector = bin2hex(random_bytes(12));         // 24 hex
        $token    = bin2hex(random_bytes(32));         // 64 hex (bi mat)
        $hash     = hash('sha256', $token);            // luu HASH, khong luu token tho
        $lifetime = (int) config('remember_lifetime', 1209600);
        $expires  = date('Y-m-d H:i:s', time() + $lifetime);

        $this->remember->create($userId, $selector, $hash, $expires);
        $this->setRememberCookie($selector . ':' . $token, $lifetime);
    }

    private function setRememberCookie(string $value, int $lifetime): void
    {
        setcookie($this->cookieName(), $value, [
            'expires'  => time() + $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $this->isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[$this->cookieName()] = $value; // dong bo trong cung request
    }

    private function clearRememberCookie(): void
    {
        $name = $this->cookieName();
        setcookie($name, '', [
            'expires'  => time() - 42000,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $this->isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        unset($_COOKIE[$name]);
    }

    /**
     * F6b: thu auto-login bang remember cookie (goi o bootstrap khi chua co session).
     * Verify hash, ROTATE token (xoa cu, phat moi), khoi tao session.
     * Tra true neu auto-login thanh cong.
     */
    public function attemptRememberLogin(): bool
    {
        if (current_user()) {
            return false; // da co session
        }
        $raw = (string) ($_COOKIE[$this->cookieName()] ?? '');
        if ($raw === '' || strpos($raw, ':') === false) {
            return false;
        }

        [$selector, $token] = explode(':', $raw, 2);
        // Dinh dang co ban: selector 24 hex, token 64 hex.
        if (!preg_match('/^[a-f0-9]{24}$/', $selector) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->clearRememberCookie();
            return false;
        }

        $row = $this->remember->findValidBySelector($selector);
        if (!$row) {
            $this->clearRememberCookie();
            return false;
        }

        // So sanh hash an toan thoi gian.
        $calc = hash('sha256', $token);
        if (!hash_equals($row['token_hash'], $calc)) {
            // Co the bi danh cap selector / tan cong -> thu hoi token nay.
            $this->remember->deleteBySelector($selector);
            $this->clearRememberCookie();
            log_app('warning', 'Remember-me token hash mismatch for selector=' . $selector);
            return false;
        }

        $user = $this->users->findById((int) $row['user_id']);
        if (!$user) {
            $this->remember->deleteBySelector($selector);
            $this->clearRememberCookie();
            return false;
        }

        // ROTATE: huy token vua dung, phat token moi (chong replay).
        $this->remember->deleteBySelector($selector);

        $this->login([
            'id'        => (int) $user['id'],
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
        ]);
        $this->issueRememberToken((int) $user['id']);

        return true;
    }
}
