<?php
require_once __DIR__ . '/auth.php';
$page_title = $page_title ?? APP_NAME;
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php if ($user): ?>
<aside class="sidebar">
    <a class="brand" href="<?= BASE_URL ?>/dashboard.php">StockMate</a>
    <nav>
        <a href="<?= BASE_URL ?>/dashboard.php">Dashboard</a>
        <a href="<?= BASE_URL ?>/sales/create.php">New Sale</a>
        <a href="<?= BASE_URL ?>/products/index.php">Products</a>
        <a href="<?= BASE_URL ?>/categories/index.php">Categories</a>
        <a href="<?= BASE_URL ?>/reports/daily.php">Reports</a>
        <?php if (is_admin()): ?>
            <a href="<?= BASE_URL ?>/users.php">Users</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/logout.php">Logout</a>
    </nav>
</aside>
<main class="main">
    <header class="topbar">
        <button class="menu-toggle" type="button" aria-label="Open menu">Menu</button>
        <div>
            <h1><?= e($page_title) ?></h1>
            <span><?= e(ucfirst($user['role'])) ?>: <?= e($user['name']) ?></span>
        </div>
    </header>
<?php else: ?>
<main class="auth-main">
<?php endif; ?>
<?php if ($flash = get_flash()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
