<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$listingId = (int) ($_GET['id'] ?? $_POST['listing_id'] ?? 0);
$error = '';
$listing = $database->getFarmerListingForEdit($listingId, (int) $user['id']);
if (!$listing) {
  http_response_code(404);
  exit('Listing not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? 'save';
  if ($action === 'archive') {
    $database->archiveFarmerListing($listingId, (int) $user['id']);
    set_flash('success', 'Listing removed from the marketplace.');
    redirect_to('create-listing.php');
  }
  $title = trim($_POST['title'] ?? '');
  $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_FLOAT);
  $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
  $categoryId = (int) ($_POST['category_id'] ?? 0);
  if ($title === '' || $quantity === false || $quantity < 0 || $price === false || $price < 0 || $categoryId < 1) {
    $error = 'Enter a title, category, valid quantity, and price.';
  }

  $imagePath = $listing['image_path'];
  $savedImagePath = null;
  $upload = $_FILES['product_image'] ?? null;
  if ($error === '' && $upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 5 * 1024 * 1024) {
      $error = 'Choose a product image smaller than 5 MB.';
    } else {
      $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
      $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
      if (!isset($extensions[$mimeType])) {
        $error = 'Upload a JPG, PNG, or WebP product image.';
      } else {
        $fileName = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        $savedImagePath = __DIR__ . '/../assets/listings/' . $fileName;
        if (move_uploaded_file($upload['tmp_name'], $savedImagePath)) {
          $imagePath = 'assets/listings/' . $fileName;
        } else {
          $savedImagePath = null;
          $error = 'The product image could not be saved.';
        }
      }
    }
  }

  if ($error === '') {
    try {
      $database->updateFarmerListing($listingId, (int) $user['id'], [
        'title' => $title,
        'category_id' => $categoryId,
        'quantity' => (float) $quantity,
        'price' => (float) $price,
        'quality_label' => trim($_POST['quality_label'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'delivery_notes' => trim($_POST['delivery_notes'] ?? ''),
        'image_path' => $imagePath,
      ]);
      if ($savedImagePath && str_starts_with($listing['image_path'] ?? '', 'assets/listings/')) {
        $oldImagePath = __DIR__ . '/../' . $listing['image_path'];
        if (is_file($oldImagePath)) {
          unlink($oldImagePath);
        }
      }
      set_flash('success', 'Listing updated.');
      redirect_to('create-listing.php');
    } catch (Throwable $exception) {
      if ($savedImagePath && is_file($savedImagePath)) {
        unlink($savedImagePath);
      }
      $error = $exception instanceof RuntimeException
        ? $exception->getMessage()
        : 'The listing could not be saved. Please try again.';
    }
  }
}
$categories = $database->getActiveCategories();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Listing — Farmer Dashboard</title>
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
        <a class="nav-link active" href="create-listing.php">Listing</a>
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
            <h1 class="page-title">Edit Crop Listing</h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="marketplace.php">Back to Marketplace</a>
          </div>
        </div>
        <p class="meta-text">Update crop quantities, pricing, descriptions, or media for your active listing (#LST-8902).</p>
      </section>

      <section class="content-grid">
        <!-- Main Form Area -->
        <article class="context-card" style="grid-column: span 2;">
          <div class="section-header">
            <h2>Listing Information</h2>
            <span class="pill pill-success">Active</span>
          </div>

          <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
          <form id="editListingForm" method="post" action="edit-listing.php?id=<?= (int) $listingId ?>" enctype="multipart/form-data" style="display:grid; gap:16px; margin-top:16px;">
            <?= csrf_field() ?>
            <input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
            <div style="display:grid; gap:6px;">
              <label for="crop-title" style="font-weight:700; font-size:13px; color:var(--color-muted);">Crop Name / Title</label>
              <input id="crop-title" name="title" type="text" value="<?= escape_html($listing['title']) ?>" required style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
              <div style="display:grid; gap:6px;">
                <label for="crop-category" style="font-weight:700; font-size:13px; color:var(--color-muted);">Category</label>
                <select id="crop-category" name="category_id" required style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
                  <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) $listing['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div style="display:grid; gap:6px;">
                <label for="crop-grade" style="font-weight:700; font-size:13px; color:var(--color-muted);">Quality Grade</label>
                <input id="crop-grade" name="quality_label" type="text" value="<?= escape_html($listing['quality_label']) ?>" style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
              </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
              <div style="display:grid; gap:6px;">
                <label for="crop-quantity" style="font-weight:700; font-size:13px; color:var(--color-muted);">Available Quantity (MT)</label>
                <input id="crop-quantity" name="quantity" type="number" min="0" max="<?= escape_html((string) $listing['max_quantity']) ?>" step="0.001" value="<?= escape_html((string) $listing['quantity_available']) ?>" required style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
                <small>Maximum based on available inventory: <?= number_format((float) $listing['max_quantity'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></small>
              </div>

              <div style="display:grid; gap:6px;">
                <label for="crop-price" style="font-weight:700; font-size:13px; color:var(--color-muted);">Unit Price (Tk / MT)</label>
                <input id="crop-price" name="price" type="number" min="0" step="0.01" value="<?= escape_html((string) $listing['price_per_unit']) ?>" required style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
              </div>
            </div>

            <div style="display:grid; gap:6px;">
              <label for="crop-location" style="font-weight:700; font-size:13px; color:var(--color-muted);">Harvest / Pickup Location</label>
              <input id="crop-location" name="delivery_notes" type="text" value="<?= escape_html($listing['delivery_notes']) ?>" style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff;">
            </div>

            <div style="display:grid; gap:6px;">
              <label for="crop-desc" style="font-weight:700; font-size:13px; color:var(--color-muted);">Detailed Description</label>
              <textarea id="crop-desc" name="description" rows="4" style="padding:10px 14px; border:1px solid var(--color-border); border-radius:10px; background:#fff; resize:vertical;"><?= escape_html($listing['description']) ?></textarea>
            </div>

            <div style="display:flex; gap:12px; margin-top:8px;">
              <button class="button button-primary" type="submit" name="action" value="save">Save Changes</button>
              <a class="button button-secondary" href="marketplace.php">Cancel</a>
            </div>
          </form>
        </article>

        <!-- Image & Status Column -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          <article class="context-card">
            <div class="section-header">
              <h2>Listing Preview</h2>
            </div>
            <div style="display:grid; gap:12px;">
              <img src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" style="width:100%; max-height:160px; object-fit:cover; border-radius:10px; border:1px solid var(--color-border);">
              <div style="display:grid; gap:6px;">
                <label for="crop-image" style="font-weight:700; font-size:13px; color:var(--color-muted);">Replace Image</label>
                <input id="crop-image" name="product_image" type="file" accept="image/jpeg,image/png,image/webp" form="editListingForm" style="padding:6px; border:1px solid var(--color-border); border-radius:8px; background:#fff; font-size:12px;">
              </div>
            </div>
          </article>

          <article class="context-card">
            <div class="section-header">
              <h2>Listing Controls</h2>
            </div>
            <p class="meta-text" style="margin-bottom:12px;">Manage current offering status across the marketplace.</p>
            <div style="display:flex; flex-direction:column; gap:8px;">
              <form method="post" action="edit-listing.php?id=<?= (int) $listingId ?>" onsubmit="return confirm('Remove this listing from the marketplace?');">
                <?= csrf_field() ?>
                <input type="hidden" name="listing_id" value="<?= (int) $listingId ?>">
                <input type="hidden" name="action" value="archive">
                <button class="button button-secondary" type="submit" style="width:100%; border-color:#ef4444; color:#b91c1c;">Delete Listing</button>
              </form>
            </div>
          </article>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
