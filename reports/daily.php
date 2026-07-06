<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$date = $_GET['date'] ?? date('Y-m-d');
$stmt = db()->prepare('
    SELECT s.*, u.name AS cashier
    FROM sales s
    JOIN users u ON u.id = s.user_id
    WHERE DATE(s.created_at) = ?
    ORDER BY s.created_at DESC
');
$stmt->execute([$date]);
$sales = $stmt->fetchAll();
$total = array_sum(array_column($sales, 'total_amount'));

$page_title = 'Daily Sales Report';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Daily Sales</h2>
        <nav class="report-tabs">
            <a href="<?= BASE_URL ?>/reports/profit.php">Profit</a>
            <a href="<?= BASE_URL ?>/reports/low-stock.php">Low Stock</a>
            <a href="<?= BASE_URL ?>/sales/history.php">History</a>
        </nav>
    </div>
    <form class="filter-bar" method="get">
        <label>Date <input type="date" name="date" value="<?= e($date) ?>"></label>
        <button class="btn btn-secondary" type="submit">Filter</button>
    </form>
    <div class="summary-strip">Total sales: <strong>GHS <?= number_format((float) $total, 2) ?></strong></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Invoice</th><th>Cashier</th><th>Total</th><th>Time</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td><?= e($sale['invoice_no']) ?></td>
                    <td><?= e($sale['cashier']) ?></td>
                    <td>GHS <?= number_format((float) $sale['total_amount'], 2) ?></td>
                    <td><?= e(date('g:i A', strtotime($sale['created_at']))) ?></td>
                    <td><a href="<?= BASE_URL ?>/sales/receipt.php?id=<?= (int) $sale['id'] ?>">Receipt</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$sales): ?><tr><td colspan="5" class="empty">No sales for this date.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

