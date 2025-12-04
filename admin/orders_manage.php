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

// ---- FETCH ORDERS ----
$query = "SELECT 
            orders.id,
            orders.customer_name,
            orders.customer_email,
            orders.customer_phone,
            orders.status,
            orders.created_at
          FROM orders
          $where
          ORDER BY orders.created_at DESC, orders.id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders / Inquiries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-items-list {
            font-size: 0.9rem;
            margin: 0;
            padding-left: 1.2rem;
        }
        .order-items-list li {
            margin-bottom: 0.3rem;
        }
    </style>
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

                <!-- ORDERS LIST -->
                <div class="accordion" id="ordersAccordion">
                    <?php if(count($orders) > 0): ?>
                        <?php foreach ($orders as $order): 
                            $status_class = $order['status'] == 'pending' ? 'badge bg-warning text-dark' : 'badge bg-success';
                            $date_formatted = date('M d, Y g:i A', strtotime($order['created_at']));
                            
                            // Fetch order items for this order
                            $stmt_items = $conn->prepare("
                                SELECT 
                                    order_items.quantity,
                                    order_items.price_at_time,
                                    products.name,
                                    products.image_path
                                FROM order_items
                                LEFT JOIN products ON order_items.product_id = products.id
                                WHERE order_items.order_id = ?
                            ");
                            $stmt_items->execute([$order['id']]);
                            $items = $stmt_items->fetchAll();
                            
                            // Calculate totals
                            $total_items = 0;
                            $total_amount = 0;
                            foreach ($items as $item) {
                                $total_items += $item['quantity'];
                                $total_amount += $item['quantity'] * $item['price_at_time'];
                            }
                        ?>
                        
                        <!-- Order Card -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#order<?= $order['id'] ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <strong>Order #<?= $order['id'] ?></strong> - 
                                            <?= htmlspecialchars($order['customer_name']) ?>
                                        </div>
                                        <div class="d-flex gap-3 align-items-center">
                                            <span class="<?= $status_class ?>"><?= ucfirst($order['status']) ?></span>
                                            <span class="text-muted"><?= $date_formatted ?></span>
                                            <strong class="text-primary">₱<?= number_format($total_amount, 2) ?></strong>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="order<?= $order['id'] ?>" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <div class="row">
                                        <!-- Customer Info -->
                                        <div class="col-md-4">
                                            <h5>Customer Information</h5>
                                            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                                            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                                            <p class="mb-1"><strong>Date:</strong> <?= $date_formatted ?></p>
                                        </div>

                                        <!-- Order Items -->
                                        <div class="col-md-8">
                                            <h5>Order Items (<?= $total_items ?> total items)</h5>
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price Each</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($items as $item): 
                                                        $subtotal = $item['quantity'] * $item['price_at_time'];
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <img src="../assets/images/<?= htmlspecialchars($item['image_path']) ?>" 
                                                                     alt="Product" 
                                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                                <?= htmlspecialchars($item['name']) ?>
                                                            </div>
                                                        </td>
                                                        <td><strong>×<?= $item['quantity'] ?></strong></td>
                                                        <td>₱<?= number_format($item['price_at_time'], 2) ?></td>
                                                        <td><strong>₱<?= number_format($subtotal, 2) ?></strong></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <tr class="table-active">
                                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                                        <td><strong class="text-primary fs-5">₱<?= number_format($total_amount, 2) ?></strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!-- Actions -->
                                            <div class="mt-3">
                                                <?php if ($order['status'] == "pending"): ?>
                                                    <a href="orders_manage.php?complete_id=<?= $order['id'] ?>" 
                                                       class="btn btn-success"
                                                       onclick="return confirm('Mark this order as completed?')">
                                                        ✓ Mark as Completed
                                                    </a>
                                                <?php else: ?>
                                                    <a href="orders_manage.php?uncomplete_id=<?= $order['id'] ?>" 
                                                       class="btn btn-warning"
                                                       onclick="return confirm('Mark this order as pending?')">
                                                        ← Mark as Pending
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <p class="mb-0">No orders found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>