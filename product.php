<?php require_once 'includes/header.php'; ?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo '<div class="container my-5"><div class="alert alert-danger">Product not found. <a href="products.php">Go back</a></div></div>';
    require_once 'includes/footer.php';
    exit;
}
?>

<div class="container my-5">
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="products.php">Products</a></li>
      <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-md-5">
      <img src="<?php echo htmlspecialchars($product['image'] ?: 'assets/images/placeholder.jpg'); ?>"
           class="img-fluid rounded shadow" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="col-md-7">
      <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($product['category_name']); ?></span>
      <h2 class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></h2>
      <p class="price fs-3 my-3">KES <?php echo number_format($product['price'], 2); ?></p>
      <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

      <p class="mt-3">
        <?php if ($product['stock'] > 0): ?>
          <span class="badge bg-success"><i class="bi bi-check-circle"></i> In Stock (<?php echo $product['stock']; ?> left)</span>
        <?php else: ?>
          <span class="badge bg-danger">Out of Stock</span>
        <?php endif; ?>
      </p>

      <?php if ($product['stock'] > 0): ?>
      <form action="cart.php" method="POST" class="mt-4 d-flex gap-3 align-items-center">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <div class="input-group" style="width:130px;">
          <button type="button" class="btn btn-outline-secondary qty-btn" data-id="<?php echo $product['id']; ?>" data-action="dec">-</button>
          <input type="number" class="form-control text-center" name="qty_<?php echo $product['id']; ?>" id="qty" value="1" min="1" max="<?php echo $product['stock']; ?>">
          <button type="button" class="btn btn-outline-secondary qty-btn" data-id="<?php echo $product['id']; ?>" data-action="inc">+</button>
        </div>
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
