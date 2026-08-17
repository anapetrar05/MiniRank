<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

Auth::startSession();

// Already logged in? Go to the list.
if (Auth::user() !== null) {
    header('Location: /');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::requireValidPost();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $users = new UserRepository(Database::connection());
    $user = $users->findByEmail($email);

    if ($user === null || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Invalid email or password.';
    } else {
        Auth::login($user);
        header('Location: /');
        exit;
    }
}

require __DIR__ . '/../src/views/header.php';
require __DIR__ . '/../src/views/login.php';
require __DIR__ . '/../src/views/footer.php';