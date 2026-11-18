<?php
require_once __DIR__ . '/../database/app.php';
$user = require_role(['delivery']);
$jobId = filter_var($_GET['job'] ?? null, FILTER_VALIDATE_INT);
$jobs = app_database()->getAssignedDeliveryJobs((int) $user['id']);
if (!$jobId && $jobs) {
  $jobId = (int) $jobs[0]['id'];
}
$job = null;
foreach ($jobs as $assignedJob) {
  if ((int) $assignedJob['id'] === (int) $jobId) {
    $job = $assignedJob;
    break;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Route Detail — Delivery</title>
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
          <a class="nav-link" href="history.php">History</a>
        <a class="nav-link active" href="route-detail.php">Route Detail</a>
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

      <!-- JOB SELECTOR CONTROLLER -->
      <section class="panel route-selector-panel">
        <div class="route-selector">
          <div class="route-selector-copy">
            <label class="route-selector-label" for="job-select">Select active job route</label>
            <form class="route-selector-form" method="get" action="route-detail.php">
              <select id="job-select" class="delivery-select" name="job" onchange="this.form.submit()" <?= !$jobs ? 'disabled' : '' ?>>
                <?php foreach ($jobs as $assignedJob): ?>
                  <option value="<?= (int) $assignedJob['id'] ?>" <?= (int) $jobId === (int) $assignedJob['id'] ? 'selected' : '' ?>><?= escape_html($assignedJob['order_number']) ?> — <?= escape_html($assignedJob['title'] ?? 'Produce') ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </div>
          <div class="route-selector-actions">
            <?php if ($job): ?>
              <a class="button button-secondary" href="job-detail.php?job=<?= (int) $job['id'] ?>">View Full Job</a>
              <a class="button button-primary" href="hand-over.php?job=<?= (int) $job['id'] ?>">Handover</a>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="panel" style="margin-top: 16px;">
        <div class="page-head">
          <div>
            <p class="eyebrow">Active Navigation Manifest</p>
            <h1 class="page-title"><?= $job ? 'Route · ' . escape_html($job['order_number']) : 'No assigned route' ?></h1>
          </div>
        </div>
        <?php if ($job): ?><p class="meta-text">Pickup: <strong><?= escape_html($job['pickup_address'] ?: 'Not provided') ?></strong> → Delivery: <strong><?= escape_html($job['dropoff_address'] ?: 'Not provided') ?></strong></p><?php endif; ?>
      </section>

      <section class="stats-grid">
        <?php if ($job): ?>
          <div class="stat-card"><h3>Cargo</h3><p class="stat-value"><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?></p></div>
          <div class="stat-card"><h3>Status</h3><p class="stat-value"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></p></div>
          <div class="stat-card"><h3>Scheduled</h3><p class="stat-value"><?= escape_html($job['scheduled_at'] ?: 'Not scheduled') ?></p></div>
        <?php endif; ?>
      </section>

      <?php if ($job): ?>
      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Pickup and delivery</h2>
            <span class="pill pill-muted"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></span>
          </div>
          <ul class="context-list">
            <li class="context-item">
              <div><strong>Pickup</strong><small><?= escape_html($job['pickup_address'] ?: 'Address not provided') ?></small></div>
              <span class="context-figure">Origin</span>
            </li>
            <li class="context-item">
              <div><strong>Delivery</strong><small><?= escape_html($job['dropoff_address'] ?: 'Address not provided') ?></small></div>
              <span class="context-figure">Destination</span>
            </li>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Consignment</h2>
          </div>
          <div class="mini-metrics">
            <div><span>Order</span><strong><?= escape_html($job['order_number']) ?></strong></div>
            <div><span>Commodity</span><strong><?= escape_html($job['title'] ?? 'Produce') ?></strong></div>
            <div><span>Quantity</span><strong><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?></strong></div>
          </div>
        </article>
      </section>
      <?php else: ?>
        <section class="panel"><p class="empty-state">You have no assigned routes. Browse available jobs to accept a delivery.</p><a class="button button-primary" href="jobs.php">Browse jobs</a></section>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>