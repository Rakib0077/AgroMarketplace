<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['delivery']);
$database = app_database();
$jobId = (int) ($_GET['job'] ?? 0);
$job = $database->getDeliveryJobDetails($jobId, (int) $user['id']);
if (!$job) {
  http_response_code(404);
  exit('Assigned delivery job not found.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? 'advance';
  try {
    $newStatus = $database->updateDeliveryJob($jobId, (int) $user['id'], $action);
    if ($action === 'cancel') {
      set_flash('success', 'Pickup cancelled. The job is available for another delivery agent.');
      redirect_to('jobs.php');
    }

    set_flash('success', $newStatus === 'in_transit' ? 'Delivery started. A handover code was sent to the buyer.' : 'Pickup confirmed.');
    redirect_to($newStatus === 'in_transit' ? 'hand-over.php?job=' . $jobId : 'job-detail.php?job=' . $jobId);
  } catch (Throwable $exception) {
    $error = $exception instanceof RuntimeException ? $exception->getMessage() : 'The delivery status could not be updated.';
  }
}
$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Job Specification — Delivery</title>
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
        <a class="nav-link active" href="jobs.php">Jobs</a>
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
            <p class="eyebrow">Job Specification Manifest</p>
            <h1 class="page-title">Job Details: <?= escape_html($job['order_number']) ?></h1>
          </div>
          <div class="header-actions">
            <a class="button button-primary" href="route-detail.php?job=<?= (int) $jobId ?>">Open Route</a>
            <a class="button button-secondary" href="hand-over.php?job=<?= (int) $jobId ?>">Handover</a>
          </div>
        </div>
        <p class="meta-text">Comprehensive overview of cargo parameters, seller origin, buyer destination, and specific loading bay instructions.</p>
      </section>

      <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
      <section class="stats-grid">
        <div class="stat-card">
          <h3>Order total</h3>
          <p class="stat-value"><?= number_format((float) $job['total_amount'], 2) ?> <?= escape_html($job['currency'] ?? 'BDT') ?></p>
        </div>
        <div class="stat-card">
          <h3>Cargo Type &amp; Weight</h3>
          <p class="stat-value"><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?> <?= escape_html($job['title'] ?? 'Produce') ?></p>
        </div>
        <div class="stat-card">
          <h3>Delivery status</h3>
          <p class="stat-value"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></p>
        </div>
      </section>

      <?php if (in_array($job['status'], ['assigned', 'accepted', 'picked_up'], true)): ?>
        <form class="delivery-job-actions" method="post" action="job-detail.php?job=<?= (int) $jobId ?>">
          <?= csrf_field() ?>
          <button class="button button-primary" type="submit" name="action" value="advance"><?= in_array($job['status'], ['assigned', 'accepted'], true) ? 'Confirm pickup' : 'Start delivery' ?></button>
          <?php if (in_array($job['status'], ['assigned', 'accepted'], true)): ?>
            <button class="button button-danger" type="submit" name="action" value="cancel" onclick="return confirm('Cancel pickup and return this job to the available list?')">Cancel pickup</button>
          <?php endif; ?>
        </form>
      <?php elseif ($job['status'] === 'in_transit'): ?>
        <a class="button button-primary" href="hand-over.php?job=<?= (int) $jobId ?>">Verify handover</a>
      <?php elseif ($job['status'] === 'delivered'): ?>
        <p class="meta-text">Delivery completed and verified.</p>
      <?php endif; ?>

      <section class="content-grid">
        <!-- LOADING POINT DETAILS -->
        <article class="context-card">
          <div class="section-header">
            <h2>Loading Point Details (Pickup Origin)</h2>
            <span class="pill pill-success"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></span>
          </div>
          <div class="mini-metrics" style="grid-template-columns: 1fr; gap: 12px;">
            <div><span>Seller / Producer:</span> <strong><?= escape_html($job['farmer_name']) ?></strong></div>
            <div><span>Pickup Address:</span> <strong><?= escape_html($job['pickup_address']) ?></strong></div>
            <div><span>Contact Person:</span> <strong><?= escape_html($job['farmer_phone']) ?></strong></div>
            <div><span>Loading Notes:</span> <strong><?= escape_html($job['delivery_notes']) ?></strong></div>
          </div>
        </article>

        <!-- DELIVERY POINT DETAILS -->
        <article class="context-card">
          <div class="section-header">
            <h2>Delivery Point Details (Destination)</h2>
            <span class="pill pill-muted"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></span>
          </div>
          <div class="mini-metrics" style="grid-template-columns: 1fr; gap: 12px;">
            <div><span>Buyer / Consignee:</span> <strong><?= escape_html($job['buyer_name']) ?></strong></div>
            <div><span>Delivery Address:</span> <strong><?= escape_html($job['dropoff_address']) ?></strong></div>
            <div><span>Receiving Contact:</span> <strong><?= escape_html($job['buyer_phone']) ?></strong></div>
            <div><span>Payment Status:</span> <strong><?= escape_html($job['payment_status']) ?></strong></div>
          </div>
        </article>
      </section>

      <section class="panel" style="margin-top: 20px;">
        <div class="section-header">
          <h2>Cargo Itemization &amp; Specifications</h2>
        </div>
        <ul class="context-list">
          <li class="context-item">
            <div>
              <strong>Item Description</strong>
              <small><?= escape_html($job['title'] ?? 'Produce') ?></small>
            </div>
            <span class="context-figure"><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?></span>
          </li>
          <li class="context-item">
            <div>
              <strong>Container Seal Identification</strong>
              <small><?= escape_html($job['delivery_notes']) ?></small>
            </div>
            <span class="context-figure">#SEAL-992</span>
          </li>
          <li class="context-item">
            <div>
              <strong>Special Transit Requirements</strong>
              <small>Cover with waterproof tarpaulins during transit</small>
            </div>
            <span class="context-figure warn">Moisture Control</span>
          </li>
        </ul>
      </section>
    </section>
  </main>
</body>
</html>