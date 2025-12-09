<?php
include 'includes/header.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    die("Product ID not found.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// If product doesn't exist
if (!$product) {
    die("Product not found.");
}

// Convert gallery JSON to array (if you stored gallery as JSON)
$product['gallery'] = !empty($product['gallery']) 
    ? json_decode($product['gallery'], true) 
    : [];
?>


<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container">

    <div class="image-gallery">

        <!-- Main Image -->
        <img 
            id="MainImage"
            src="assets/images/products/<?= htmlspecialchars($product['image_path']) ?>"
            class="main-image zoomable"
        >

        <!-- Thumbnails -->
        <div class="thumbnail-row">
            <?php foreach ($product['gallery'] as $img): ?>
                <img src="assets/images/products/<?= htmlspecialchars($img) ?>" 
                     class="thumbnail">
            <?php endforeach; ?>
        </div>
    </div>

    <div class="product-info">
        <h2><?= htmlspecialchars($product['name']) ?></h2>

        <p class="price">₱<?= number_format($product['price'], 2) ?></p>

        <?php if ($product['stock'] > 0): ?>
            <span class="badge in-stock">In Stock</span>
        <?php else: ?>
            <span class="badge sold-out">Sold Out</span>
        <?php endif; ?>

        <p class="description">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </p>
    </div>

</div>

</body>
</html>
