<?php $pageTitle = 'Log in'; ?>

<section class="card auth-card">
    <h2>Log in</h2>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="auth-form">
        <?= Csrf::field() ?>
        <label>
            Email
            <input type="email" name="email" value="<?= e($email ?? '') ?>"
                   required autocomplete="email">
        </label>
        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button type="submit">Log in</button>
    </form>

    <p class="auth-switch">No account? <a href="/register.php">Register</a></p>
</section>