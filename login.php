<?php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, name, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        redirect('/dashboard.php');
    }

    $error = 'Invalid username or password.';
}

$page_title = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<section class="login-card">
    <h1>StockMate POS</h1>
    <p>Sign in to manage sales and inventory.</p>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post" class="form">
        <label>Username
            <input type="text" name="username" required autofocus>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button class="btn btn-primary" type="submit">Login</button>
    </form>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

