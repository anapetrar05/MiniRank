<?php $pageTitle = 'Register'; ?>

<section class="card auth-card">
    <h2>Create an account</h2>

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
            <input type="email" name="email" value="<?= e($email) ?>"
                   required autocomplete="email">
        </label>
        <label>
            Password (at least 8 characters)
            <input type="password" name="password" required minlength="8"
                   autocomplete="new-password">
        </label>
        <label>
            Confirm password
            <input type="password" name="password_confirm" required minlength="8"
                   autocomplete="new-password">
        </label>
        <button type="submit">Register</button>
    </form>

    <p class="auth-switch">Already have an account? <a href="/login.php">Log in</a></p>
</section>