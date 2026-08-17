<?php

declare(strict_types=1);

/**
 * CSRF protection: one random token per session, embedded in every form
 * (and sent as a header by the AJAX refresh) and verified on every POST.
 */
final class Csrf
{
    /** The session's token (created on first use). */
    public static function token(): string
    {
        Auth::startSession();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /** Hidden input for forms. */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . e(self::token()) . '">';
    }

    /** True when the submitted token matches the session token. */
    public static function verify(string $sent): bool
    {
        Auth::startSession();

        return is_string($sent)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $sent);
    }

    /** Verify $_POST['csrf_token']; abort with 403 when invalid. */
    public static function requireValidPost(): void
    {
        if (!self::verify((string) ($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            echo 'Invalid CSRF token.';
            exit;
        }
    }
}