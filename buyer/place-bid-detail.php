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
  $price = filter_var($_POST['price_per_unit'] ?? '', FILTER_VALIDATE_FLOAT);
  $deliveryDate = $_POST['delivery_date'] ?? '';

  if ($quantity === false || $quantity <= 0 || $quantity > (float) $listing['quantity_available']) {
    $error = 'Enter a quantity within the available stock.';
  } elseif ($price === false || $price < 0) {
    $error = 'Enter a valid offer price.';
  } else {
    $database->createBuyerBid(
      (int) $user['id'],
      $listingId,
      (float) $quantity,
      (float) $price,
      $deliveryDate !== '' ? $deliveryDate : null
    );
    set_flash('success', 'Your bid was submitted to the farmer.');
    redirect_to('bids.php');
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Place Bid Details — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
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
          <p class="eyebrow">Buyer auction</p>
          <h1 class="page-title">Place bid details</h1>
        </div>
        <a class="button button-secondary" href="marketplace.php">Back to marketplace</a>
      </div>

      <div class="content-grid" style="grid-template-columns: 1.4fr 0.8fr; align-items:start;">
        <article class="context-card" style="box-shadow: none;">
          <p class="eyebrow"><?= escape_html($listing['category'] ?? 'Produce') ?></p>
          <h2><?= escape_html($listing['title']) ?></h2>
          <p class="meta-text"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> available • <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT per <?= escape_html($listing['quantity_unit']) ?></p>
          <p class="meta-text">Seller: <?= escape_html($listing['business_name'] ?: $listing['fullname']) ?></p>
          <div class="stats-grid" style="margin-top:16px; box-shadow: none;">
            <div class="stat-card">
              <h3>Current bid</h3>
              <p class="stat-value"><?= number_format((float) $listing['price_per_unit'], 2) ?> BDT</p>
            </div>
            <div class="stat-card">
              <h3>Quantity</h3>
              <p class="stat-value"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></p>
            </div>
            <div class="stat-card">
              <h3>Closing</h3>
              <p class="stat-value">4h 12m</p>
            </div>
          </div>
        </article>

        <aside class="context-card" style="box-shadow: none;">
          <div class="section-header">
            <h2>Bid form</h2>
            <span class="pill pill-muted">Live</span>
          </div>

          <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
          <form action="place-bid-detail.php?id=<?= (int) $listing['id'] ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>">
            <div class="filter-group" style="margin-bottom:16px;">
              <label for="bid-amount">Your bid amount</label>
              <input id="bid-amount" name="price_per_unit" type="number" min="0" step="0.01" value="<?= escape_html((string) $listing['price_per_unit']) ?>" required style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px;">
            </div>

            <div class="filter-group" style="margin-bottom:16px;">
              <label for="bid-qty">Quantity</label>
              <input id="bid-qty" name="quantity" type="number" min="0.001" max="<?= escape_html((string) $listing['quantity_available']) ?>" step="0.001" value="1" required style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px;">
            </div>

            <div class="filter-group" style="margin-bottom:16px;">
              <label for="delivery-date">Preferred delivery</label>
              <input id="delivery-date" name="delivery_date" type="date" min="<?= date('Y-m-d') ?>" style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px;">
            </div>

            <button class="button button-primary" type="submit">Submit bid</button>
          </form>
        </aside>
      </div>
    </section>
  </main>
</body>
</html>
