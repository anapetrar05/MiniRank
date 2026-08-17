<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

// Logout is a state change, so only accept POST (with a valid CSRF token).
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed.';
    exit;
}

Csrf::requireValidPost();
Auth::logout();

header('Location: /login.php');
exit;