<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Session timeout after 15 minutes of inactivity
$expireAfter = 15 * 60;

if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $expireAfter)
{
    session_unset();
    session_destroy();
    header("Location: login.php?message=Session expired. Please log in again.");
    exit;
}

$_SESSION['last_activity'] = time();

require_once "../includes/db_connect.php";
require_once "../includes/functions.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    // Basic validation
    if (empty($name) || empty($price)) {
        $error = "Name and price are required.";
    } 
    // Validate price
    elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    }
    // Validate stock
    elseif (!is_numeric($stock) || $stock < 0) {
        $error = "Stock must be a non-negative number.";
    }
    // All basic validations passed
    else {
        $image_path = null;
        
        // Handle file upload if provided
        if (!empty($_FILES['image']['name'])) {
            // Check for upload errors
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = "File upload failed. Please try again.";
            } 
            // Validate file type
            else {
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                
                if (!in_array($fileType, $allowed)) {
                    $error = "Invalid file type. Only JPG, PNG, GIF, and WebP images allowed.";
                }
                // Validate file size (max 5MB)
                elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $error = "File too large. Maximum size is 5MB.";
                }
                // Validate extension matches MIME type
                else {
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    
                    if (!in_array($ext, $allowedExtensions)) {
                        $error = "Invalid file extension.";
                    }
                    // All file validations passed
                    else {
                        // Generate secure filename
                        $image_name = uniqid() . '_' . time() . '.' . $ext;
                        $target = "../assets/images/products/" . $image_name;
                        
                        // Ensure directory exists
                        if (!is_dir("../assets/images/products/")) {
                            mkdir("../assets/images/products/", 0755, true);
                        }
                        
                        // Move uploaded file
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                            $image_path = $image_name;
                        } else {
                            $error = "Failed to save uploaded file.";
                        }
                    }
                }
            }
        }

        // ✅ ONLY add product if no errors occurred
        if (empty($error)) {
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
                $error = "Failed to add product to database.";
            }
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
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Price (₱) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" 
                                           value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>" 
                                           min="0.01" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control" 
                                           value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '0' ?>" 
                                           min="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="Storage" <?= (isset($_POST['category']) && $_POST['category'] == 'Storage') ? 'selected' : '' ?>>Storage</option>
                                        <option value="Kitchen" <?= (isset($_POST['category']) && $_POST['category'] == 'Kitchen') ? 'selected' : '' ?>>Kitchen</option>
                                        <option value="Home Decor" <?= (isset($_POST['category']) && $_POST['category'] == 'Home Decor') ? 'selected' : '' ?>>Home Decor</option>
                                        <option value="Furniture" <?= (isset($_POST['category']) && $_POST['category'] == 'Furniture') ? 'selected' : '' ?>>Furniture</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
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