<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$stmt = db()->prepare('
    SELECT DATE(created_at) AS sale_date, SUM(total_amount) AS sales_total, SUM(profit_amount) AS profit_total, COUNT(*) AS sale_count
    FROM sales
    WHERE DATE(created_at) BETWEEN ? AND ?
    GROUP BY DATE(created_at)
    ORDER BY sale_date DESC
');
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll();
$salesTotal = array_sum(array_column($rows, 'sales_total'));
$profitTotal = array_sum(array_column($rows, 'profit_total'));

$page_title = 'Profit Report';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Profit Report</h2>
        <nav class="report-tabs">
            <a href="<?= BASE_URL ?>/reports/daily.php">Daily</a>
            <a href="<?= BASE_URL ?>/reports/low-stock.php">Low Stock</a>
            <a href="<?= BASE_URL ?>/sales/history.php">History</a>
        </nav>
    </div>
    <form class="filter-bar" method="get">
        <label>From <input type="date" name="from" value="<?= e($from) ?>"></label>
        <label>To <input type="date" name="to" value="<?= e($to) ?>"></label>
        <button class="btn btn-secondary" type="submit">Filter</button>
    </form>
    <div class="summary-strip">
        Sales: <strong>GHS <?= number_format((float) $salesTotal, 2) ?></strong>
        Profit: <strong>GHS <?= number_format((float) $profitTotal, 2) ?></strong>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Sales Count</th><th>Total Sales</th><th>Total Profit</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['sale_date']) ?></td>
                    <td><?= (int) $row['sale_count'] ?></td>
                    <td>GHS <?= number_format((float) $row['sales_total'], 2) ?></td>
                    <td>GHS <?= number_format((float) $row['profit_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="4" class="empty">No sales in this period.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

