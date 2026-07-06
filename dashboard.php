<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = db();
$today = date('Y-m-d');

$salesStmt = $pdo->prepare('SELECT COALESCE(SUM(total_amount), 0) AS total, COALESCE(SUM(profit_amount), 0) AS profit FROM sales WHERE DATE(created_at) = ?');
$salesStmt->execute([$today]);
$todayStats = $salesStmt->fetch();

$productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$lowStockCount = (int) $pdo->query('SELECT COUNT(*) FROM products WHERE stock_quantity <= low_stock_limit')->fetchColumn();

$recentStmt = $pdo->query('
    SELECT s.id, s.invoice_no, s.total_amount, s.created_at, u.name AS cashier
    FROM sales s
    JOIN users u ON u.id = s.user_id
    ORDER BY s.created_at DESC
    LIMIT 8
');
$recentSales = $recentStmt->fetchAll();

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>
<section class="stats-grid">
    <article class="stat-card">
        <span>Total sales today</span>
        <strong>GHS <?= number_format((float) $todayStats['total'], 2) ?></strong>
    </article>
    <article class="stat-card">
        <span>Total profit today</span>
        <strong>GHS <?= number_format((float) $todayStats['profit'], 2) ?></strong>
    </article>
    <article class="stat-card">
        <span>Products</span>
        <strong><?= $productCount ?></strong>
    </article>
    <article class="stat-card warning">
        <span>Low-stock products</span>
        <strong><?= $lowStockCount ?></strong>
    </article>
</section>

<section class="panel">
    <div class="section-title">
        <h2>Recent Sales</h2>
        <a class="btn btn-secondary" href="<?= BASE_URL ?>/sales/create.php">New Sale</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Cashier</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentSales as $sale): ?>
                <tr>
                    <td><?= e($sale['invoice_no']) ?></td>
                    <td><?= e($sale['cashier']) ?></td>
                    <td>GHS <?= number_format((float) $sale['total_amount'], 2) ?></td>
                    <td><?= e(date('M j, Y g:i A', strtotime($sale['created_at']))) ?></td>
                    <td><a href="<?= BASE_URL ?>/sales/receipt.php?id=<?= (int) $sale['id'] ?>">Receipt</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recentSales): ?>
                <tr><td colspan="5" class="empty">No sales yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

