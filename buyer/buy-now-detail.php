<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$listingId = (int) ($_GET['id'] ?? $_POST['listing_id'] ?? 0);
$listing = $database->getActiveListingDetails($listingId);
if (!$listing) {
  http_response_code(404);
  exit('This listing is no longer available.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_FLOAT);

  if ($quantity === false || $quantity <= 0) {
    $error = 'Enter a valid order quantity.';
  } elseif ((int) $listing['farmer_id'] === (int) $user['id']) {
    $error = 'You cannot buy your own listing.';
  } else {
    try {
      $orderId = $database->createBuyerOrder((int) $user['id'], $listingId, (float) $quantity);
      set_flash('success', 'Order placed. Payment is pending.');
      redirect_to('transaction.php?id=' . $orderId);
    } catch (Throwable $exception) {
      $error = 'The order could not be placed. The listing may have sold or the stock may have changed.';
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buy Now Details — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body class="theme-buyer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Buyer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile-self.php">Profile</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="bids.php">My Bids</a>
        <a class="nav-link" href="orders.php">Orders</a>
        <a class="nav-link" href="invoices.php">Invoices</a>
        <a class="nav-link" href="chat.php">Chat</a>
      </nav>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="page-shell">
    <section class="panel">
      <div class="page-head">
        <div>
          <p class="eyebrow">Buyer checkout</p>
          <h1 class="page-title">Buy now details</h1>
        </div>
        <a class="button button-secondary" href="marketplace.php">Back to marketplace</a>
      </div>

      <div class="content-grid" style="grid-template-columns: 1.4fr 0.8fr; align-items:start;">
        <article class="context-card" style="box-shadow: none;">
          <p class="eyebrow"><?= escape_html($listing['category'] ?? 'Produce') ?></p>
          <h2><?= escape_html($listing['title']) ?></h2>
          <p class="meta-text"><?= escape_html($listing['quality_label'] ?? '') ?></p>
          <p class="meta-text">Seller: <?= escape_html($listing['business_name'] ?: $listing['fullname']) ?></p>
          <div class="stats-grid" style="margin-top:16px; box-shadow: none;">
            <div class="stat-card">
              <h3>Unit price</h3>
              <p class="stat-value"><?= number_format((float) $listing['price_per_unit'], 2) ?> BDT</p>
            </div>
            <div class="stat-card">
              <h3>Quantity</h3>
              <p class="stat-value"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></p>
            </div>
            <div class="stat-card">
              <h3>Location</h3>
              <p class="stat-value"><?= escape_html($listing['harvest_date'] ?? 'Location not provided') ?></p>
            </div>
          </div>
        </article>

        <aside class="context-card" style="box-shadow: none;">
          <div class="section-header">
            <h2>Order summary</h2>
            <span class="pill pill-success">Ready</span>
          </div>
          <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
          <form action="buy-now-detail.php?id=<?= (int) $listing['id'] ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>">
            <label class="filter-group" for="order-quantity">Quantity (<?= escape_html($listing['quantity_unit']) ?>)
              <input id="order-quantity" name="quantity" type="number" min="0.001" max="<?= escape_html((string) $listing['quantity_available']) ?>" step="0.001" value="1" required>
            </label>
            <p class="meta-text">Unit price: <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT. Payment is not collected by this demo.</p>
            <button class="button button-primary" type="submit">Place order</button>
          </form>
        </aside>
      </div>
    </section>
  </main>
</body>
</html>
