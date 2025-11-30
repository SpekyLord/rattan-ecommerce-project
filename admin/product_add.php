<?php
require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    // Validation
    if (empty($name) || empty($price)) {
        $error = "Name and price are required.";
    } else {
        // Image Upload
        $image_path = null;
        if (!empty($_FILES['image']['name'])) {
            $image_name = time() . "_" . basename($_FILES['image']['name']);
            $target = "../assets/images/" . $image_name;
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
            $image_path = $image_name;
        }

        $data = [
            "name" => $name,
            "description" => $desc,
            "price" => $price,
            "stock" => $stock,
            "category" => $category,
            "image_path" => $image_path
        ];

        if (addProduct($conn, $data)) {
            header("Location: products_manage.php");
            exit;
        } else {
            $error = "Failed to add product.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product</title></head>
<body>

<h2>Add Product</h2>

<p style="color:red;"><?= $error ?></p>

<form method="POST" enctype="multipart/form-data">

    Name: <input type="text" name="name"><br><br>
    Description: <textarea name="description"></textarea><br><br>
    Price: <input type="number" step="0.01" name="price"><br><br>
    Stock: <input type="number" name="stock"><br><br>
    Category: <input type="text" name="category"><br><br>
    Image: <input type="file" name="image"><br><br>

    <button type="submit">Add Product</button>

</form>

</body>
</html>
