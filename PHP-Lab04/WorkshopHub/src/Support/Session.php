<?php

namespace WorkshopHub\Support;

/**
 * WorkshopHub — Session helper
 * ------------------------------------------------------------------
 * Sinh vien : Le Pham Hong Hien  —  MSSV: 22110059
 * Mon hoc   : Lap trinh Web      —  GVHD: TS. Tran Anh Tuan
 * ------------------------------------------------------------------
 * - Cau hinh cookie params (HttpOnly, SameSite=Lax, Secure theo env)
 *   TRUOC khi session_start().
 * - Idle timeout, regenerate id, logout sach (logout_clean).
 */
class Session
{
    /** Idle timeout: 15 phut cho moi truong "that". */
    public const IDLE_TIMEOUT = 900;

    /** Idle timeout demo ngan de test (10s) khi bat WSH_DEMO_TIMEOUT=1. */
    public const IDLE_TIMEOUT_DEMO = 10;

    /**
     * Khoi tao session voi cookie flags an toan.
     * Bat buoc goi session_set_cookie_params() TRUOC session_start().
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Secure chi bat khi chay tren HTTPS (production). Local HTTP -> tat,
        // neu bat se khien trinh duyet khong gui cookie tren HTTP.
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) == 443);

        session_set_cookie_params([
            'lifetime' => 0,          // cookie phien — mat khi dong trinh duyet
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,   // theo moi truong
            'httponly' => true,       // JS khong doc duoc cookie -> chong XSS doc session
            'samesite' => 'Lax',      // chong CSRF co ban cho request cross-site
        ]);

        session_name('WSHSESSID');
        session_start();
    }

    /**
     * Tao mot session moi SACH sau khi da logoutClean().
     * Goi session_regenerate_id(true) de PHP gui Set-Cookie hop le,
     * ghi de cookie "deleted" do logoutClean() dat truoc do —
     * nho vay flash message van duoc luu va doc o request sau.
     */
    public static function startFresh(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Idle timeout hieu luc.
     * Bat che do demo (timeout ngan) bang 1 trong 2 cach:
     *   - bien moi truong WSH_DEMO_TIMEOUT=1, hoac
     *   - tao file storage/.demo_timeout (tien khi test bang built-in server).
     */
    public static function idleTimeout(): int
    {
        $demo = getenv('WSH_DEMO_TIMEOUT') === '1'
            || file_exists(__DIR__ . '/../../storage/.demo_timeout');
        return $demo ? self::IDLE_TIMEOUT_DEMO : self::IDLE_TIMEOUT;
    }

    /** Da dang nhap chua? */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Kiem tra idle timeout. Neu het han -> logout sach, tra ve true.
     * Neu con han -> cap nhat last_activity_at, tra ve false.
     */
    public static function checkIdleTimeout(): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }
        $last = $_SESSION['last_activity_at'] ?? time();
        if ((time() - $last) > self::idleTimeout()) {
            self::logoutClean();
            return true; // da het han
        }
        $_SESSION['last_activity_at'] = time();
        return false;
    }

    /**
     * Dang nhap thanh cong: chong session fixation bang regenerate_id(true),
     * sau do moi luu thong tin user.
     */
    public static function login(array $user): void
    {
        // Quan trong: doi session ID, xoa session cu (true) -> chong fixation.
        session_regenerate_id(true);

        $_SESSION['user_id']          = $user['id'];
        $_SESSION['user_email']       = $user['email'];
        $_SESSION['user_name']        = $user['name'];
        $_SESSION['role']             = $user['role'];
        $_SESSION['login_at']         = time();
        $_SESSION['last_activity_at'] = time();
    }

    /**
     * Logout sach:
     *   1. Xoa toan bo session data.
     *   2. Xoa cookie phien o trinh duyet.
     *   3. Destroy session tren server.
     */
    public static function logoutClean(): void
    {
        // 1. Xoa du lieu trong $_SESSION.
        $_SESSION = [];

        // 2. Xoa cookie phien.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires'  => time() - 42000,
                    'path'     => $params['path'],
                    'domain'   => $params['domain'],
                    'secure'   => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'],
                ]
            );
        }

        // 3. Huy session tren server.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
