<?php $pageTitle = $pageTitle ?? 'MiniRank'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · MiniRank</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <h1 class="site-title"><a href="/">MiniRank</a></h1>
            <p class="site-tagline">Keyword position tracker</p>
        </div>
    </header>

    <main class="container">
        <nav class="breadcrumb">
            <a href="/">Keywords</a>
            <?php if (!empty($breadcrumb)): ?>
                <span class="crumb-sep">/</span><span><?= e($breadcrumb) ?></span>
            <?php endif; ?>
        </nav>