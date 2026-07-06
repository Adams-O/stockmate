<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$products = db()->query('
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.stock_quantity <= p.low_stock_limit
    ORDER BY p.stock_quantity ASC, p.name
')->fetchAll();

$page_title = 'Low-Stock Report';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Low-Stock Products</h2>
        <nav class="report-tabs">
            <a href="<?= BASE_URL ?>/reports/daily.php">Daily</a>
            <a href="<?= BASE_URL ?>/reports/profit.php">Profit</a>
            <a href="<?= BASE_URL ?>/sales/history.php">History</a>
        </nav>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Limit</th><th>Selling Price</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="low-stock">
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= (int) $product['stock_quantity'] ?></td>
                    <td><?= (int) $product['low_stock_limit'] ?></td>
                    <td>GHS <?= number_format((float) $product['selling_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?><tr><td colspan="5" class="empty">No low-stock products.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

