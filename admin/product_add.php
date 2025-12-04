<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    if (empty($name) || empty($price)) {
        $error = "Name and price are required.";
    } else {
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
            header("Location: products_manage.php?success=added");
            exit;
        } else {
            $error = "Failed to add product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Add New Product</h1>
                    <a href="products_manage.php" class="btn btn-secondary">← Back to Products</a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price (₱) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control" value="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="Storage">Storage</option>
                                        <option value="Kitchen">Kitchen</option>
                                        <option value="Home Decor">Home Decor</option>
                                        <option value="Furniture">Furniture</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>