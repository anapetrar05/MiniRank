<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

Auth::startSession();

if (Auth::user() !== null) {
    header('Location: /');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::requireValidPost();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } elseif (mb_strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        $users = new UserRepository(Database::connection());

        if ($users->findByEmail($email) !== null) {
            $errors[] = 'That email is already registered.';
        } else {
            $users->create($email, password_hash($password, PASSWORD_DEFAULT));
            $user = $users->findByEmail($email);
            Auth::login($user);
            header('Location: /');
            exit;
        }
    }
}

require __DIR__ . '/../src/views/header.php';
require __DIR__ . '/../src/views/register.php';
require __DIR__ . '/../src/views/footer.php';