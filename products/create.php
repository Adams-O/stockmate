<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('
        INSERT INTO products (category_id, name, cost_price, selling_price, stock_quantity, low_stock_limit)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $_POST['category_id'] ?: null,
        trim($_POST['name']),
        (float) $_POST['cost_price'],
        (float) $_POST['selling_price'],
        (int) $_POST['stock_quantity'],
        (int) $_POST['low_stock_limit'],
    ]);
    set_flash('success', 'Product added successfully.');
    redirect('/products/index.php');
}

$page_title = 'Add Product';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/form.php';
require_once __DIR__ . '/../includes/footer.php';

