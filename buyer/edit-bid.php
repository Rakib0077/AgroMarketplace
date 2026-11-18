<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$bidId = (int) ($_GET['id'] ?? $_POST['bid_id'] ?? 0);
$bid = $database->getBuyerBid($bidId, (int) $user['id']);
if (!$bid) {
  http_response_code(404);
  exit('Bid not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  if ($bid['status'] !== 'pending') {
    http_response_code(409);
    exit('Only pending bids can be changed.');
  }
  if (($_POST['action'] ?? '') === 'cancel') {
    $database->cancelBuyerBid($bidId, (int) $user['id']);
    set_flash('success', 'Bid cancelled.');
  } else {
    $price = filter_var($_POST['price_per_unit'] ?? '', FILTER_VALIDATE_FLOAT);
    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_FLOAT);
    if ($price === false || $price < 0 || $quantity === false || $quantity <= 0) {
      http_response_code(422);
      exit('Enter a valid bid price and quantity.');
    }
    $database->updateBuyerBid($bidId, (int) $user['id'], (float) $price, (float) $quantity, trim($_POST['message'] ?? ''));
    set_flash('success', 'Bid updated.');
  }
  redirect_to('bids.php');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Bid — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
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
          <p class="eyebrow">Buyer bids</p>
          <h1 class="page-title">Edit bid</h1>
        </div>
        <a class="button button-secondary" href="bids.php">Back to bids</a>
      </div>

      <div class="content-grid" style="grid-template-columns: 1.4fr 0.8fr; align-items:start;">
        <article class="context-card">
          <img class="product-image" src="../assets/nazirshail rice.jpeg" alt="Premium Nazirshail rice" style="width:100%; height:260px; object-fit:cover; border-radius:16px; margin-bottom:16px;">
          <h2>Premium Nazirshail Rice</h2>
          <p class="meta-text">Sold by <a class="profile-link" href="../farmer/profile/profile-public.php">Rahim Uddin</a> • Current counter: Tk 68,000</p>

          <div class="stats-grid" style="margin-top:16px; padding:0; background:transparent; border:none; box-shadow:none; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px;">
            <div style="padding:14px 14px; border: 1px solid var(--color-border); border-radius:14px; background:linear-gradient(180deg, var(--color-card-bg), var(--color-buyer-light-bg));">
              <h3 style="margin:0; color:var(--color-text-soft); font-size:12px;">Current bid</h3>
              <p class="stat-value" style="margin-top:6px; font-size:20px;">Tk 67,500</p>
            </div>
            <div style="padding:14px 14px; border: 1px solid var(--color-border); border-radius:14px; background:linear-gradient(180deg, var(--color-card-bg), var(--color-buyer-light-bg));">
              <h3 style="margin:0; color:var(--color-text-soft); font-size:12px;">Expiry</h3>
              <p class="stat-value" style="margin-top:6px; font-size:20px;">04h 12m</p>
            </div>
          </div>
        </article>

        <aside class="context-card">
          <div class="section-header">
            <h2>Update your bid</h2>
            <span class="pill pill-success">Open</span>
          </div>

          <form action="edit-bid.php?id=<?= (int) $bidId ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="bid_id" value="<?= (int) $bidId ?>">
            <div class="field-group" style="margin-bottom:16px;">
              <span>Bid amount</span>
              <input type="number" name="price_per_unit" min="0" step="0.01" value="<?= escape_html((string) $bid['price_per_unit']) ?>" required style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;">
            </div>

            <div class="field-group" style="margin-bottom:16px;">
              <span>Quantity</span>
              <input type="number" name="quantity" min="0.001" step="0.001" value="<?= escape_html((string) $bid['quantity']) ?>" required style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;">
            </div>

            <div class="field-group" style="margin-bottom:16px;">
              <span>Notes</span>
              <textarea name="message" rows="4" style="width:100%; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;"><?= escape_html($bid['message']) ?></textarea>
            </div>

            <div class="form-actions" style="justify-content:space-between;">
              <button class="button button-danger" type="submit" name="action" value="cancel">Cancel bid</button>
              <button class="button button-primary" type="submit">Save changes</button>
            </div>
          </form>
        </aside>
      </div>

      <section class="panel" style="margin-top:20px;">
        <div class="section-header" style="margin-bottom:12px;">
          <h2>Bid timeline</h2>
          <span class="pill pill-success">You lead</span>
        </div>

        <ul class="timeline-list">
          <li class="timeline-item">
            <span class="dot dot-success"></span>
            <div>
              <strong>You — Tk 67,500</strong>
              <small>Today • 09:48 AM • Current highest bid</small>
            </div>
            <span class="pill pill-success">Leader</span>
          </li>
          <li class="timeline-item">
            <span class="dot dot-warn"></span>
            <div>
              <strong>Shahadat Hossain — Tk 67,200</strong>
              <small>Today • 09:22 AM</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-muted"></span>
            <div>
              <strong>Nabila Ahmed — Tk 66,900</strong>
              <small>Today • 09:05 AM</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-muted"></span>
            <div>
              <strong>Mahmudul Hasan — Tk 66,350</strong>
              <small>Today • 08:41 AM</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-success"></span>
            <div>
              <strong>You — Tk 65,900</strong>
              <small>Today • 08:12 AM • Joined the auction</small>
            </div>
          </li>
        </ul>
      </section>
    </section>
  </main>
</body>
</html>
