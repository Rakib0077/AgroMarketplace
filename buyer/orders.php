<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$orders = $database->getBuyerOrders((int) $user['id']);
$stats = $database->getBuyerOrderStats((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Orders — Buyer</title>
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
            <h1 class="page-title">Orders</h1>
          </div>
        </div>
        <p class="meta-text">Your active and past orders, including delivery milestones and pickup confirmations.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card">
          <h3>Open orders</h3>
          <p class="stat-value"><?= (int) $stats['open_count'] ?></p>
        </div>
        <div class="stat-card">
          <h3>Total spend</h3>
          <p class="stat-value"><?= number_format((float) $stats['total_spend'], 2) ?> BDT</p>
        </div>
        <div class="stat-card">
          <h3>Next delivery</h3>
          <p class="stat-value"><?= count($orders) ?></p>
        </div>
        <div class="stat-card">
          <h3>On-time rate</h3>
          <p class="stat-value">Orders: <?= (int) $stats['order_count'] ?></p>
        </div>
      </section>

      <section class="panel" id="recent-orders">
        <div class="section-header">
          <h2>Recent orders</h2>
          <span class="pill pill-success">Updated</span>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Commodity</th>
              <th>Seller</th>
              <th>Qty</th>
              <th>Status</th>
              <th>Delivery</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= escape_html($order['order_number']) ?></td>
                <td><?= escape_html($order['title'] ?? 'Produce') ?></td>
                <td><a class="profile-link" href="../farmer/profile/profile-public.php?id=<?= (int) $order['farmer_id'] ?>"><?= escape_html($order['business_name'] ?: $order['fullname']) ?></a></td>
                <td><?= number_format((float) $order['quantity'], 3) ?> <?= escape_html($order['quantity_unit']) ?></td>
                <td><span class="pill pill-muted"><?= escape_html(ucwords(str_replace('_', ' ', $order['status']))) ?></span></td>
                <td><?= $order['expected_delivery_at'] ? escape_html($order['expected_delivery_at']) : 'Not scheduled' ?></td>
                <td>
                  <a class="button button-secondary" href="transaction.php?id=<?= (int) $order['id'] ?>">Details</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?><tr><td colspan="7">No orders yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
