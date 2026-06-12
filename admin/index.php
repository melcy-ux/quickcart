<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$totalProducts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$totalOrders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$totalUsers    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
$totalRevenue  = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status='completed'"))[0];

$recentOrders = mysqli_query($conn,
    "SELECT o.id, o.full_name, o.total_amount, o.status, o.created_at
     FROM orders o ORDER BY o.created_at DESC LIMIT 5");
?>

<div class="container-fluid my-4">
  <h2 class="fw-bold mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card admin-stat-card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Total Products</h6>
          <h3 class="fw-bold text-primary"><?php echo $totalProducts; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card admin-stat-card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Total Orders</h6>
          <h3 class="fw-bold text-success"><?php echo $totalOrders; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card admin-stat-card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Registered Users</h6>
          <h3 class="fw-bold text-info"><?php echo $totalUsers; ?></h3>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card admin-stat-card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted">Completed Revenue</h6>
          <h3 class="fw-bold text-warning">KES <?php echo number_format($totalRevenue, 2); ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span class="fw-semibold">Recent Orders</span>
        </div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead class="table-dark">
              <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($order = mysqli_fetch_assoc($recentOrders)): ?>
              <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                <td>KES <?php echo number_format($order['total_amount'], 2); ?></td>
                <td>
                  <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                    <?php echo ucfirst($order['status']); ?>
                  </span>
                </td>
                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Quick Links</div>
        <div class="list-group list-group-flush">
          <a href="products.php" class="list-group-item list-group-item-action">
            <i class="bi bi-box-seam"></i> Manage Products
          </a>
          <a href="../products.php" class="list-group-item list-group-item-action">
            <i class="bi bi-shop"></i> View Storefront
          </a>
          <a href="../logout.php" class="list-group-item list-group-item-action text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
