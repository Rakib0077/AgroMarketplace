<?php
require_once __DIR__ . '/../database/app.php';
$admin = require_role(['admin']);
$database = app_database();
$userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$userId) {
  http_response_code(404);
  exit('User not found.');
}

$user = $database->getAdminUserReview((int) $userId);

if (!$user) {
  http_response_code(404);
  exit('User not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? '';

  if ((int) $user['id'] === (int) $admin['id']) {
    set_flash('error', 'You cannot change your own account here.');
  } elseif ($user['account_status'] === 'pending' && $action === 'approve') {
    $updated = $database->updateAccountStatus((int) $userId, 'pending', 'active') === 1;
    set_flash($updated ? 'success' : 'error', $updated ? 'Registration approved. The user can now sign in.' : 'The registration could not be approved.');
  } elseif ($user['account_status'] === 'pending' && $action === 'delete') {
    try {
      $database->deletePendingUser((int) $userId);
      set_flash('success', 'Pending registration permanently deleted.');
      redirect_to('user-verification.php');
    } catch (Throwable $exception) {
      set_flash('error', 'This account could not be deleted. It may have related marketplace activity.');
    }
  } elseif (in_array($user['account_status'], ['active', 'suspended'], true) && $action === 'block') {
    $updated = $database->updateAccountStatus((int) $userId, 'active', 'suspended') === 1;
    set_flash($updated ? 'success' : 'error', $updated ? 'User blocked.' : 'The account status changed before this action.');
  } elseif ($user['account_status'] === 'suspended' && $action === 'unblock') {
    $updated = $database->updateAccountStatus((int) $userId, 'suspended', 'active') === 1;
    set_flash($updated ? 'success' : 'error', $updated ? 'User unblocked.' : 'The account status changed before this action.');
  } else {
    set_flash('error', 'That account action is not available.');
  }

  redirect_to('user-review.php?id=' . (int) $userId);
}

$verifications = $database->getUserVerifications((int) $userId);
$notice = take_flash();
$displayName = $user['fullname'] ?: $user['username'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Review User — Admin</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Admin navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="user-verification.php">User Verification</a>
        <a class="nav-link" href="commission-manager.php">Commission Manager</a>
        <a class="nav-link" href="disputes.php">Disputes</a>
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
            <p class="eyebrow">Admin workspace</p>
            <h1 class="page-title">Review <?= escape_html($displayName) ?></h1>
          </div>
          <a class="button button-secondary" href="user-verification.php">Back to queue</a>
        </div>
        <?php if ($notice): ?>
          <p class="verification-notice <?= ($notice['type'] ?? '') === 'error' ? 'is-error' : '' ?>" role="status"><?= escape_html($notice['message']) ?></p>
        <?php endif; ?>
      </section>

      <section class="detail-layout single-column-detail">
        <article class="detail-card">
          <h2>Account details</h2>
          <div class="detail-meta">
            <div><span>Role</span><strong><?= escape_html(ucfirst($user['role'])) ?></strong></div>
            <div><span>Account status</span><strong><?= escape_html(ucfirst($user['account_status'])) ?></strong></div>
            <div><span>Email</span><strong><?= escape_html($user['email']) ?></strong></div>
            <div><span>Username</span><strong><?= escape_html($user['username']) ?></strong></div>
            <div><span>Phone</span><strong><?= escape_html($user['phone'] ?: 'Not provided') ?></strong></div>
            <div><span>Location</span><strong><?= escape_html($user['address'] ?: 'Not provided') ?></strong></div>
            <div><span>Registered</span><strong><?= escape_html(date('Y-m-d H:i', strtotime($user['created_at']))) ?></strong></div>
            <?php if ($user['business_name']): ?><div><span>Business</span><strong><?= escape_html($user['business_name']) ?></strong></div><?php endif; ?>
            <?php if ($user['preferred_buying_category']): ?><div><span>Buying category</span><strong><?= escape_html($user['preferred_buying_category']) ?></strong></div><?php endif; ?>
            <?php if ($user['typical_order_volume']): ?><div><span>Typical order volume</span><strong><?= escape_html($user['typical_order_volume']) ?></strong></div><?php endif; ?>
            <?php if ($user['land_area']): ?><div><span>Land area</span><strong><?= escape_html($user['land_area']) ?></strong></div><?php endif; ?>
            <?php if ($user['primary_crops']): ?><div><span>Primary crops</span><strong><?= escape_html($user['primary_crops']) ?></strong></div><?php endif; ?>
            <?php if ($user['vehicle_capacity']): ?><div><span>Vehicle capacity</span><strong><?= escape_html($user['vehicle_capacity']) ?></strong></div><?php endif; ?>
            <?php if ($user['vehicle_license_plate']): ?><div><span>Vehicle plate</span><strong><?= escape_html($user['vehicle_license_plate']) ?></strong></div><?php endif; ?>
          </div>
        </article>

        <article class="detail-card">
          <div class="section-header">
            <h2>Verification documents</h2>
            <span class="pill pill-muted"><?= count($verifications) ?> records</span>
          </div>
          <?php if ($verifications): ?>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr><th>Document</th><th>Reference / details</th><th>Status</th><th>Submitted</th><th>Review notes</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($verifications as $verification): ?>
                    <?php $statusClass = $verification['status'] === 'approved' ? 'status-approved' : ($verification['status'] === 'rejected' ? 'status-rejected' : 'status-pending'); ?>
                    <tr>
                      <td><?= escape_html($verification['document_type']) ?></td>
                      <td><?= escape_html($verification['document_reference'] ?: 'Not provided') ?></td>
                      <td><span class="status-badge <?= $statusClass ?>"><?= escape_html(ucfirst($verification['status'])) ?></span></td>
                      <td><?= escape_html(date('Y-m-d', strtotime($verification['submitted_at']))) ?></td>
                      <td><?= escape_html($verification['review_notes'] ?: '—') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php elseif ($user['verification_documents']): ?>
            <p class="detail-copy"><?= escape_html($user['verification_documents']) ?></p>
          <?php else: ?>
            <p class="empty-state">No verification documents have been submitted.</p>
          <?php endif; ?>
        </article>

        <article class="detail-card">
          <h2><?= $user['account_status'] === 'pending' ? 'Registration decision' : 'Account access' ?></h2>
          <?php if ((int) $user['id'] === (int) $admin['id']): ?>
            <p class="empty-state">You cannot change your own account.</p>
          <?php elseif ($user['account_status'] === 'pending'): ?>
            <p class="meta-text">This new registration cannot sign in until approved. You can approve it or permanently delete the pending account.</p>
            <div class="detail-actions">
              <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="approve">
                <button class="button button-primary" type="submit">Approve registration</button>
              </form>
              <form method="post" onsubmit="return confirm('Permanently delete this pending account? This cannot be undone.');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <button class="button button-danger" type="submit">Permanently delete</button>
              </form>
            </div>
          <?php elseif ($user['account_status'] === 'active' || $user['account_status'] === 'suspended'): ?>
            <p class="meta-text">This user is already registered. Approval is not required; manage account access here.</p>
            <form method="post" class="detail-actions">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="<?= $user['account_status'] === 'active' ? 'block' : 'unblock' ?>">
              <button class="button <?= $user['account_status'] === 'active' ? 'button-danger' : 'button-primary' ?>" type="submit"><?= $user['account_status'] === 'active' ? 'Block user' : 'Unblock user' ?></button>
            </form>
          <?php else: ?>
            <p class="empty-state">This account is closed and cannot be changed here.</p>
          <?php endif; ?>
        </article>
      </section>
    </section>
  </main>
</body>
</html>