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

// Handle AJAX requests for edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_product') {
    $id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    
    $product = getProductById($conn, $id);
    $image_path = $product['image_path'];
    
    // Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $new_image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/products/" . $new_image);
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
        header("Location: products_manage.php?success=updated");
        exit;
    }
}

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
    <style>
        .modal-backdrop.show {
            opacity: 0.7;
        }
        
        /* Custom Delete Modal Styling */
        .delete-modal .modal-content {
            border: 2px solid #dc3545;
        }
        
        .delete-modal .modal-header {
            background-color: #dc3545;
            color: white;
        }
        
        .delete-modal .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        
        .delete-modal .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
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
                        ✓ Product added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($success == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        ✓ Product updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php elseif ($success == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        ✓ Product deleted successfully!
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
                                        <img src="../assets/images/products/<?= htmlspecialchars($product['image_path']) ?>" 
                                            alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
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
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="openEditModal(<?= htmlspecialchars(json_encode($product)) ?>)">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="openDeleteModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>')">
                                            Delete
                                        </button>
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

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProductForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_product">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Product Name *</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <select name="category" id="edit_category" class="form-select">
                                    <option value="Storage">Storage</option>
                                    <option value="Kitchen">Kitchen</option>
                                    <option value="Home Decor">Home Decor</option>
                                    <option value="Furniture">Furniture</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Price (₱) *</label>
                                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock" id="edit_stock" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img id="edit_current_image" src="" alt="Current product" 
                                     style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload New Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade delete-modal" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">⚠️ Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete this product?</p>
                    <p class="fw-bold text-danger mb-0" id="delete_product_name"></p>
                    <p class="text-muted small mt-2">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirm_delete_btn" class="btn btn-danger">Delete Product</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Open Edit Modal
        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description || '';
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock;
            document.getElementById('edit_category').value = product.category;
            document.getElementById('edit_current_image').src = '../assets/images/products/' + product.image_path;
            
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        }

        // Open Delete Modal
        function openDeleteModal(productId, productName) {
            document.getElementById('delete_product_name').textContent = productName;
            document.getElementById('confirm_delete_btn').href = 'product_delete.php?id=' + productId;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
            modal.show();
        }
    </script>
</body>
</html>