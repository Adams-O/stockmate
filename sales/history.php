<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$sales = db()->query('
    SELECT s.*, u.name AS cashier
    FROM sales s
    JOIN users u ON u.id = s.user_id
    ORDER BY s.created_at DESC
    LIMIT 200
')->fetchAll();

$page_title = 'Sales History';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Sales History</h2>
        <a class="btn btn-secondary" href="<?= BASE_URL ?>/reports/daily.php">Daily Report</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Cashier</th>
                    <th>Total</th>
                    <th>Profit</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= e($sale['invoice_no']) ?></td>
                    <td><?= e($sale['cashier']) ?></td>
                    <td>GHS <?= number_format((float) $sale['total_amount'], 2) ?></td>
                    <td>GHS <?= number_format((float) $sale['profit_amount'], 2) ?></td>
                    <td><?= e(date('M j, Y g:i A', strtotime($sale['created_at']))) ?></td>
                    <td><a href="<?= BASE_URL ?>/sales/receipt.php?id=<?= (int) $sale['id'] ?>">Receipt</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$sales): ?>
                <tr><td colspan="6" class="empty">No sales found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

