<?php require_once 'includes/header.php'; ?>

<div class="container my-5">

  <div class="row">
    <!-- Sidebar filters -->
    <div class="col-md-3 mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">Filter Products</div>
        <div class="card-body">
          <form method="GET" action="products.php">
            <label class="form-label fw-semibold">Category</label>
            <?php
            $cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
            while ($cat = mysqli_fetch_assoc($cats)):
              $checked = (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'checked' : '';
            ?>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="cat"
                     value="<?php echo $cat['id']; ?>" id="cat<?php echo $cat['id']; ?>" <?php echo $checked; ?>>
              <label class="form-check-label" for="cat<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
              </label>
            </div>
            <?php endwhile; ?>
            <div class="form-check mt-1">
              <input class="form-check-input" type="radio" name="cat" value="" id="catAll"
                     <?php echo !isset($_GET['cat']) || $_GET['cat'] === '' ? 'checked' : ''; ?>>
              <label class="form-check-label" for="catAll">All Categories</label>
            </div>

            <hr>
            <label class="form-label fw-semibold">Search</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Product name..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">

            <button type="submit" class="btn btn-primary btn-sm w-100 mt-3">Apply</button>
            <a href="products.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Clear</a>
          </form>
        </div>
      </div>
    </div>

    <!-- Products grid -->
    <div class="col-md-9">
      <h2 class="fw-bold mb-4">All Products</h2>

      <?php
      $where = ["p.stock > 0"];
      $params = [];

      if (!empty($_GET['cat'])) {
          $catId = (int)$_GET['cat'];
          $where[] = "p.category_id = $catId";
      }

      if (!empty($_GET['q'])) {
          $q = mysqli_real_escape_string($conn, $_GET['q']);
          $where[] = "p.name LIKE '%$q%'";
      }

      $whereStr = 'WHERE ' . implode(' AND ', $where);
      $sql = "SELECT p.*, c.name AS category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              $whereStr
              ORDER BY p.name";
      $result = mysqli_query($conn, $sql);
      $count = mysqli_num_rows($result);
      ?>

      <p class="text-muted mb-3"><?php echo $count; ?> product<?php echo $count !== 1 ? 's' : ''; ?> found</p>

      <?php if ($count === 0): ?>
        <div class="alert alert-info">No products found. <a href="products.php">Clear filters</a></div>
      <?php else: ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        <?php while ($p = mysqli_fetch_assoc($result)): ?>
        <div class="col">
          <div class="card product-card shadow-sm h-100">
            <img src="<?php echo htmlspecialchars($p['image'] ?: 'assets/images/placeholder.jpg'); ?>"
                 class="card-img-top"
                 alt="<?php echo htmlspecialchars($p['name']); ?>">
            <div class="card-body d-flex flex-column">
              <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($p['category_name']); ?></span>
              <h6 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h6>
              <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars(mb_substr($p['description'], 0, 70)) . '...'; ?></p>
              <p class="price">KES <?php echo number_format($p['price'], 2); ?></p>
              <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm">View Details</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once 'includes/footer.php'; ?>
