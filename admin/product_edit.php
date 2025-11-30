<?php
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$id = $_GET['id'];
$product = getProductById($conn, $id);

if (!$product) {
    die("Product not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $image_path = $product['image_path'];

    // If uploading a NEW image
    if (!empty($_FILES['image']['name'])) {
        $new_image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $new_image);
        $image_path = $new_image;
    }

    $data = [
        "name" => $name,
        "description" => $desc,
        "price" => $price,
        "stock" => $stock,
        "category" => $category,
        "image_path" => $image_path
    ];

    if (updateProduct($conn, $id, $data)) {
        header("Location: products_manage.php");
        exit;
    } else {
        $error = "Error updating product.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product</title></head>
<body>

<h2>Edit Product</h2>

<p style="color:red;"><?= $error ?></p>

<form method="POST" enctype="multipart/form-data">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br><br>
    Description: <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>"><br><br>
    Stock: <input type="number" name="stock" value="<?= $product['stock'] ?>"><br><br>
    Category: <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>"><br><br>

    Current Image:<br>
    <?php if ($product['image_path']): ?>
        <img src="../assets/images/<?= $product['image_path'] ?>" width="120"><br><br>
    <?php endif; ?>

    Upload New Image: <input type="file" name="image"><br><br>

    <button type="submit">Save Changes</button>
</form>

</body>
</html>
