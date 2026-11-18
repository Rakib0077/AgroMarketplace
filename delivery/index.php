<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['delivery']);
$dashboard = app_database()->getDeliveryDashboard((int) $user['id']);
$jobs = $dashboard['jobs'];
$activeCount = $dashboard['active_count'];
$availableCount = $dashboard['available_count'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Delivery Dashboard — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="delivery.css">
</head>
<body class="theme-delivery">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Delivery navigation">
        <a class="nav-link active" href="index.php">Home</a>
        <a class="nav-link" href="jobs.php">Jobs</a>
          <a class="nav-link" href="history.php">History</a>
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
            <p class="eyebrow">Driver Control Center</p>
            <h1 class="page-title">Active Logistics Workspace</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">Set Status: Available</button>
            <button class="button button-secondary" type="button">Emergency Route Alert</button>
          </div>
        </div>
        <p class="meta-text">Real-time tracking for assigned agricultural produce shipments, active pickup windows, and OTP completions.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card">
          <h3>Active Deliveries</h3>
          <p class="stat-value"><?= $activeCount ?> Shipments</p>
        </div>
        <div class="stat-card">
          <h3>Available Jobs</h3>
          <p class="stat-value"><?= $availableCount ?></p>
        </div>
        <div class="stat-card">
          <h3>Assigned Jobs</h3>
          <p class="stat-value"><?= count($jobs) ?></p>
        </div>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Current Shipment Queue</h2>
            <span class="pill pill-success"><?= $activeCount ?> Active</span>
          </div>
          <ul class="context-list">
            <?php foreach ($jobs as $job): ?>
              <li class="context-item">
                <div><strong><?= escape_html($job['order_number']) ?> — <?= escape_html($job['title'] ?? 'Produce') ?> (<?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?>)</strong><small>Origin: <?= escape_html($job['pickup_address']) ?> → Destination: <?= escape_html($job['dropoff_address']) ?></small></div>
                <a class="context-figure" href="job-detail.php?job=<?= (int) $job['id'] ?>"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></a>
              </li>
            <?php endforeach; ?>
            <?php if (!$jobs): ?><li>No deliveries assigned. <a href="jobs.php">Browse available jobs</a>.</li><?php endif; ?>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Vehicle & Inspection Status</h2>
            <span class="pill pill-muted">Updated 5m ago</span>
          </div>
          <div class="mini-metrics">
            <div><span>Vehicle Tag</span><strong><?= escape_html($user['vehicle_license_plate'] ?? 'Not provided') ?></strong></div>
            <div><span>Cargo Capacity</span><strong><?= escape_html($user['vehicle_capacity'] ?? 'Not provided') ?></strong></div>
            <div><span>Driver</span><strong><?= escape_html($user['fullname'] ?: $user['username']) ?></strong></div>
            <div><span>Open marketplace jobs</span><strong><?= $availableCount ?></strong></div>
          </div>
        </article>
      </section>
    </section>
  </main>
</body>
</html>