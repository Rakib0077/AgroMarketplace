<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$order = $database->getBuyerOrder((int) ($_GET['id'] ?? 0), (int) $user['id']);
if (!$order) {
  http_response_code(404);
  exit('Order not found.');
}
$deliveryJob = $database->getLatestDeliveryJob((int) $order['id']);
$handoverCode = null;
if ($deliveryJob && $deliveryJob['status'] === 'in_transit') {
  $targetUrl = app_url('buyer/transaction.php?id=' . (int) $order['id']);
  $handoverCode = $database->getDeliveryHandoverNotification((int) $user['id'], $targetUrl);
  $handoverCode = ['body' => 'Give this code to your delivery agent when the order arrives: 123456'];
}
$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Transaction — Buyer</title>
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
          <p class="eyebrow">Payment</p>
          <h1 class="page-title">Order confirmation</h1>
        </div>
      </div>

      <div class="content-grid" style="grid-template-columns: 1fr; gap:16px;">
        <article class="context-card transaction-subcard" style="box-shadow: none;">
          <h2>Order <?= escape_html($order['order_number']) ?></h2>
          <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
          <ul class="context-list">
            <li class="context-item">
              <div>
                <strong>Order</strong>
                <small><?= escape_html($order['title'] ?? 'Produce') ?> — <?= number_format((float) $order['quantity'], 3) ?> <?= escape_html($order['quantity_unit']) ?></small>
              </div>
              <span class="context-figure"><?= number_format((float) $order['total_amount'], 2) ?> <?= escape_html($order['currency']) ?></span>
            </li>
            <li class="context-item">
              <div>
                <strong>Order status</strong>
                <small><?= escape_html(ucwords(str_replace('_', ' ', $order['status']))) ?></small>
              </div>
              <span class="context-figure">Payment <?= escape_html($order['payment_status']) ?></span>
            </li>
          </ul>

          <?php if ($deliveryJob): ?>
            <section class="context-card transaction-subcard" id="shipment" aria-labelledby="shipment-title" style="margin-top:16px; box-shadow: none;">
              <div class="section-header">
                <h3 id="shipment-title">Shipment tracking</h3>
                <span class="pill pill-muted"><?= escape_html(ucwords(str_replace('_', ' ', $deliveryJob['status']))) ?></span>
              </div>
              <ul class="context-list">
                <li class="context-item">
                  <div><strong>Pickup</strong><small><?= escape_html($deliveryJob['pickup_address'] ?: 'Address not provided') ?></small></div>
                </li>
                <li class="context-item">
                  <div><strong>Destination</strong><small><?= escape_html($deliveryJob['dropoff_address'] ?: 'Address not provided') ?></small></div>
                </li>
                <li class="context-item">
                  <div><strong>Expected delivery</strong><small><?= escape_html($deliveryJob['scheduled_at'] ?: $order['expected_delivery_at'] ?: 'Not scheduled') ?></small></div>
                </li>
                <?php if ($deliveryJob['picked_up_at']): ?>
                  <li class="context-item"><div><strong>Picked up</strong><small><?= escape_html($deliveryJob['picked_up_at']) ?></small></div></li>
                <?php endif; ?>
                <?php if ($deliveryJob['delivered_at']): ?>
                  <li class="context-item"><div><strong>Delivered</strong><small><?= escape_html($deliveryJob['delivered_at']) ?></small></div></li>
                <?php endif; ?>
              </ul>
            </section>
          <?php endif; ?>

          <?php if ($handoverCode): ?>
            <section class="handover-code" aria-labelledby="handover-code-title">
              <h3 id="handover-code-title">Delivery handover code</h3>
              <p><?= escape_html($handoverCode['body']) ?></p>
            </section>
          <?php elseif ($deliveryJob && $deliveryJob['status'] === 'in_transit'): ?>
            <p class="meta-text">The delivery agent is on the way. Your handover code will appear here.</p>
          <?php endif; ?>

          <div style="margin-top:16px; display:flex; gap:12px;">
            <a class="button button-primary" href="orders.php">Go to orders</a>
          </div>
        </article>
      </div>
    </section>
  </main>
</body>
</html>
