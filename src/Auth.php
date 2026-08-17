<?php

declare(strict_types=1);

/**
 * Session-based authentication.
 */
final class Auth
{
    /** Start the session with hardened cookie settings (if not already). */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    /** Logged-in user as ['id' => int, 'email' => string], or null. */
    public static function user(): ?array
    {
        self::startSession();

        $user = $_SESSION['user'] ?? null;

        return is_array($user) && isset($user['id'], $user['email']) ? $user : null;
    }

    /** Store the user in the session (with a fresh session id). */
    public static function login(array $user): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'email' => $user['email'],
        ];
    }

    /** Destroy the session and expire its cookie. */
    public static function logout(): void
    {
        self::startSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /** Redirect unauthenticated visitors to the login page. */
    public static function requireLogin(): void
    {
        if (self::user() === null) {
            header('Location: /login.php');
            exit;
        }
    }
}