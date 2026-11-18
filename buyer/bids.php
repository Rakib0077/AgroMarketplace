<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$notice = take_flash();
$database = app_database();
$bids = $database->getBuyerBids((int) $user['id']);
$stats = $database->getBuyerBidStats((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Bids — Buyer</title>
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
    <section class="content-area">
      <section class="panel">
        <div class="page-head">
          <div>
            <p class="eyebrow">Buyer workspace</p>
            <h1 class="page-title">My bids</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">Refresh bids</button>
          </div>
        </div>
        <p class="meta-text">Track the offers you have placed, the counter bids on the table, and the deals that are closing soon.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card">
          <h3>Active bids</h3>
          <p class="stat-value"><?= (int) $stats['bid_count'] ?></p>
        </div>
        <div class="stat-card">
          <h3>Leading offer</h3>
          <p class="stat-value"><?= number_format((float) ($stats['average_price'] ?? 0), 2) ?> BDT</p>
        </div>
        <div class="stat-card">
          <h3>Avg. bid</h3>
          <p class="stat-value"><?= count($bids) ?></p>
        </div>
        <div class="stat-card">
          <h3>Next expiry</h3>
          <p class="stat-value">04h 12m</p>
        </div>
      </section>

      <section class="panel">
        <div class="section-header">
          <h2>Open bids</h2>
            <span class="pill pill-success"><?= count($bids) ?> total</span>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Listing</th>
              <th>Seller</th>
              <th>Your offer</th>
              <th>Counter</th>
              <th>Expiry</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
          <tbody>
            <?php foreach ($bids as $bid): ?>
              <tr>
                <td><?= escape_html($bid['title']) ?></td>
                <td><a class="profile-link" href="../farmer/profile/profile-public.php?id=<?= (int) $bid['farmer_id'] ?>"><?= escape_html($bid['business_name'] ?: $bid['fullname']) ?></a></td>
                <td><?= number_format((float) $bid['price_per_unit'], 2) ?> BDT</td>
                <td><?= number_format((float) $bid['asking_price'], 2) ?> BDT</td>
                <td><?= $bid['delivery_date'] ? escape_html($bid['delivery_date']) : 'Not set' ?></td>
                <td><span class="pill pill-muted"><?= escape_html(ucfirst($bid['status'])) ?></span></td>
                <td><?php if ($bid['status'] === 'pending'): ?><a class="button button-secondary" href="edit-bid.php?id=<?= (int) $bid['id'] ?>">Edit bid</a><?php else: ?><span>—</span><?php endif; ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$bids): ?><tr><td colspan="7">You have not placed any bids yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
