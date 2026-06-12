<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$message = '';
$error   = '';

// Add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $image       = trim($_POST['image'] ?? '');

    if ($name === '' || $price <= 0 || $category_id === 0) {
        $error = 'Name, price, and category are required.';
    } else {
        $name_s  = mysqli_real_escape_string($conn, $name);
        $desc_s  = mysqli_real_escape_string($conn, $description);
        $image_s = mysqli_real_escape_string($conn, $image);
        $sql = "INSERT INTO products (name, description, price, stock, category_id, image)
                VALUES ('$name_s', '$desc_s', $price, $stock, $category_id, '$image_s')";
        if (mysqli_query($conn, $sql)) {
            $message = 'Product added successfully.';
        } else {
            $error = 'Failed to add product.';
        }
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $delId");
    header('Location: products.php');
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$products   = mysqli_query($conn,
    "SELECT p.*, c.name AS category_name FROM products p
     LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
?>

<div class="container-fluid my-4">
  <h2 class="fw-bold mb-4"><i class="bi bi-box-seam"></i> Product Management</h2>

  <?php if ($message): ?>
    <div class="alert alert-success py-2"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Add product form -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">Add New Product</div>
        <div class="card-body">
          <form method="POST" action="products.php">
            <input type="hidden" name="action" value="add">
            <div class="mb-2">
              <label class="form-label">Product Name *</label>
              <input type="text" name="name" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2">
              <label class="form-label">Category *</label>
              <select name="category_id" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <?php
                mysqli_data_seek($categories, 0);
                while ($cat = mysqli_fetch_assoc($categories)):
                ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <div class="row g-2 mb-2">
              <div class="col">
                <label class="form-label">Price (KES) *</label>
                <input type="number" name="price" class="form-control form-control-sm" min="0" step="0.01" required>
              </div>
              <div class="col">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control form-control-sm" min="0" value="0">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Image URL</label>
              <input type="text" name="image" class="form-control form-control-sm" placeholder="https://...">
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">Add Product</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Products table -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">All Products</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                  <td><?php echo $p['id']; ?></td>
                  <td><?php echo htmlspecialchars($p['name']); ?></td>
                  <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                  <td>KES <?php echo number_format($p['price'], 2); ?></td>
                  <td><?php echo $p['stock']; ?></td>
                  <td>
                    <a href="products.php?delete=<?php echo $p['id']; ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this product?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
