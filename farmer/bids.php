<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $bidId = (int) ($_POST['bid_id'] ?? 0);
  $action = $_POST['action'] ?? '';

  try {
    $database->updateFarmerBid($bidId, (int) $user['id'], $action);
    set_flash('success', $action === 'accept' ? 'Bid accepted and order created.' : 'Bid rejected.');
    redirect_to('bids.php');
  } catch (Throwable $exception) {
    $error = 'The offer could not be updated. It may already have been handled.';
  }
}

$notice = take_flash();
$listings = $database->getFarmerBids((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bids — Farmer</title>
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
            <p class="eyebrow">Offers & Auctions</p>
            <h1 class="page-title">Manage Bids</h1>
          </div>
          <div class="header-actions">
            <a class="button button-primary" href="create-listing.php">New Listing</a>
          </div>
        </div>
        <p class="meta-text">Review incoming bids, accept a buyer, cancel a listing, or instantly sell to the top bidder.</p>
      </section>

      <section class="panel">
        <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
        <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
        <div class="bids-grid">
          <?php foreach ($listings as $listing): ?>
            <article class="bids-listing-card" style="box-shadow: none; margin-bottom: 32px;">
              <div class="bids-listing-header">
                <div>
                  <h2 class="bids-listing-title"><?= escape_html($listing['title']) ?></h2>
                  <p class="meta-text">Status: <?= escape_html(ucfirst($listing['status'])) ?></p>
                </div>
                <div class="bids-top-badge">
                  <span>Offers</span>
                  <strong><?= count($listing['bids']) ?></strong>
                </div>
              </div>
              <div class="bids-chip-group">
                <?php foreach ($listing['bids'] as $bid): ?>
                  <div class="bid-chip">
                    <span><?= escape_html($bid['business_name'] ?: $bid['fullname']) ?> — <?= number_format((float) $bid['bid_price'], 2) ?> BDT for <?= number_format((float) $bid['bid_quantity'], 3) ?> <?= escape_html($listing['unit']) ?> (<?= escape_html($bid['bid_status']) ?>)</span>
                    <?php if ($bid['bid_status'] === 'pending' && $listing['status'] === 'active'): ?>
                      <form method="post" action="bids.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="bid_id" value="<?= (int) $bid['bid_id'] ?>">
                        <button class="button button-primary small-button" name="action" value="accept" type="submit">Accept</button>
                        <button class="button button-secondary small-button" name="action" value="reject" type="submit">Reject</button>
                      </form>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
                <?php if (!$listing['bids']): ?><p class="meta-text">No incoming bids yet.</p><?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
          <?php if (!$listings): ?><p class="meta-text">You have no listings yet.</p><?php endif; ?>
        </div>
      </section>
    </section>
  </main>
</body>
</html>