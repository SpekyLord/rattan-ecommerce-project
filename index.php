<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section text-center py-5 bg-light">
    <h1>Authentic Filipino Rattan Products</h1>
    <p class="lead">Handcrafted with tradition, made with pride</p>
</div>

<!-- Featured Products -->
<div class="container my-5">
    <h2 class="text-center mb-4">Featured Products</h2>
    <div class="row" id="featured-products">
        <?php
        require_once 'includes/db_connect.php';
        require_once 'includes/functions.php';
        
        $products = getAllProducts($conn);
        $featured = array_slice($products, 0, 6); // Show first 6
        
        foreach ($featured as $product):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                     class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text">₱<?= number_format($product['price'], 2) ?></p>
                    <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                        <?= $product['stock'] > 0 ? 'In Stock' : 'Sold Out' ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>