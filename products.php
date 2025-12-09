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
<div class="container my-5 products-page">

    <h1 class="text-center mb-4">All Rattan Products</h1>

    <!-- Search + Category Filters -->
    <!-- Search + Category Filters -->
<div class="row mb-4">
  <div class="col-md-6 mb-3">
    <form method="GET" class="custom-search-form">
      <div class="search-bar">
        <input
          type="text"
          name="search"
          class="search-input"
          placeholder="Search products..."
          value="<?= htmlspecialchars($search) ?>">

        <button type="submit" class="search-btn">Search</button>

        <?php if ($search): ?>
          <a href="products.php" class="clear-btn">Clear</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <div class="col-md-6 mb-3">
  <form method="GET" class="custom-category-form" onsubmit="return false;">
  <div class="custom-select-wrapper">
      <div class="custom-select-trigger" id="custom-select-trigger" tabindex="0">
          <span id="selected-text"><?= htmlspecialchars($category ?? 'All Categories') ?></span>
          <svg class="arrow" fill="gray" height="24" width="24" viewBox="0 0 24 24">
              <path d="M7 10l5 5 5-5z"/>
          </svg>
      </div>
      <ul class="custom-options" id="custom-options">
          <li class="option" data-value="All Categories">All Categories</li>
          <li class="option" data-value="Storage">Storage</li>
          <li class="option" data-value="Kitchen">Kitchen</li>
          <li class="option" data-value="Home Decor">Home Decor</li>
          <li class="option" data-value="Furniture">Furniture</li>
      </ul>
      <input type="hidden" name="category" id="category-input" value="<?= htmlspecialchars($category ?? 'All Categories') ?>">
  </div>
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
