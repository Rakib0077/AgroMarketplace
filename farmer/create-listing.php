<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$error = '';
$notice = take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $inventoryItemId = (int) ($_POST['inventory_item_id'] ?? 0);
  $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_FLOAT);
  $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);

  $imagePath = null;
  $savedImagePath = null;
  $upload = $_FILES['product_image'] ?? null;
  if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 5 * 1024 * 1024) {
      $error = 'Choose a product image smaller than 5 MB.';
    } else {
      $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
      $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
      ];
      if (!isset($extensions[$mimeType])) {
        $error = 'Upload a JPG, PNG, or WebP product image.';
      } else {
        $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        $savedImagePath = __DIR__ . '/../assets/listings/' . $fileName;
        if (move_uploaded_file($upload['tmp_name'], $savedImagePath)) {
          $imagePath = 'assets/listings/' . $fileName;
        } else {
          $error = 'The product image could not be saved.';
        }
      }
    }
  }

  if ($error === '' && ($inventoryItemId < 1 || $quantity === false || $quantity <= 0 || $price === false || $price < 0)) {
    $error = 'Choose an inventory item, enter a positive quantity, and enter a valid price.';
  }

  if ($error !== '' && $savedImagePath && is_file($savedImagePath)) {
    unlink($savedImagePath);
  }

  if ($error === '') {
    try {
      $deliveryNotes = trim($_POST['delivery_notes'] ?? '');
      $database->createFarmerListing(
        (int) $user['id'],
        $inventoryItemId,
        (float) $quantity,
        (float) $price,
        $imagePath,
        $deliveryNotes
      );
      set_flash('success', 'Your listing is now live in the marketplace.');
      redirect_to('create-listing.php');
    } catch (Throwable $exception) {
      if ($savedImagePath && is_file($savedImagePath)) {
        unlink($savedImagePath);
      }
      $error = $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'We could not publish the listing. Please check the values and try again.';
    }
  }
}

