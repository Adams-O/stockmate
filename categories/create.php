<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$category = ['name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO categories (name) VALUES (?)');
    $stmt->execute([trim($_POST['name'])]);
    set_flash('success', 'Category added successfully.');
    redirect('/categories/index.php');
}

$page_title = 'Add Category';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/form.php';
require_once __DIR__ . '/../includes/footer.php';

