<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['delivery']);
$database = app_database();
$error = '';
$notice = take_flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $jobId = filter_var($_POST['job_id'] ?? null, FILTER_VALIDATE_INT);
  try {
    $database->claimDeliveryJob((int) ($jobId ?: 0), (int) $user['id']);
    set_flash('success', 'Job assigned. You can now message the buyer and farmer.');
    redirect_to('jobs.php');
  } catch (Throwable $exception) {
    $error = $exception instanceof RuntimeException ? $exception->getMessage() : 'This job could not be assigned.';
  }
}

$jobLists = $database->getDeliveryJobs((int) $user['id']);
$availableJobs = $jobLists['available'];
$myJobs = $jobLists['assigned'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Jobs Overview — AgroMarketplace Delivery</title>
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
            <p class="eyebrow">Dispatch & Assignments</p>
            <h1 class="page-title">Marketplace Cargo Jobs</h1>
          </div>
        </div>
        <p class="meta-text">Review available dispatch offers or inspect full loading and delivery specifications for your accepted routes.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card">
          <h3>Available Offers</h3>
          <p class="stat-value"><?= count($availableJobs) ?> Jobs</p>
        </div>
        <div class="stat-card">
          <h3>Accepted / Active</h3>
          <p class="stat-value"><?= count($myJobs) ?> Jobs</p>
        </div>
        <div class="stat-card">
          <h3>Total Projected Earnings</h3>
          <p class="stat-value"><?= number_format(array_sum(array_map(static fn ($job) => (float) $job['total_amount'], $myJobs)), 2) ?> BDT</p>
        </div>
      </section>

      <section class="content-grid">
        <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
        <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
        <article class="context-card">
          <div class="section-header"><h2>Your jobs</h2><span class="pill pill-success"><?= count($myJobs) ?> assigned</span></div>
          <ul class="context-list">
            <?php foreach ($myJobs as $job): ?>
              <li class="context-item">
                <div><strong><?= escape_html($job['order_number']) ?> · <?= escape_html($job['title'] ?? 'Produce') ?></strong><small><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?> · <?= escape_html($job['status']) ?><br>Pickup: <?= escape_html($job['pickup_address']) ?><br>Drop-off: <?= escape_html($job['dropoff_address']) ?></small></div>
                <a class="button button-secondary" href="job-detail.php?job=<?= (int) $job['id'] ?>">Details</a>
              </li>
            <?php endforeach; ?>
            <?php if (!$myJobs): ?><li>No delivery jobs assigned to you.</li><?php endif; ?>
          </ul>
        </article>
        <article class="context-card">
          <div class="section-header"><h2>Available jobs</h2><span class="pill pill-muted"><?= count($availableJobs) ?> open</span></div>
          <ul class="context-list">
            <?php foreach ($availableJobs as $job): ?>
              <li class="context-item">
                <div><strong><?= escape_html($job['order_number']) ?> · <?= escape_html($job['title'] ?? 'Produce') ?></strong><small><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?><br>Pickup: <?= escape_html($job['pickup_address']) ?><br>Drop-off: <?= escape_html($job['dropoff_address']) ?></small></div>
                <form method="post" action="jobs.php">
                  <?= csrf_field() ?>
                  <input type="hidden" name="job_id" value="<?= (int) $job['id'] ?>">
                  <button class="button button-primary" type="submit">Accept job</button>
                </form>
              </li>
            <?php endforeach; ?>
            <?php if (!$availableJobs): ?><li>No delivery jobs are available.</li><?php endif; ?>
          </ul>
        </article>
      </section>
    </section>
  </main>
</body>
</html>