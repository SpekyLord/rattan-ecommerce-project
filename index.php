<?php include 'includes/header.php'; ?>

<!-- Enhanced Hero Section with Parallax -->
<section class="hero">
    <!-- Parallax Background -->
    <div class="hero-bg" id="heroBg"></div>
    
    <!-- Gradient Overlay -->
    <div class="hero-overlay"></div>
    
    <!-- Animated Pattern -->
    <div class="hero-pattern"></div>
    
    <!-- Floating Shapes -->
    <div class="hero-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="hero-content">
        <h1>
            Authentic Filipino Rattan Products
            <span>Handcrafted with tradition, made with pride</span>
        </h1>
        
        <p>
            Discover the beauty of traditional Filipino craftsmanship.<br>
            Each piece tells a story of heritage and artistry.
        </p>
        
        <div class="hero-buttons">
            <a href="products.php" class="hero-btn hero-btn-primary">Shop Collection</a>
            <a href="about.php" class="hero-btn hero-btn-secondary">Our Story</a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<div class="container my-5">
    <h2 class="text-center mb-4">Featured Products</h2>
    <div class="row" id="featured-products">
        <?php
        require_once 'includes/db_connect.php';
        require_once 'includes/functions.php';
        
        $products = getAllProducts($conn);
        $featured = array_slice($products, 0, 3); 
        
        foreach ($featured as $product):
        ?>
        <div class="col-md-4 mb-4">
            <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                <div class="card h-100 shadow-sm">
                    <img src="assets/images/products/<?= htmlspecialchars($product['image_path']) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         style="height: 250px; object-fit: cover;">

                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text h6">₱<?= number_format($product['price'], 2) ?></p>

                        <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                            <?= $product['stock'] > 0 ? 'In Stock' : 'Sold Out' ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</main>
<?php include 'includes/footer.php'; ?>