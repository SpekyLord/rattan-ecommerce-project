<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Inputs
$search   = $_GET['search']   ?? '';
$category = $_GET['category'] ?? '';

// Product fetching
if (!empty($search)) {
    $products = searchProducts($conn, $search);

} elseif (!empty($category) && $category !== 'All Categories') {
    $stmt = $conn->prepare("
        SELECT * FROM products 
        WHERE active = 1 AND category = ?
        ORDER BY name
    ");
    $stmt->execute([$category]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    $products = getAllProducts($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Sophee's Home Decors</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container my-5">

    <h1 class="text-center mb-4">All Rattan Products</h1>

    <!-- Search + Category Filters -->
    <div class="row mb-4">

        <div class="col-md-6 mb-3">
            <form method="GET">
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search products..."
                           value="<?= htmlspecialchars($search) ?>">

                    <button class="btn btn-primary" type="submit">Search</button>

                    <?php if ($search): ?>
                        <a href="products.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="col-md-6 mb-3">
            <form method="GET">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="All Categories">All Categories</option>
                    <option value="Storage" <?= $category==='Storage' ? 'selected' : '' ?>>Storage</option>
                    <option value="Kitchen" <?= $category==='Kitchen' ? 'selected' : '' ?>>Kitchen</option>
                    <option value="Home Decor" <?= $category==='Home Decor' ? 'selected' : '' ?>>Home Decor</option>
                    <option value="Furniture" <?= $category==='Furniture' ? 'selected' : '' ?>>Furniture</option>
                </select>
            </form>
        </div>
    </div>

    
    <div class="row">

        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-md-4 mb-4">
                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">

                        <div class="card h-100">

                            <img src="assets/images/products/<?= htmlspecialchars($p['image_path']) ?>"
                                 class="card-img-top"
                                 style="height: 250px; object-fit: cover;"
                                 alt="<?= htmlspecialchars($p['name']) ?>">

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>

                                <p class="card-text fw-bold">
                                    ₱<?= number_format($p['price'], 2) ?>
                                </p>

                                <span class="badge <?= $p['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $p['stock'] > 0 ? 'In Stock' : 'Sold Out' ?>
                                </span>

                                <div>
                                    <small class="text-muted">Category: <?= htmlspecialchars($p['category']) ?></small>
                                </div>
                            </div>
                        </div>

                    </a>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4>No products found</h4>
                    <a href="products.php" class="btn btn-primary mt-2">View All Products</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
