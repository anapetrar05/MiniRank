<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $pageTitle ?? 'MiniRank';
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title><?= e($pageTitle) ?> · MiniRank</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-row">
            <div class="header-side"></div>
            <div class="site-brand">
                <h1 class="site-title"><a href="/">MiniRank</a></h1>
                <p class="site-tagline">Keyword position tracker</p>
            </div>
            <nav class="user-nav">
                <?php if ($currentUser): ?>
                    <span class="user-email"><?= e($currentUser['email']) ?></span>
                    <form method="post" action="/logout.php" class="inline-form logout-form">
                        <?= Csrf::field() ?>
                        <button type="submit" class="btn-link">Log out</button>
                    </form>
                <?php else: ?>
                    <a class="btn-link" href="/login.php">Log in</a>
                    <a class="btn-link" href="/register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <nav class="breadcrumb">
            <a href="/">Keywords</a>
            <?php if (!empty($breadcrumb)): ?>
                <span class="crumb-sep">/</span><span><?= e($breadcrumb) ?></span>
            <?php endif; ?>
        </nav>