<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/products/index.php');
}

$stmt = db()->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([(int) ($_POST['id'] ?? 0)]);
set_flash('success', 'Product deleted successfully.');
redirect('/products/index.php');

