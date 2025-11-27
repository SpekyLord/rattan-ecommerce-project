<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../includes/db_connect.php';

// Get statistics
$stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE active = 1");
$total_products = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE active = 1 AND stock > 0");
$in_stock = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE active = 1 AND stock = 0");
$out_of_stock = $stmt->fetch()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$pending_orders = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rattan Crafts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">🇵🇭 Rattan Crafts Admin</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>!
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                📊 Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products_manage.php">
                                📦 Manage Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="product_add.php">
                                ➕ Add New Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders_manage.php">
                                🛒 Manage Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php" target="_blank">
                                🌐 View Website
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <p class="card-text display-4"><?= $total_products ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">In Stock</h5>
                                <p class="card-text display-4"><?= $in_stock ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5 class="card-title">Out of Stock</h5>
                                <p class="card-text display-4"><?= $out_of_stock ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Pending Orders</h5>
                                <p class="card-text display-4"><?= $pending_orders ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Products -->
                <h3 class="mb-3">Recent Products</h3>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT * FROM products WHERE active = 1 ORDER BY created_at DESC LIMIT 5");
                            while ($product = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>₱<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td><?= htmlspecialchars($product['category']) ?></td>
                                <td>
                                    <span class="badge <?= $product['stock'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $product['stock'] > 0 ? 'Available' : 'Out of Stock' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>