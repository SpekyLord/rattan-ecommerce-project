<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If product doesn't exist
if (!$product) {
    header('Location: products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Sophee's Home Decors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="product-detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="products.php">Products</a>
        <span>/</span>
        <span><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <div class="product-layout">
        <!-- Image Gallery -->
        <div class="image-gallery">
            <div class="main-image-container">
                <img 
                    id="mainImage"
                    src="assets/images/products/<?= htmlspecialchars($product['image_path']) ?>"
                    class="main-image"
                    alt="<?= htmlspecialchars($product['name']) ?>"
                >
            </div>

            <!-- Thumbnails (if you have multiple images) -->
            <div class="thumbnail-row">
                <img 
                    src="assets/images/products/<?= htmlspecialchars($product['image_path']) ?>" 
                    class="thumbnail active"
                    onclick="changeImage(this)"
                    alt="View 1"
                >
            </div>
        </div>

        <!-- Product Info -->
        <div class="product-info">
            <span class="product-category"><?= htmlspecialchars($product['category']) ?></span>
            
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>

            <?php if ($product['stock'] > 0): ?>
                <div class="stock-badge in-stock">
                    In Stock (<?= $product['stock'] ?> available)
                </div>
            <?php else: ?>
                <div class="stock-badge sold-out">
                    Sold Out
                </div>
            <?php endif; ?>

            <p class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>

            <!-- Quantity Selector -->
            <?php if ($product['stock'] > 0): ?>
            <div class="quantity-selector">
                <span class="quantity-label">Quantity:</span>
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="decrementQty()">−</button>
                    <input type="number" id="quantity" class="qty-input" value="1" min="1" max="<?= $product['stock'] ?>" readonly>
                    <button class="qty-btn" onclick="incrementQty(<?= $product['stock'] ?>)">+</button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button 
                    class="btn-add-cart" 
                    <?= $product['stock'] <= 0 ? 'disabled' : '' ?>
                    onclick="addToCart()"
                >
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                </button>
                <a href="products.php" class="btn-back">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Back to Products
                </a>
            </div>

            <!-- Product Details -->
            <div class="product-details-grid">
                <div class="detail-row">
                    <span class="detail-label">Product ID:</span>
                    <span class="detail-value">#<?= str_pad($product['id'], 4, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Category:</span>
                    <span class="detail-value"><?= htmlspecialchars($product['category']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Availability:</span>
                    <span class="detail-value"><?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

</main>
<?php include 'contact.php'; ?>
<?php include 'includes/footer.php'; ?>

</body>
</html>