$notice = $notice ?? take_flash();
$inventoryItems = $database->getAvailableFarmerInventory((int) $user['id']);
$selectedInventoryId = (int) ($_POST['inventory_item_id'] ?? 0);
$selectedInventory = null;
foreach ($inventoryItems as $inventoryItem) {
  if ((int) $inventoryItem['id'] === $selectedInventoryId) {
    $selectedInventory = $inventoryItem;
    break;
  }
}
$listings = $database->getFarmerListings((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Listing — Farmer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="farmer.css">
</head>
<body class="theme-farmer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Farmer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile/profile-self.php">Profile</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="create-listing.php">Listing</a>
        <a class="nav-link" href="bids.php">Bids</a>
        <a class="nav-link" href="inventory.php">Inventory</a>
        <a class="nav-link" href="sales-ledger.php">Sales Ledger</a>
        <a class="nav-link" href="chat.php">Chat</a>
      </nav>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="page-shell">
    <section class="content-area">
      <section class="panel">
        <div class="page-head">
          <div>
            <p class="eyebrow">Farmer workspace</p>
            <h1 class="page-title">Listing</h1>
          </div>
        </div>
        <p class="meta-text">Create listings from available inventory. Each listing is limited to the stock not already offered.</p>
      </section>

      <section class="content-grid">
        <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
        <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
        <?php if (!$inventoryItems): ?>
          <article class="context-card">
            <div class="section-header">
              <h2>No unlisted inventory</h2>
            </div>
            <p class="meta-text">All available stock is already listed, or no inventory has been added. Add stock or increase an inventory quantity before creating another listing.</p>
            <a class="button button-primary" href="inventory.php">Manage inventory</a>
          </article>
        <?php else: ?>
        <form class="context-card" action="create-listing.php" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="section-header">
            <h2>New listing</h2>
            <span class="pill pill-success">Ready</span>
          </div>

          <div class="form-grid">
            <label class="field-group">
              <span>Inventory item</span>
              <select id="inventory-item" name="inventory_item_id" required>
                <option value="">Choose available inventory</option>
                <?php foreach ($inventoryItems as $inventoryItem): ?>
                  <option value="<?= (int) $inventoryItem['id'] ?>" data-available="<?= escape_html((string) $inventoryItem['available_quantity']) ?>" <?= (int) $inventoryItem['id'] === $selectedInventoryId ? 'selected' : '' ?>><?= escape_html($inventoryItem['name']) ?> - <?= escape_html($inventoryItem['category'] ?? 'Uncategorized') ?> (<?= number_format((float) $inventoryItem['available_quantity'], 3) ?> <?= escape_html($inventoryItem['unit']) ?> available)</option>
                <?php endforeach; ?>
              </select>
            </label>
            <label class="field-group">
              <span>Quantity to list</span>
              <input id="listing-quantity" type="number" name="quantity" min="0.001" step="0.001" max="<?= $selectedInventory ? escape_html((string) $selectedInventory['available_quantity']) : '' ?>" required>
            </label>
            <label class="field-group">
              <span>Price expectation</span>
              <input type="number" name="price" min="0" step="0.01" required>
            </label>
            <label class="field-group full-width">
              <span>Product photo (JPG, PNG, or WebP; up to 5 MB)</span>
              <input type="file" name="product_image" accept="image/jpeg,image/png,image/webp">
            </label>
            <label class="field-group">
              <span>Status</span>
              <input type="text" value="Active after publish" disabled>
            </label>
            <label class="field-group full-width">
              <span>Delivery notes</span>
              <textarea name="delivery_notes" rows="4"></textarea>
            </label>
          </div>

          <div class="form-actions">
            <button class="button button-primary" type="submit">Publish listing</button>
          </div>
        </form>
        <?php endif; ?>

        <article class="context-card">
          <div class="section-header">
            <h2>Market guidance</h2>
            <span class="pill pill-muted">Best price</span>
          </div>
          <ul class="context-list">
            <li class="context-item">
              <div>
                <strong>Premium rice</strong>
                <small>Strong buyer demand this week</small>
              </div>
              <span class="context-figure">+18%</span>
            </li>
            <li class="context-item">
              <div>
                <strong>Warehouse pickup</strong>
                <small>Most listings with this option sell faster</small>
              </div>
              <span class="context-figure warn">Recommended</span>
            </li>
            <li class="context-item">
              <div>
                <strong>Listing expiry</strong>
                <small>Keep active offers within 14 days</small>
              </div>
              <span class="context-figure">14 days</span>
            </li>
          </ul>
        </article>
      </section>

      <section class="panel">
        <div class="section-header">
          <h2>All listings</h2>
          <span class="pill pill-success"><?= count($listings) ?> listings</span>
        </div>

        <div class="table-wrap">
          <table class="data-table listing-table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($listings as $listing): ?>
                <tr>
                  <td><?= escape_html($listing['title']) ?></td>
                  <td><?= escape_html($listing['category']) ?></td>
                  <td><?= number_format((float) $listing['price_per_unit'], 2) ?> BDT/<?= escape_html($listing['quantity_unit']) ?></td>
                  <td><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></td>
                  <td><span class="status-tag <?= $listing['status'] === 'active' ? 'active' : 'pending' ?>"><?= escape_html(ucfirst($listing['status'])) ?></span></td>
                  <td class="listing-actions"><a class="button button-secondary small-button" href="edit-listing.php?id=<?= (int) $listing['id'] ?>">Edit</a></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$listings): ?><tr><td colspan="6">No listings yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
  <script>
    const inventorySelect = document.getElementById('inventory-item');
    const quantityInput = document.getElementById('listing-quantity');

    if (inventorySelect && quantityInput) {
      inventorySelect.addEventListener('change', () => {
        const option = inventorySelect.selectedOptions[0];
        quantityInput.max = option.dataset.available || '';
        quantityInput.value = '';
      });
    }
  </script>
</body>
</html>
