<!-- sidebar.php - Replace with this: -->
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                     Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'products_manage.php' ? 'active' : '' ?>" href="products_manage.php">
                     Manage Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'product_add.php' ? 'active' : '' ?>" href="product_add.php">
                     Add New Product
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $current_page == 'orders_manage.php' ? 'active' : '' ?>" href="orders_manage.php">
                     Manage Orders
                </a>
            </li>
        </ul>
    </div>
</nav>