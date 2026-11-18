<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['delivery']);
$jobs = app_database()->getDeliveryHistory((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Delivery History — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="delivery.css">
</head>
<body class="theme-delivery">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Delivery navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="jobs.php">Jobs</a>
        <a class="nav-link active" href="history.php">History</a>
        <a class="nav-link" href="route-detail.php">Route Detail</a>
        <a class="nav-link" href="hand-over.php">Order Handover</a>
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
            <p class="eyebrow">Completed deliveries</p>
            <h1 class="page-title">Delivery history</h1>
          </div>
          <span class="pill pill-success"><?= count($jobs) ?> delivered</span>
        </div>
        <p class="meta-text">Completed jobs assigned to your account.</p>
      </section>

      <?php if ($jobs): ?>
        <section class="panel">
          <ul class="context-list">
            <?php foreach ($jobs as $job): ?>
              <li class="context-item">
                <div>
                  <strong><?= escape_html($job['order_number']) ?> · <?= escape_html($job['title'] ?? 'Produce') ?></strong>
                  <small><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?> · Buyer: <?= escape_html($job['buyer_name']) ?><br>Pickup: <?= escape_html($job['pickup_address'] ?: 'Address not provided') ?><br>Drop-off: <?= escape_html($job['dropoff_address'] ?: 'Address not provided') ?></small>
                </div>
                <span class="context-figure">Delivered <?= escape_html($job['delivered_at'] ? date('Y-m-d H:i', strtotime($job['delivered_at'])) : 'date unavailable') ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      <?php else: ?>
        <section class="panel"><p class="empty-state">No completed deliveries yet.</p></section>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>