<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['delivery']);
$database = app_database();
$jobId = filter_var($_GET['job'] ?? $_POST['job_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$error = '';
$notice = take_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? '';
  try {
    if ($action === 'verify_otp') {
      $otp = trim(is_string($_POST['otp'] ?? null) ? $_POST['otp'] : '');
      $database->verifyDeliveryHandover($jobId, (int) $user['id'], $otp);
      set_flash('success', 'Handover verified. The order is marked delivered.');
      redirect_to('hand-over.php?job=' . $jobId);
    }

    if ($action === 'report_damage') {
      $status = $database->getDeliveryJobStatus($jobId, (int) $user['id']);
      $damageType = trim(is_string($_POST['damage_type'] ?? null) ? $_POST['damage_type'] : '');
      $affectedQuantity = trim(is_string($_POST['affected_quantity'] ?? null) ? $_POST['affected_quantity'] : '');
      $description = trim(is_string($_POST['description'] ?? null) ? $_POST['description'] : '');
      $photo = $_FILES['photo'] ?? null;
      if (!in_array($status, ['assigned', 'accepted', 'picked_up', 'in_transit'], true)) {
        throw new RuntimeException('Damage can only be reported for your active jobs.');
      }
      if ($damageType === '' || strlen($damageType) > 80 || $description === '' || strlen($description) > 5000 || strlen($affectedQuantity) > 120) {
        throw new RuntimeException('Enter a damage type and a description of up to 5,000 characters.');
      }
      if (!$photo || $photo['error'] !== UPLOAD_ERR_OK || $photo['size'] > 5 * 1024 * 1024 || !is_uploaded_file($photo['tmp_name'])) {
        throw new RuntimeException('Choose a photo smaller than 5 MB.');
      }
      $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($photo['tmp_name']);
      if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        throw new RuntimeException('Upload a JPG, PNG, or WebP image.');
      }
      $imageData = file_get_contents($photo['tmp_name']);
      if ($imageData === false) {
        throw new RuntimeException('The photo could not be read. Please try again.');
      }
      $database->saveDeliveryDamageReport($jobId, (int) $user['id'], [
        'damage_type' => $damageType,
        'affected_quantity' => $affectedQuantity ?: null,
        'description' => $description,
      ], $mimeType, $imageData);
      set_flash('success', 'Damage report and photo saved.');
      redirect_to('hand-over.php?job=' . $jobId);
    }
    throw new RuntimeException('Choose a valid delivery action.');
  } catch (Throwable $exception) {
    $error = $exception instanceof RuntimeException ? $exception->getMessage() : 'The delivery update could not be saved.';
  }
}

