<?php

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class container>

        <div class="Image-gallery">
            <img id="MainImage">
                src="<? $product [ 'image' ]" ?>"
                class="main-image zoomable">

            <div class="thumbnail=row">
                <?php foreach ($product['gallery'] as $img): ?>
                    <img src="<?= $img ?>" class="thumbnail">
                <? php endforeach; ?>
            </div>                   
        </div>
    
        <div class="product-info">
            <h2><?= $product['name'] ?></h2>
            <p class="price">₱<?= number_format($product['price'], 2) ?></p>

            <?php if( $product['stock'] > 0 ): ?>
                <span class="badge in-stock">In Stock</span>
            <?php else: ?>
                <span class="badge sold-out">Sold Out</span>
            <?php endif; ?>

            <p class="description">
                <?= $product['description'] ?>
            </p>

        </div>

    </div>
</body>
</html>