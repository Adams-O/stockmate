<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found.');
    redirect('/products/index.php');
}

$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('
        UPDATE products
        SET category_id = ?, name = ?, cost_price = ?, selling_price = ?, stock_quantity = ?, low_stock_limit = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $_POST['category_id'] ?: null,
        trim($_POST['name']),
        (float) $_POST['cost_price'],
        (float) $_POST['selling_price'],
        (int) $_POST['stock_quantity'],
        (int) $_POST['low_stock_limit'],
        $id,
    ]);
    set_flash('success', 'Product updated successfully.');
    redirect('/products/index.php');
}

$page_title = 'Edit Product';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/form.php';
require_once __DIR__ . '/../includes/footer.php';

