<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$sql = "SELECT c.quantity, p.id AS product_id, p.name, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $userId";
$result = mysqli_query($conn, $sql);
$items  = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');

    if ($name === '' || $address === '' || $phone === '') {
        $error = 'Please fill in all required fields.';
    } else {
        $name_s    = mysqli_real_escape_string($conn, $name);
        $address_s = mysqli_real_escape_string($conn, $address);
        $phone_s   = mysqli_real_escape_string($conn, $phone);

        mysqli_query($conn, "START TRANSACTION");

        $orderSql = "INSERT INTO orders (user_id, full_name, address, phone, total_amount, status)
                     VALUES ($userId, '$name_s', '$address_s', '$phone_s', $total, 'pending')";
        mysqli_query($conn, $orderSql);
        $orderId = mysqli_insert_id($conn);

        $ok = true;
        foreach ($items as $item) {
            $pid = (int)$item['product_id'];
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];
            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                        VALUES ($orderId, $pid, $qty, $price)";
            if (!mysqli_query($conn, $itemSql)) { $ok = false; break; }
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid");
        }

        if ($ok) {
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $userId");
            mysqli_query($conn, "COMMIT");
            $success = true;
        } else {
            mysqli_query($conn, "ROLLBACK");
            $error = 'Order could not be completed. Please try again.';
        }
    }
}
?>

<div class="container my-5">
  <h2 class="fw-bold mb-4">Checkout</h2>

  <?php if ($success): ?>
    <div class="alert alert-success text-center py-4">
      <h4><i class="bi bi-check-circle-fill"></i> Order Placed Successfully!</h4>
      <p>Thank you for shopping with QuickCart. Your order is being processed.</p>
      <a href="index.php" class="btn btn-primary mt-2">Back to Home</a>
    </div>
  <?php else: ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold bg-primary text-white">Delivery Details</div>
        <div class="card-body p-4">
          <form method="POST" action="checkout.php">
            <div class="mb-3">
              <label class="form-label">Full Name *</label>
              <input type="text" name="full_name" class="form-control" required
                     value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Delivery Address *</label>
              <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number *</label>
              <input type="tel" name="phone" class="form-control" required
                     value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">Place Order</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Order Summary</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <?php foreach ($items as $item): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?php echo htmlspecialchars($item['name']); ?> &times; <?php echo $item['quantity']; ?></span>
              <span>KES <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between fw-bold">
              <span>Total</span>
              <span>KES <?php echo number_format($total, 2); ?></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
