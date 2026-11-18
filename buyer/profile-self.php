<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $database->updateBuyerProfile((int) $user['id'], [
    'business_name' => trim($_POST['business_name'] ?? ''),
    'profile_description' => trim($_POST['profile_description'] ?? ''),
    'primary_crops' => trim($_POST['primary_crops'] ?? ''),
    'market_preferences' => trim($_POST['market_preferences'] ?? ''),
  ]);
  $message = 'Profile changes saved.';
}
$profile = $database->getBuyerProfile((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Farmer Profile — Self</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="theme-farmer">
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

  <main class="profile-shell">
    <section class="profile-card">
      <div class="profile-hero">
        <div class="profile-identity">
          <div class="profile-avatar-wrapper">
            <img class="profile-avatar-img" src="../assets/guest-avatar.png" alt="Account profile picture">
          </div>
          <div>
            <h1 class="profile-name"><?= escape_html($profile['fullname'] ?: $profile['username']) ?></h1>
            <p class="profile-role">Buyer</p>
          </div>
        </div>
        <span class="status-badge status-active">Profile active</span>
      </div>

      <div class="profile-body">
        <div class="profile-section">
          <h2 class="section-title">Account settings</h2>

          <div class="profile-grid">
            <div class="profile-detail"><span>Full name</span><strong><?= escape_html($profile['fullname']) ?></strong></div>
            <div class="profile-detail"><span>User ID</span><strong><?= (int) $profile['id'] ?></strong></div>
            <div class="profile-detail"><span>Email</span><strong><?= escape_html($profile['email']) ?></strong></div>
            <div class="profile-detail"><span>Phone</span><strong><?= escape_html($profile['phone']) ?></strong></div>
            <div class="profile-detail"><span>Location</span><strong><?= escape_html($profile['address']) ?></strong></div>
            <div class="profile-detail"><span>Business</span><strong><?= escape_html($profile['business_name']) ?></strong></div>
          </div>

          <div class="profile-actions" style="margin-top:16px;">
              <button class="profile-button primary" type="submit" form="buyer-profile-form">Save changes</button>
            <button class="profile-button secondary" type="button">Change password</button>
            <button class="profile-button secondary" type="button">Upload photo</button>
          </div>
        </div>

        <div class="profile-stack">
          <div class="profile-section">
            <h2 class="section-title">Edit profile details</h2>
            <?php if ($message !== ''): ?><p role="status"><?= escape_html($message) ?></p><?php endif; ?>
            <form id="buyer-profile-form" class="profile-form" method="post" action="profile-self.php" style="display:grid; gap:14px;">
              <?= csrf_field() ?>
              <label style="display:grid; gap:6px; color:var(--profile-muted); font-weight:700; font-size:13px;">
                <span>Business name</span>
                <input type="text" name="business_name" value="<?= escape_html($profile['business_name']) ?>" style="padding:12px 14px; border:1px solid var(--profile-border); border-radius:12px; background:#fff;">
              </label>

              <label style="display:grid; gap:6px; color:var(--profile-muted); font-weight:700; font-size:13px;">
                <span>Profile description</span>
                <textarea name="profile_description" rows="4" style="padding:12px 14px; border:1px solid var(--profile-border); border-radius:12px; background:#fff; resize:vertical;"><?= escape_html($profile['profile_description']) ?></textarea>
              </label>

              <label style="display:grid; gap:6px; color:var(--profile-muted); font-weight:700; font-size:13px;">
                <span>Primary crop focus</span>
                <input type="text" name="primary_crops" value="<?= escape_html($profile['primary_crops']) ?>" style="padding:12px 14px; border:1px solid var(--profile-border); border-radius:12px; background:#fff;">
              </label>

              <label style="display:grid; gap:6px; color:var(--profile-muted); font-weight:700; font-size:13px;">
                <span>Market preferences</span>
                <input type="text" name="market_preferences" value="<?= escape_html($profile['market_preferences']) ?>" style="padding:12px 14px; border:1px solid var(--profile-border); border-radius:12px; background:#fff;">
              </label>

              <button class="profile-button secondary" type="submit">Apply Changes</button>
            </form>
          </div>

          <div class="profile-section">
            <h2 class="section-title">Account actions</h2>
            <ul class="profile-list">
              <li class="profile-item"><div><strong>Verification</strong><br><small>Business identity approved</small></div><span class="status-badge status-active">Verified</span></li>
              <li class="profile-item"><div><strong>Delivery readiness</strong><br><small>Pickup and local shipment enabled</small></div><span class="status-badge status-active">Ready</span></li>
            </ul>
            <div class="profile-actions" style="margin-top:16px;">
              <a class="profile-button secondary" href="profile-public.php">View public profile</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>