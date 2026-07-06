<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$stmt = db()->query('
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.name
');
$products = $stmt->fetchAll();

$page_title = 'Products';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Product List</h2>
        <?php if (is_admin()): ?>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/products/create.php">Add Product</a>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Cost</th>
                    <th>Selling</th>
                    <th>Stock</th>
                    <th>Low Limit</th>
                    <?php if (is_admin()): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="<?= (int) $product['stock_quantity'] <= (int) $product['low_stock_limit'] ? 'low-stock' : '' ?>">
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name'] ?? 'Uncategorized') ?></td>
                    <td>GHS <?= number_format((float) $product['cost_price'], 2) ?></td>
                    <td>GHS <?= number_format((float) $product['selling_price'], 2) ?></td>
                    <td><?= (int) $product['stock_quantity'] ?></td>
                    <td><?= (int) $product['low_stock_limit'] ?></td>
                    <?php if (is_admin()): ?>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>/products/edit.php?id=<?= (int) $product['id'] ?>">Edit</a>
                        <form method="post" action="<?= BASE_URL ?>/products/delete.php" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                            <button type="submit" class="link-danger">Delete</button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <tr><td colspan="7" class="empty">No products found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

