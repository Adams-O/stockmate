<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$saleId = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('
    SELECT s.*, u.name AS cashier
    FROM sales s
    JOIN users u ON u.id = s.user_id
    WHERE s.id = ?
');
$stmt->execute([$saleId]);
$sale = $stmt->fetch();

if (!$sale) {
    set_flash('error', 'Sale not found.');
    redirect('/dashboard.php');
}

$itemsStmt = db()->prepare('
    SELECT si.*, p.name
    FROM sale_items si
    JOIN products p ON p.id = si.product_id
    WHERE si.sale_id = ?
');
$itemsStmt->execute([$saleId]);
$items = $itemsStmt->fetchAll();

$page_title = 'Receipt';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel receipt">
    <div class="receipt-head">
        <div>
            <h2>StockMate POS</h2>
            <p>Invoice: <?= e($sale['invoice_no']) ?></p>
            <p>Cashier: <?= e($sale['cashier']) ?></p>
            <p>Date: <?= e(date('M j, Y g:i A', strtotime($sale['created_at']))) ?></p>
        </div>
        <button class="btn btn-secondary no-print" onclick="window.print()">Print</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td>GHS <?= number_format((float) $item['unit_price'], 2) ?></td>
                    <td>GHS <?= number_format((float) $item['line_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Grand Total</th>
                    <th>GHS <?= number_format((float) $sale['total_amount'], 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="form-actions no-print">
        <a class="btn btn-primary" href="<?= BASE_URL ?>/sales/create.php">New Sale</a>
        <a class="btn btn-secondary" href="<?= BASE_URL ?>/dashboard.php">Dashboard</a>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

