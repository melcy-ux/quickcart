<?php require_once 'includes/header.php'; ?>

<section class="hero-section">
  <div class="container text-center">
    <h1><i class="bi bi-cart3"></i> Welcome to QuickCart</h1>
    <p class="lead mt-3">Quality products at affordable prices, delivered fast.</p>
    <a href="products.php" class="btn btn-light btn-lg mt-3 fw-semibold">Shop Now</a>
  </div>
</section>

<div class="container">

  <h2 class="mb-4 fw-bold">Featured Products</h2>

  <?php
  $sql = "SELECT p.*, c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.stock > 0
          ORDER BY p.created_at DESC
          LIMIT 8";
  $result = mysqli_query($conn, $sql);
  ?>

  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php while ($p = mysqli_fetch_assoc($result)): ?>
    <div class="col">
      <div class="card product-card shadow-sm">
        <img src="<?php echo htmlspecialchars($p['image'] ?: 'assets/images/placeholder.jpg'); ?>"
             class="card-img-top"
             alt="<?php echo htmlspecialchars($p['name']); ?>">
        <div class="card-body d-flex flex-column">
          <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($p['category_name']); ?></span>
          <h6 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h6>
          <p class="price mt-auto">KES <?php echo number_format($p['price'], 2); ?></p>
          <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm mt-2">View Product</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <div class="text-center mt-5">
    <a href="products.php" class="btn btn-outline-primary btn-lg">Browse All Products</a>
  </div>

</div>

<?php require_once 'includes/footer.php'; ?>