$jobs = $database->getDeliveryHandoverJobs((int) $user['id']);
$job = null;
foreach ($jobs as $assignedJob) {
  if ((int) $assignedJob['id'] === $jobId) {
    $job = $assignedJob;
    break;
  }
}
if (!$job && $jobs) {
  $job = $jobs[0];
  $jobId = (int) $job['id'];
}
$reports = [];
if ($job) {
  $reports = $database->getDeliveryDamageReports($jobId);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Order Handover — AgroMarketplace Delivery</title>
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
        <a class="nav-link" href="route-detail.php">Route Detail</a>
        <a class="nav-link active" href="hand-over.php">Order Handover</a>
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
        <div class="page-head handover-head">
          <div>
            <p class="eyebrow">Delivery handover</p>
            <h1 class="page-title">Handover and damage report</h1>
          </div>
          <?php if ($jobs): ?>
            <form class="handover-job-picker" method="get" action="hand-over.php">
              <label for="handover-job-select">Assigned job</label>
              <select id="handover-job-select" class="delivery-select" name="job" onchange="this.form.submit()">
                <?php foreach ($jobs as $assignedJob): ?>
                  <option value="<?= (int) $assignedJob['id'] ?>" <?= (int) $jobId === (int) $assignedJob['id'] ? 'selected' : '' ?>><?= escape_html($assignedJob['order_number']) ?> — <?= escape_html($assignedJob['title'] ?? 'Produce') ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          <?php endif; ?>
        </div>
        <?php if ($notice): ?><p class="meta-text" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
        <?php if ($error !== ''): ?><p class="meta-text" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      </section>

      <?php if ($job): ?>
        <section class="stats-grid">
          <div class="stat-card"><h3>Order</h3><p class="stat-value"><?= escape_html($job['order_number']) ?></p></div>
          <div class="stat-card"><h3>Cargo</h3><p class="stat-value"><?= number_format((float) $job['quantity'], 3) ?> <?= escape_html($job['quantity_unit']) ?></p></div>
          <div class="stat-card"><h3>Status</h3><p class="stat-value"><?= escape_html(ucwords(str_replace('_', ' ', $job['status']))) ?></p></div>
        </section>

        <section class="content-grid">
          <article class="context-card">
            <div class="section-header"><h2>Verify buyer handover</h2></div>
            <?php if ($job['status'] === 'in_transit'): ?>
              <p class="meta-text">Ask the buyer for the delivery code shown on their order page.</p>
              <form class="delivery-form" method="post" action="hand-over.php?job=<?= (int) $jobId ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="job_id" value="<?= (int) $jobId ?>">
                <input type="hidden" name="action" value="verify_otp">
                <label for="handover-otp">Buyer handover code</label>
                <input id="handover-otp" class="delivery-input" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required>
                <button class="button button-primary" type="submit">Confirm delivery</button>
              </form>
            <?php else: ?>
              <p class="meta-text"><?= $job['status'] === 'delivered' ? 'This order has already been delivered.' : 'Start delivery from the job details page before verifying handover.' ?></p>
              <?php if ($job['status'] !== 'delivered'): ?><a class="button button-secondary" href="job-detail.php?job=<?= (int) $jobId ?>">Open job details</a><?php endif; ?>
            <?php endif; ?>
          </article>

          <article class="context-card">
            <div class="section-header"><h2>Report damaged goods</h2></div>
            <form class="delivery-form" method="post" action="hand-over.php?job=<?= (int) $jobId ?>" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <input type="hidden" name="job_id" value="<?= (int) $jobId ?>">
              <input type="hidden" name="action" value="report_damage">
              <label for="damage-type">Damage type</label>
              <input id="damage-type" class="delivery-input" name="damage_type" maxlength="80" required>
              <label for="affected-quantity">Affected quantity (optional)</label>
              <input id="affected-quantity" class="delivery-input" name="affected_quantity" maxlength="120">
              <label for="damage-description">Description</label>
              <textarea id="damage-description" class="delivery-input" name="description" maxlength="5000" rows="4" required></textarea>
              <label for="damage-photo">Photo (JPG, PNG, or WebP, max 5 MB)</label>
              <input id="damage-photo" class="delivery-file" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
              <button class="button button-primary" type="submit" <?= $job['status'] === 'delivered' ? 'disabled' : '' ?>>Save damage report</button>
            </form>
          </article>
        </section>
        <?php if ($reports): ?>
          <section class="panel">
            <div class="section-header"><h2>Saved damage reports</h2></div>
            <ul class="context-list">
              <?php foreach ($reports as $report): ?>
                <li class="context-item">
                  <div><strong><?= escape_html($report['damage_type']) ?><?= $report['affected_quantity'] ? ' · ' . escape_html($report['affected_quantity']) : '' ?></strong><small><?= nl2br(escape_html($report['description'])) ?><br><?= escape_html(date('Y-m-d H:i', strtotime($report['created_at']))) ?></small></div>
                  <?php if ($report['photo_id']): ?><a class="button button-secondary" href="damage-photo.php?id=<?= (int) $report['photo_id'] ?>" target="_blank" rel="noopener">View photo</a><?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      <?php else: ?>
        <section class="panel"><p class="empty-state">No delivery jobs are assigned to you.</p><a class="button button-primary" href="jobs.php">Browse available jobs</a></section>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
