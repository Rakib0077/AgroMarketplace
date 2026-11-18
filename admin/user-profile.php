<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);

$userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$userId) {
  http_response_code(404);
  exit('User profile not found.');
}

$profile = app_database()->getAdminUserProfile((int) $userId);
if (!$profile) {
  http_response_code(404);
  exit('User profile not found.');
}

$details = [
  'Role' => ucfirst($profile['role']),
  'Account status' => ucfirst($profile['account_status']),
  'Username' => $profile['username'],
  'Email' => $profile['email'],
  'Phone' => $profile['phone'] ?: 'Not provided',
  'Location' => $profile['address'] ?: 'Not provided',
  'Joined' => date('Y-m-d', strtotime($profile['created_at'])),
];

if ($profile['business_name']) {
  $details['Business name'] = $profile['business_name'];
}
if ($profile['role'] === 'buyer') {
  $details['Preferred category'] = $profile['preferred_buying_category'] ?: 'Not provided';
  $details['Typical order volume'] = $profile['typical_order_volume'] ?: 'Not provided';
  $details['Market preferences'] = $profile['market_preferences'] ?: 'Not provided';
} elseif ($profile['role'] === 'farmer') {
  $details['Land area'] = $profile['land_area'] ?: 'Not provided';
  $details['Primary crops'] = $profile['primary_crops'] ?: 'Not provided';
  $details['Verification documents'] = $profile['verification_documents'] ?: 'Not provided';
  $details['Market preferences'] = $profile['market_preferences'] ?: 'Not provided';
} elseif ($profile['role'] === 'delivery') {
  $details['Vehicle capacity'] = $profile['vehicle_capacity'] ?: 'Not provided';
  $details['Vehicle license plate'] = $profile['vehicle_license_plate'] ?: 'Not provided';
}

$displayName = $profile['fullname'] ?: $profile['username'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= escape_html($displayName) ?> — Admin Profile</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="theme-admin">
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
            <h1 class="page-title"><?= escape_html($displayName) ?></h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="user-review.php?id=<?= (int) $profile['id'] ?>">Review account</a>
            <a class="button button-secondary" href="user-verification.php">Back to queue</a>
          </div>
        </div>
        <?php if ($profile['profile_description']): ?>
          <p class="meta-text"><?= nl2br(escape_html($profile['profile_description'])) ?></p>
        <?php endif; ?>
      </section>

      <article class="detail-card">
        <h2>Public profile</h2>
        <div class="detail-meta">
          <?php foreach ($details as $label => $value): ?>
            <div><span><?= escape_html($label) ?></span><strong><?= escape_html($value) ?></strong></div>
          <?php endforeach; ?>
        </div>
      </article>
    </section>
  </main>
</body>
</html>