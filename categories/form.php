<section class="panel narrow">
    <form method="post" class="form">
        <label>Category Name
            <input type="text" name="name" value="<?= e($category['name'] ?? '') ?>" required>
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save Category</button>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/categories/index.php">Cancel</a>
        </div>
    </form>
</section>

