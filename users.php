<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] === 'admin' ? 'admin' : 'cashier';

    if ($name === '' || $username === '' || strlen($password) < 6) {
        set_flash('error', 'Name, username, and a password of at least 6 characters are required.');
        redirect('/users.php');
    }

    try {
        $stmt = db()->prepare('INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), $role]);
        set_flash('success', 'User created successfully.');
    } catch (PDOException $e) {
        set_flash('error', 'Could not create user. The username may already exist.');
    }
    redirect('/users.php');
}

$users = db()->query('SELECT id, name, username, role, created_at FROM users ORDER BY name')->fetchAll();

$page_title = 'Users';
require_once __DIR__ . '/includes/header.php';
?>
<section class="panel narrow">
    <div class="section-title">
        <h2>Add User</h2>
    </div>
    <form method="post" class="form grid-form">
        <label>Name
            <input type="text" name="name" required>
        </label>
        <label>Username
            <input type="text" name="username" required>
        </label>
        <label>Password
            <input type="password" name="password" minlength="6" required>
        </label>
        <label>Role
            <select name="role" required>
                <option value="cashier">Cashier</option>
                <option value="admin">Admin</option>
            </select>
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Create User</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="section-title">
        <h2>Staff Accounts</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Created</th></tr></thead>
            <tbody>
            <?php foreach ($users as $userRow): ?>
                <tr>
                    <td><?= e($userRow['name']) ?></td>
                    <td><?= e($userRow['username']) ?></td>
                    <td><?= e(ucfirst($userRow['role'])) ?></td>
                    <td><?= e(date('M j, Y', strtotime($userRow['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

