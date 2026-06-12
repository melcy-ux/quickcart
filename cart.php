<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $qty       = (int)($_POST["qty_$productId"] ?? 1);
    if ($qty < 1) $qty = 1;

    $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id = $userId AND product_id = $productId");
    if (mysqli_num_rows($check) > 0) {
        $row    = mysqli_fetch_assoc($check);
        $newQty = $row['quantity'] + $qty;
        mysqli_query($conn, "UPDATE cart SET quantity = $newQty WHERE id = {$row['id']}");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($userId, $productId, $qty)");
    }
    header('Location: cart.php');
    exit;
}

// Handle remove
if (isset($_GET['remove'])) {
    $cartId = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id = $cartId AND user_id = $userId");
    header('Location: cart.php');
    exit;
}

// Handle update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    foreach ($_POST as $key => $val) {
        if (strpos($key, 'qty_') === 0) {
            $cartId = (int)substr($key, 4);
            $qty    = max(1, (int)$val);
            mysqli_query($conn, "UPDATE cart SET quantity = $qty WHERE id = $cartId AND user_id = $userId");
        }
    }
    header('Location: cart.php');
    exit;
}

// Fetch cart items
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $userId";
$result = mysqli_query($conn, $sql);
$items  = mysqli_fetch_all($result, MYSQLI_ASSOC);

$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
?>

<div class="container my-5">
  <h2 class="fw-bold mb-4"><i class="bi bi-cart3"></i> Your Cart</h2>

  <?php if (empty($items)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="products.php">Continue shopping</a></div>
  <?php else: ?>

  <form method="POST" action="cart.php">
    <input type="hidden" name="action" value="update">
    <div class="table-responsive">
      <table class="table cart-table align-middle">
        <thead class="table-dark">
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="<?php echo htmlspecialchars($item['image'] ?: 'assets/images/placeholder.jpg'); ?>"
                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                <span><?php echo htmlspecialchars($item['name']); ?></span>
              </div>
            </td>
            <td>KES <?php echo number_format($item['price'], 2); ?></td>
            <td style="width:140px;">
              <div class="input-group input-group-sm">
                <button type="button" class="btn btn-outline-secondary qty-btn"
                        data-id="<?php echo $item['cart_id']; ?>" data-action="dec">-</button>
                <input type="number" class="form-control text-center"
                       name="qty_<?php echo $item['cart_id']; ?>"
                       value="<?php echo $item['quantity']; ?>"
                       min="1" max="<?php echo $item['stock']; ?>">
                <button type="button" class="btn btn-outline-secondary qty-btn"
                        data-id="<?php echo $item['cart_id']; ?>" data-action="inc">+</button>
              </div>
            </td>
            <td>KES <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            <td>
              <a href="cart.php?remove=<?php echo $item['cart_id']; ?>"
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Remove this item?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
      <button type="submit" class="btn btn-outline-secondary">Update Cart</button>
      <div class="text-end">
        <p class="fs-5 fw-bold">Total: KES <?php echo number_format($total, 2); ?></p>
        <a href="checkout.php" class="btn btn-primary btn-lg">Proceed to Checkout</a>
      </div>
    </div>
  </form>

  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
