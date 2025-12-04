<?php

session_start();

// Check if logged in
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


// ---- UPDATE STATUS ----
if (isset($_GET['complete_id'])) {
    $id = intval($_GET['complete_id']);
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: orders_manage.php");
    exit;
}

if (isset($_GET['uncomplete_id'])) {
    $id = intval($_GET['uncomplete_id']);
    $stmt = $conn->prepare("UPDATE orders SET status = 'pending' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: orders_manage.php");
    exit;
}

// ---- FILTERING ----
$status_filter = "";
$where = "";
$params = [];

if (isset($_GET['status']) && $_GET['status'] !== "all") {
    $status_filter = $_GET['status'];
    $where = "WHERE orders.status = ?";
    $params[] = $status_filter;
}

// ---- FETCH ORDERS WITH ITEMS ----
$query = "SELECT 
            orders.id,
            orders.customer_name,
            orders.customer_email,
            orders.customer_phone,
            orders.status,
            orders.created_at,
            GROUP_CONCAT(products.name SEPARATOR ', ') AS product_names,
            SUM(order_items.quantity) AS total_quantity,
            SUM(order_items.quantity * order_items.price_at_time) AS total_amount
          FROM orders
          LEFT JOIN order_items ON orders.id = order_items.order_id
          LEFT JOIN products ON order_items.product_id = products.id
          $where
          GROUP BY orders.id
          ORDER BY orders.created_at DESC, orders.id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$result = $stmt->fetchAll();
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
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Products</th>
                                <th>Total Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(count($result) > 0): ?>
                            <?php foreach ($result as $row): 
                                $status_class = $row['status'] == 'pending' ? 'badge bg-warning text-dark' : 'badge bg-success';
                                $date_formatted = date('M d, Y', strtotime($row['created_at']));
                            ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $date_formatted ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['customer_email']) ?></td>
                                <td><?= htmlspecialchars($row['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($row['product_names'] ?? 'No products') ?></td>
                                <td><?= $row['total_quantity'] ?? 0 ?></td>
                                <td>₱<?= number_format($row['total_amount'] ?? 0, 2) ?></td>
                                <td><span class="<?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                                <td>
                                    <?php if ($row['status'] == "pending"): ?>
                                        <a href="orders_manage.php?complete_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Mark Complete</a>
                                    <?php else: ?>
                                        <a href="orders_manage.php?uncomplete_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Mark Pending</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center fst-italic text-muted">No orders found.</td>
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