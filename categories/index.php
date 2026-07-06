<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$categories = db()->query('
    SELECT c.*, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id
    ORDER BY c.name
')->fetchAll();

$page_title = 'Categories';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="panel">
    <div class="section-title">
        <h2>Categories</h2>
        <?php if (is_admin()): ?>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/categories/create.php">Add Category</a>
        <?php endif; ?>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Products</th>
                    <?php if (is_admin()): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= e($category['name']) ?></td>
                    <td><?= (int) $category['product_count'] ?></td>
                    <?php if (is_admin()): ?>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>/categories/edit.php?id=<?= (int) $category['id'] ?>">Edit</a>
                        <form method="post" action="<?= BASE_URL ?>/categories/delete.php" onsubmit="return confirm('Delete this category? Products will become uncategorized.');">
                            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                            <button type="submit" class="link-danger">Delete</button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (!$categories): ?>
                <tr><td colspan="3" class="empty">No categories found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

