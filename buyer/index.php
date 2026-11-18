<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$summary = $database->getBuyerDashboardSummary((int) $user['id']);
$pendingBids = $database->getPendingBuyerBidCount((int) $user['id']);
$recommendations = $database->getRecommendedListings();
$activity = $database->getBuyerRecentActivity((int) $user['id']);
$bids = $database->getRecentBuyerBids((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buyer Dashboard — AgroMarketplace</title>
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
            <h1 class="page-title">Welcome back, <?= escape_html($user['fullname'] ?: $user['username']) ?></h1>
          </div>
          <div class="header-actions">
            <input class="search-input" type="search" placeholder="Search listings" aria-label="Search listings">
            <button class="button button-primary" type="button">Search</button>
          </div>
        </div>
        <p class="meta-text">Monitor active bids, manage escrow, and track orders in a single dashboard.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card"><h3>Active Escrow</h3><p class="stat-value"><?= number_format((float) $summary['escrow'], 2) ?> BDT</p></div>
        <div class="stat-card"><h3>30-Day Orders</h3><p class="stat-value"><?= (int) $summary['recent_orders'] ?></p></div>
        <div class="stat-card"><h3>Pending Bids</h3><p class="stat-value"><?= $pendingBids ?></p></div>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Recommended buys</h2>
            <span class="pill pill-success">Hot</span>
          </div>
          <ul class="context-list">
            <?php foreach ($recommendations as $listing): ?>
              <li class="context-item">
                <div><strong><?= escape_html($listing['title']) ?></strong><small><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> available</small></div>
                <a class="context-figure" href="marketplace.php"> <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT</a>
              </li>
            <?php endforeach; ?>
            <?php if (!$recommendations): ?><li>No active listings yet.</li><?php endif; ?>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Recent activity</h2>
            <span class="pill pill-muted">Today</span>
          </div>
          <div class="timeline-list">
            <div class="timeline-list">
              <?php foreach ($activity as $event): ?>
                <div class="timeline-item"><span class="dot dot-muted"></span><div><strong><?= escape_html($event['activity']) ?></strong><small><?= escape_html($event['created_at']) ?></small></div></div>
              <?php endforeach; ?>
              <?php if (!$activity): ?><p>No recent activity.</p><?php endif; ?>
            </div>
      </section>

      <section class="panel">
        <h2>Active Bids</h2>
        <table class="data-table">
          <thead><tr><th>Listing</th><th>Seller</th><th>Offer</th><th>Counter</th><th>Time</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach ($bids as $bid): ?>
              <tr><td><?= escape_html($bid['title']) ?></td><td><a class="profile-link" href="../farmer/profile/profile-public.php?id=<?= (int) $bid['farmer_id'] ?>"><?= escape_html($bid['business_name'] ?: $bid['fullname']) ?></a></td><td><?= number_format((float) $bid['price_per_unit'], 2) ?> BDT</td><td><?= number_format((float) $bid['asking_price'], 2) ?> BDT</td><td>—</td><td><?= escape_html(ucfirst($bid['status'])) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$bids): ?><tr><td colspan="6">No bids yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
