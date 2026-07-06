<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/categories/index.php');
}

$pdo = db();
$pdo->beginTransaction();
try {
    $id = (int) ($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE products SET category_id = NULL WHERE category_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    $pdo->commit();
    set_flash('success', 'Category deleted successfully.');
} catch (Throwable $e) {
    $pdo->rollBack();
    set_flash('error', 'Could not delete category.');
}

redirect('/categories/index.php');

