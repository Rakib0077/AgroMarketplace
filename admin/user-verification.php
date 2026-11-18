<?php
require_once __DIR__ . '/../database/app.php';
$admin = require_role(['admin']);
$database = app_database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
  $action = $_POST['action'] ?? '';
  $target = $userId ? $database->getAdminUserReview((int) $userId) : null;

  if (!$target) {
    set_flash('error', 'The selected user could not be found.');
  } elseif ((int) $target['id'] === (int) $admin['id']) {
    set_flash('error', 'You cannot change your own account status here.');
  } elseif ($action === 'block' && $target['account_status'] === 'active') {
    $database->setAccountStatus((int) $userId, 'suspended');
    set_flash('success', 'User blocked.');
  } elseif ($action === 'unblock' && $target['account_status'] === 'suspended') {
    $database->setAccountStatus((int) $userId, 'active');
    set_flash('success', 'User unblocked.');
  } else {
    set_flash('error', 'That account status change is not available.');
  }

  $queryString = http_build_query($_GET);
  redirect_to('user-verification.php' . ($queryString === '' ? '' : '?' . $queryString));
}

$roleOptions = ['buyer', 'farmer', 'delivery', 'admin'];
$reviewOptions = ['pending', 'approved', 'rejected'];
$accountOptions = ['pending', 'active', 'suspended', 'closed'];
$searchQuery = $_GET['q'] ?? '';
$filters = [
  'q' => is_string($searchQuery) ? trim($searchQuery) : '',
  'role' => $_GET['role'] ?? '',
  'review_status' => $_GET['review_status'] ?? '',
  'account_status' => $_GET['account_status'] ?? '',
  'date_from' => $_GET['date_from'] ?? '',
  'date_to' => $_GET['date_to'] ?? '',
];
$filters['role'] = in_array($filters['role'], $roleOptions, true) ? $filters['role'] : '';
$filters['review_status'] = in_array($filters['review_status'], $reviewOptions, true) ? $filters['review_status'] : '';
$filters['account_status'] = in_array($filters['account_status'], $accountOptions, true) ? $filters['account_status'] : '';
$filters['date_from'] = is_string($filters['date_from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_from']) ? $filters['date_from'] : '';
$filters['date_to'] = is_string($filters['date_to']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_to']) ? $filters['date_to'] : '';

$users = $database->getAdminUsers($filters);
$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Verification Queue — Admin</title>
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
    <div class="admin-layout">
      <aside class="admin-sidebar" aria-label="Verification filters">
        <form class="sidebar-block" method="get" action="user-verification.php">
          <p class="filter-heading">Filter users</p>
          <label class="search-filter-group">
            <span>Search user</span>
            <input type="search" name="q" value="<?= escape_html($filters['q']) ?>" placeholder="Name, username, or email">
          </label>
          <label class="date-filter-group">
            <span>Role</span>
            <select name="role">
              <option value="">All roles</option>
              <?php foreach ($roleOptions as $role): ?>
                <option value="<?= escape_html($role) ?>" <?= $filters['role'] === $role ? 'selected' : '' ?>><?= escape_html(ucfirst($role)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label class="date-filter-group">
            <span>Review status</span>
            <select name="review_status">
              <option value="">All review statuses</option>
              <?php foreach ($reviewOptions as $status): ?>
                <option value="<?= escape_html($status) ?>" <?= $filters['review_status'] === $status ? 'selected' : '' ?>><?= escape_html(ucfirst($status)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label class="date-filter-group">
            <span>Account status</span>
            <select name="account_status">
              <option value="">All account statuses</option>
              <?php foreach ($accountOptions as $status): ?>
                <option value="<?= escape_html($status) ?>" <?= $filters['account_status'] === $status ? 'selected' : '' ?>><?= escape_html(ucfirst($status)) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label class="date-filter-group">
            <span>Registered from</span>
            <input type="date" name="date_from" value="<?= escape_html($filters['date_from']) ?>">
          </label>
          <label class="date-filter-group">
            <span>Registered to</span>
            <input type="date" name="date_to" value="<?= escape_html($filters['date_to']) ?>">
          </label>
          <button class="button button-primary" type="submit">Apply filters</button>
          <a class="button button-secondary" href="user-verification.php">Clear filters</a>
        </form>
      </aside>

      <section class="content-area">
        <section class="panel">
          <div class="page-head">
            <div>
              <p class="eyebrow">Admin workspace</p>
              <h1 class="page-title">User verification queue</h1>
            </div>
            <span class="pill pill-muted"><?= count($users) ?> users</span>
          </div>
        </section>

        <section class="panel">
          <div class="section-header">
            <h2>Accounts to review</h2>
          </div>
          <?php if ($notice): ?>
            <p class="verification-notice <?= ($notice['type'] ?? '') === 'error' ? 'is-error' : '' ?>" role="status"><?= escape_html($notice['message']) ?></p>
          <?php endif; ?>

          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Applicant</th>
                  <th>Role</th>
                  <th>Location</th>
                  <th>Documents</th>
                  <th>Registered</th>
                  <th>Review</th>
                  <th>Account</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                <?php
                  $roleClass = in_array($user['role'], ['buyer', 'farmer', 'delivery', 'admin'], true) ? $user['role'] : 'buyer';
                  $reviewClass = $user['review_status'] === 'approved' ? 'status-approved' : ($user['review_status'] === 'rejected' ? 'status-rejected' : 'status-pending');
                  $accountClass = $user['account_status'] === 'active' ? 'status-approved' : ($user['account_status'] === 'suspended' ? 'status-flagged' : ($user['account_status'] === 'pending' ? 'status-pending' : 'status-rejected'));
                ?>
                <tr>
                  <td>
                    <div class="user-cell">
                      <span class="avatar avatar-<?= escape_html($roleClass) ?>"><?= escape_html(strtoupper(substr($user['fullname'] ?: $user['username'], 0, 1))) ?></span>
                      <div>
                        <a class="profile-link" href="user-profile.php?id=<?= (int) $user['id'] ?>"><strong><?= escape_html($user['fullname'] ?: $user['username']) ?></strong></a>
                        <small><?= escape_html($user['email']) ?></small>
                      </div>
                    </div>
                  </td>
                  <td><?= escape_html(ucfirst($user['role'])) ?></td>
                  <td><?= escape_html($user['address'] ?: 'Not provided') ?></td>
                  <td><?= escape_html($user['documents']) ?></td>
                  <td><?= escape_html(date('Y-m-d', strtotime($user['created_at']))) ?></td>
                  <td><span class="status-badge <?= $reviewClass ?>"><?= escape_html(ucfirst($user['review_status'])) ?></span></td>
                  <td><span class="status-badge <?= $accountClass ?>"><?= escape_html(ucfirst($user['account_status'])) ?></span></td>
                  <td>
                    <div class="verification-actions">
                      <a class="button button-primary" href="user-review.php?id=<?= (int) $user['id'] ?>">Review</a>
                      <?php if ((int) $user['id'] === (int) $admin['id']): ?>
                        <small>Current admin</small>
                      <?php elseif ($user['account_status'] === 'active' || $user['account_status'] === 'suspended'): ?>
                        <form method="post" class="inline-action-form">
                          <?= csrf_field() ?>
                          <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                          <input type="hidden" name="action" value="<?= $user['account_status'] === 'active' ? 'block' : 'unblock' ?>">
                          <button class="button <?= $user['account_status'] === 'active' ? 'button-secondary' : 'button-primary' ?>" type="submit"><?= $user['account_status'] === 'active' ? 'Block' : 'Unblock' ?></button>
                        </form>
                      <?php else: ?>
                        <small><?= $user['account_status'] === 'pending' ? 'Awaiting review' : 'Closed account' ?></small>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if ($users === []): ?>
                  <tr><td colspan="8" class="empty-state">No users match these filters.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </div>
  </main>
</body>
</html>
