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

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $products = searchProducts($conn, $search);
} else {
    $products = getAllProducts($conn);
}

// Success message after add/edit/delete
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Products</h1>
                </div>

                <?php if ($success == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Product added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($success == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Product updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($success == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Product deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search -->
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                        <?php if ($search): ?>
                            <a href="products_manage.php" class="btn btn-outline-secondary">Clear</a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <img src="../assets/images/<?= htmlspecialchars($product['image_path']) ?>" 
                                             alt="Product" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['category']) ?></td>
                                    <td>₱<?= number_format($product['price'], 2) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $product['stock'] > 0 ? 'Available' : 'Out of Stock' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="product_edit.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-warning">Edit</a>
                                        <a href="product_delete.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>