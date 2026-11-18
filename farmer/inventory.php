<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? 'save';
  $itemId = (int) ($_POST['item_id'] ?? 0);

  if ($action === 'archive' && $itemId > 0) {
    $database->archiveFarmerInventoryItem($itemId, (int) $user['id']);
    set_flash('success', 'Inventory item removed.');
    redirect_to('inventory.php');
  }

  $name = trim($_POST['name'] ?? '');
  $categoryId = (int) ($_POST['category_id'] ?? 0);
  $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_FLOAT);
  $unit = ($_POST['unit'] ?? '') === 'KG' ? 'KG' : 'MT';
  $status = $_POST['status'] ?? 'available';
  $notes = trim($_POST['notes'] ?? '');

  if ($name === '' || $categoryId < 1 || $quantity === false || $quantity < 0 || !in_array($status, ['available', 'reserved', 'depleted'], true)) {
    $error = 'Enter a name, category, valid quantity, and status.';
  } else {
    $database->saveFarmerInventoryItem((int) $user['id'], $itemId, [
      'category_id' => $categoryId,
      'name' => $name,
      'quantity' => (float) $quantity,
      'unit' => $unit,
      'status' => $status,
      'notes' => $notes,
    ]);
    set_flash('success', 'Inventory saved.');
    redirect_to('inventory.php');
  }
}

$notice = take_flash();
$categories = $database->getActiveCategories();
$editItem = !empty($_GET['edit'])
  ? $database->getFarmerInventoryItem((int) $_GET['edit'], (int) $user['id'])
  : null;
$inventory = $database->getFarmerInventory((int) $user['id']);
$items = $inventory['items'];
$totals = $inventory['totals'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inventory — Farmer</title>
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
            <h1 class="page-title">Inventory</h1>
          </div>
          <div class="header-actions">
            <a class="button button-primary" href="#inventory-form">Add stock</a>
          </div>
        </div>
        <p class="meta-text">Track active grain lots, reserved stock, and quality status across all ready-to-sell produce.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card"><h3>Total lots</h3><p class="stat-value"><?= (int) $totals['total'] ?></p></div>
        <div class="stat-card"><h3>Available lots</h3><p class="stat-value"><?= (int) $totals['available'] ?></p></div>
        <div class="stat-card"><h3>Reserved lots</h3><p class="stat-value"><?= (int) $totals['reserved'] ?></p></div>
        <div class="stat-card"><h3>Inventory items</h3><p class="stat-value"><?= count($items) ?></p></div>
      </section>

      <section class="panel">
        <div class="section-header">
          <h2>Add or update inventory</h2>
          <span class="pill pill-success">Stock control</span>
        </div>

        <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
        <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
        <form id="inventory-form" action="inventory.php" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="item_id" value="<?= (int) ($editItem['id'] ?? 0) ?>">
          <div class="form-grid">
            <label class="field-group">
              <span>Commodity</span>
              <input type="text" name="name" value="<?= escape_html($editItem['name'] ?? '') ?>" placeholder="Commodity name" required>
            </label>

            <label class="field-group">
              <span>Category</span>
              <select name="category_id" required>
                <option value="">Choose category</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= (int) $category['id'] ?>" <?= (int) ($editItem['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="field-group">
              <span>Available quantity</span>
              <input type="number" name="quantity" min="0" step="0.001" value="<?= escape_html(isset($editItem['quantity']) ? (string) $editItem['quantity'] : '') ?>" required>
            </label>

            <label class="field-group">
              <span>Unit</span>
              <select name="unit"><option value="MT" <?= ($editItem['unit'] ?? 'MT') === 'MT' ? 'selected' : '' ?>>MT</option><option value="KG" <?= ($editItem['unit'] ?? '') === 'KG' ? 'selected' : '' ?>>KG</option></select>
            </label>

            <label class="field-group">
              <span>Status</span>
              <select name="status">
                <?php foreach (['available', 'reserved', 'depleted'] as $status): ?>
                  <option value="<?= $status ?>" <?= ($editItem['status'] ?? 'available') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="field-group full-width">
              <span>Storage / notes</span>
              <textarea name="notes"><?= escape_html($editItem['notes'] ?? '') ?></textarea>
            </label>
          </div>

          <div class="form-actions">
            <a class="button button-secondary" href="inventory.php">Clear</a>
            <button class="button button-primary" name="action" value="save" type="submit">Save inventory</button>
          </div>
        </form>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Lot overview</h2>
            <span class="pill pill-success">Healthy</span>
          </div>
          <ul class="context-list">
            <li class="context-item">
              <div>
                <strong>Lot 01 — Rice</strong>
                <small>Grade A • 50 MT stored</small>
              </div>
              <span class="context-figure">Ready</span>
            </li>
            <li class="context-item">
              <div>
                <strong>Lot 02 — Wheat</strong>
                <small>18 MT reserved for buyers</small>
              </div>
              <span class="context-figure warn">Hold</span>
            </li>
            <li class="context-item">
              <div>
                <strong>Lot 03 — Lentils</strong>
                <small>12 MT available for new bids</small>
              </div>
              <span class="context-figure">Open</span>
            </li>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Inventory status</h2>
            <span class="pill pill-muted">Live</span>
          </div>
          <div class="mini-metrics">
            <div><span>Total stock</span><strong>80 MT</strong></div>
            <div><span>Reserved</span><strong>18 MT</strong></div>
            <div><span>Available</span><strong>62 MT</strong></div>
            <div><span>Quality checks</span><strong>3 passed</strong></div>
          </div>
        </article>
      </section>

      <section class="panel">
        <div class="section-header">
          <h2>Inventory list</h2>
          <span class="pill pill-muted">Manage lots</span>
        </div>

        <div class="table-wrap">
          <table class="data-table listing-table">
            <thead>
              <tr>
                <th>Commodity</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td><strong><?= escape_html($item['name']) ?></strong></td>
                  <td><?= escape_html($item['category'] ?? '') ?></td>
                  <td><span class="qty-badge"><?= number_format((float) $item['quantity'], 3) ?> <?= escape_html($item['unit']) ?></span></td>
                  <td><span class="status-tag <?= $item['status'] === 'available' ? 'active' : 'pending' ?>"><?= escape_html(ucfirst($item['status'])) ?></span></td>
                  <td><?= escape_html($item['notes']) ?></td>
                  <td class="listing-actions">
                    <a class="button small-button button-secondary" href="inventory.php?edit=<?= (int) $item['id'] ?>#inventory-form">Edit</a>
                    <?php if ($item['status'] !== 'archived'): ?>
                      <form method="post" action="inventory.php" onsubmit="return confirm('Remove this inventory item?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                        <button class="button small-button button-danger" name="action" value="archive" type="submit">Remove</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$items): ?><tr><td colspan="6">No inventory items recorded.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
</body>
</html>