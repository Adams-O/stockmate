<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    set_flash('error', 'Category not found.');
    redirect('/categories/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('UPDATE categories SET name = ? WHERE id = ?');
    $stmt->execute([trim($_POST['name']), $id]);
    set_flash('success', 'Category updated successfully.');
    redirect('/categories/index.php');
}

$page_title = 'Edit Category';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/form.php';
require_once __DIR__ . '/../includes/footer.php';

