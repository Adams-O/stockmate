<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$pdo = db();
$products = $pdo->query('
    SELECT id, name, selling_price, stock_quantity
    FROM products
    WHERE stock_quantity > 0
    ORDER BY name
')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productIds = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $items = [];

    foreach ($productIds as $index => $productId) {
        $productId = (int) $productId;
        $quantity = (int) ($quantities[$index] ?? 0);
        if ($productId > 0 && $quantity > 0) {
            $items[] = ['product_id' => $productId, 'quantity' => $quantity];
        }
    }

    if (!$items) {
        set_flash('error', 'Add at least one product to complete a sale.');
        redirect('/sales/create.php');
    }

    $pdo->beginTransaction();
    try {
        $totalAmount = 0.0;
        $profitAmount = 0.0;
        $saleItems = [];

        foreach ($items as $item) {
            $stmt = $pdo->prepare('SELECT id, name, cost_price, selling_price, stock_quantity FROM products WHERE id = ? FOR UPDATE');
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new RuntimeException('One selected product no longer exists.');
            }
            if ((int) $product['stock_quantity'] < $item['quantity']) {
                throw new RuntimeException($product['name'] . ' has only ' . $product['stock_quantity'] . ' item(s) in stock.');
            }

            $lineTotal = (float) $product['selling_price'] * $item['quantity'];
            $lineProfit = ((float) $product['selling_price'] - (float) $product['cost_price']) * $item['quantity'];
            $totalAmount += $lineTotal;
            $profitAmount += $lineProfit;
            $saleItems[] = [
                'product_id' => (int) $product['id'],
                'quantity' => $item['quantity'],
                'unit_price' => (float) $product['selling_price'],
                'cost_price' => (float) $product['cost_price'],
                'line_total' => $lineTotal,
                'line_profit' => $lineProfit,
            ];
        }

        $invoiceNo = 'INV-' . date('YmdHis') . '-' . random_int(100, 999);
        $stmt = $pdo->prepare('INSERT INTO sales (invoice_no, user_id, total_amount, profit_amount) VALUES (?, ?, ?, ?)');
        $stmt->execute([$invoiceNo, current_user()['id'], $totalAmount, $profitAmount]);
        $saleId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('
            INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, cost_price, line_total, line_profit)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stockStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');

        foreach ($saleItems as $item) {
            $itemStmt->execute([
                $saleId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['cost_price'],
                $item['line_total'],
                $item['line_profit'],
            ]);
            $stockStmt->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->commit();
        redirect('/sales/receipt.php?id=' . $saleId);
    } catch (Throwable $e) {
        $pdo->rollBack();
        set_flash('error', $e->getMessage());
        redirect('/sales/create.php');
    }
}

$page_title = 'New Sale';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel pos-panel">
    <form method="post" id="sale-form">
        <div class="pos-layout">
            <div>
                <div class="section-title">
                    <h2>Sale Items</h2>
                    <button class="btn btn-secondary" type="button" id="add-row">Add Item</button>
                </div>
                <div class="table-wrap">
                    <table class="pos-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="sale-items"></tbody>
                    </table>
                </div>
            </div>
            <aside class="checkout-box">
                <span>Total</span>
                <strong id="sale-total">GHS 0.00</strong>
                <button class="btn btn-primary" type="submit">Complete Sale</button>
            </aside>
        </div>
    </form>
</section>

<template id="row-template">
    <tr>
        <td>
            <select name="product_id[]" class="product-select" required>
                <option value="">Choose product</option>
                <?php foreach ($products as $product): ?>
                    <option
                        value="<?= (int) $product['id'] ?>"
                        data-price="<?= e((string) $product['selling_price']) ?>"
                        data-stock="<?= (int) $product['stock_quantity'] ?>">
                        <?= e($product['name']) ?> - GHS <?= number_format((float) $product['selling_price'], 2) ?> (<?= (int) $product['stock_quantity'] ?> left)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><input type="number" name="quantity[]" class="quantity-input" min="1" value="1" required></td>
        <td class="unit-price">GHS 0.00</td>
        <td class="line-total">GHS 0.00</td>
        <td><button type="button" class="link-danger remove-row">Remove</button></td>
    </tr>
</template>
<script src="<?= BASE_URL ?>/assets/js/pos.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

