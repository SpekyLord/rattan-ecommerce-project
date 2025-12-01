<?php

session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "rattan_shop";

require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Database connection
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ---- UPDATE STATUS ----
if (isset($_GET['complete_id'])) {
    $id = intval($_GET['complete_id']);
    $query = "UPDATE orders SET status = 'completed' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: orders_manage.php");
    exit;
}

if (isset($_GET['uncomplete_id'])) {
    $id = intval($_GET['uncomplete_id']);
    $query = "UPDATE orders SET status = 'pending' WHERE id = $id";
    mysqli_query($conn, $query);
    header("Location: orders_manage.php");
    exit;
}

// ---- FILTERING ----
$status_filter = "";
$where = "";

if (isset($_GET['status']) && $_GET['status'] !== "all") {
    $status_filter = mysqli_real_escape_string($conn, $_GET['status']);
    $where = "WHERE orders.status = '$status_filter'";
}

// ---- FETCH ORDERS JOINED WITH PRODUCTS ----
$query = "SELECT orders.*, products.name AS product_name, products.price AS product_price
          FROM orders
          LEFT JOIN products ON orders.product_id = products.id
          $where
          ORDER BY orders.id DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders / Inquiries</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            
            <?php include 'sidebar.php'; ?>

            <!-- MAIN CONTENT -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Customer Orders / Inquiries</h2>
                </div>

                <!-- FILTER -->
                <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                    <label for="status" class="form-label mb-0">Filter by Status:</label>
                    <select name="status" id="status" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="all" <?= $status_filter === "" ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $status_filter=='pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $status_filter=='completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </form>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) { 
                                $total_amount = $row['product_price'] * $row['quantity'];
                                $status_class = $row['status'] == 'pending' ? 'badge bg-warning' : 'badge bg-success';
                            ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['customer_email']) ?></td>
                                <td><?= htmlspecialchars($row['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td>₱<?= number_format($total_amount, 2) ?></td>
                                <td><span class="<?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                                <td>
                                    <?php if ($row['status'] == "pending"): ?>
                                        <a href="orders_manage.php?complete_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Mark Completed</a>
                                    <?php else: ?>
                                        <a href="orders_manage.php?uncomplete_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Mark Uncompleted</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center fst-italic text-muted">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>