<?php 

?>

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
            <option value="All Categories"></option>
            <option value="Chairs"></option>
            <option value="Tables"></option>
            <option value="Decor"></option>
        </select>
    </div>
</div>

    <div class="product-grid" id="productGrid">
        <?php foreach ($ ass $p): ?>
        <a href="product_detail.php?id=<?php= $p[ 'id' ] ?>" class="product card">

        <img src="<?$p[ 'id' ] ?>" alt="">
        <div class="product-name"><?php $p[ 'id' ] ?>" 

    </div>

<script src= "assets/js/main.js"></script>
</body>
</html>