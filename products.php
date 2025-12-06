<!DOCTYPE html>
<html lang="en">
<head> 
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="Products">
    <div class="search-filter-container">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search products...">
</div>

    <div class="category-filter">
        <select id="categorySelect">
            <option value="All Categories"></option>t add 
            <option value="Chairs"></option>
            <option value="Tables"></option>
            <option value="Decor"></option>
        </select>
    </div>
</div>

    <div class="product-grid" id="productGrid">
        <?php foreach ($products as $p): ?>
        <a href="product_detail.php?id=<?php= $p[ 'id' ] ?>" class="product card">

        <img src="<?$p[ 'id' ] ?>" alt="">
        <div class="product-name"><?php $p[ 'id' ] ?>" 

        <?php if( $product['stock'] > 0 ): ?>
            <span class="badge in-stock">In Stock</span>
        <?php else: ?>
            <span class="badge sold-out">Sold Out</span>
        <?php endif; ?>
        </a>
        <?php endforeach; ?>

    </div>
</div>

<script src= "assets/js/main.js"></script>
</body>
<?php include 'includes/footer.php'; ?> 
</html>