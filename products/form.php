<?php $isEdit = isset($product); ?>
<section class="panel narrow">
    <form method="post" class="form grid-form">
        <label>Name
            <input type="text" name="name" value="<?= e($product['name'] ?? '') ?>" required>
        </label>
        <label>Category
            <select name="category_id">
                <option value="">Uncategorized</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Cost Price
            <input type="number" name="cost_price" min="0" step="0.01" value="<?= e((string) ($product['cost_price'] ?? '')) ?>" required>
        </label>
        <label>Selling Price
            <input type="number" name="selling_price" min="0" step="0.01" value="<?= e((string) ($product['selling_price'] ?? '')) ?>" required>
        </label>
        <label>Stock Quantity
            <input type="number" name="stock_quantity" min="0" value="<?= e((string) ($product['stock_quantity'] ?? '')) ?>" required>
        </label>
        <label>Low-Stock Limit
            <input type="number" name="low_stock_limit" min="0" value="<?= e((string) ($product['low_stock_limit'] ?? '5')) ?>" required>
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Update Product' : 'Save Product' ?></button>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/products/index.php">Cancel</a>
        </div>
    </form>
</section>

