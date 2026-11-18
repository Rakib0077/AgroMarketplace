<?php
require_once __DIR__ . '/../../database/app.php';

$viewer = current_user();
$isAdmin = ($viewer['role'] ?? '') === 'admin';
$farmerId = (int) ($_GET['id'] ?? 0);
$farmer = app_database()->getFarmerPublicProfile($farmerId, $isAdmin);
if (!$farmer) {
  http_response_code(404);
  exit('Farmer profile not found.');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Farmer Profile — Public</title>
  <link rel="stylesheet" href="../../colors.css">
  <link rel="stylesheet" href="../../header.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="<?= $isAdmin ? 'theme-admin' : 'theme-farmer' ?>">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../../index.php">AgroMarketplace</a>
      <?php if ($isAdmin): ?>
      <nav class="nav" aria-label="Admin navigation">
        <a class="nav-link" href="../../admin/index.php">Home</a>
        <a class="nav-link" href="../../admin/marketplace.php">Marketplace</a>
        <a class="nav-link" href="../../admin/user-verification.php">User Verification</a>
        <a class="nav-link" href="../../admin/commission-manager.php">Commission Manager</a>
        <a class="nav-link" href="../../admin/disputes.php">Disputes</a>
        <a class="nav-link" href="../../admin/chat.php">Chat</a>
      </nav>
      <?php else: ?>
      <nav class="nav" aria-label="Farmer navigation">
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link" href="profile-self.php">Profile</a>
        <a class="nav-link" href="../marketplace.php">Marketplace</a>
        <a class="nav-link" href="../create-listing.php">Listing</a>
        <a class="nav-link" href="../bids.php">Bids</a>
        <a class="nav-link" href="../inventory.php">Inventory</a>
        <a class="nav-link" href="../sales-ledger.php">Sales Ledger</a>
        <a class="nav-link" href="../chat.php">Chat</a>
      </nav>
      <?php endif; ?>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="profile-shell">
    <section class="profile-card">
      <div class="profile-hero">
        <div class="profile-identity">
          <img class="profile-avatar-img" src="../../assets/guest-avatar.png" alt="Ayesha Rahman Profile Picture">
          <div>
            <h1 class="profile-name"><?= escape_html($farmer['fullname'] ?: $farmer['username']) ?></h1>
            <p class="profile-role">Farmer</p>
          </div>
        </div>
        <div class="hero-status">
          <?php if ($isAdmin): ?>
            <span class="status-badge <?= $farmer['account_status'] === 'active' ? 'status-active' : 'status-blocked' ?>"><?= escape_html(ucfirst($farmer['account_status'])) ?> account</span>
          <?php else: ?>
            <span class="status-badge status-active">Verified seller</span>
            <a class="profile-button primary" href="../chat.php" style="margin-top:8px;">Send Message</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="profile-body">
        <div class="profile-section">
          <h2 class="section-title">Public information</h2>
          <div class="profile-grid">
            <div class="profile-detail"><span>Business</span><strong><?= escape_html($farmer['business_name']) ?></strong></div>
            <div class="profile-detail"><span>Role</span><strong>Farmer</strong></div>
            <div class="profile-detail"><span>Location</span><strong><?= escape_html($farmer['address']) ?></strong></div>
            <div class="profile-detail"><span>Joined</span><strong><?= escape_html(substr($farmer['created_at'], 0, 10)) ?></strong></div>
            <div class="profile-detail"><span>Specialty</span><strong><?= escape_html($farmer['primary_crops']) ?></strong></div>
            <div class="profile-detail"><span>Buying preferences</span><strong><?= escape_html($farmer['market_preferences']) ?></strong></div>
          </div>

          <p class="profile-about"><?= escape_html($farmer['profile_description']) ?></p>
        </div>

        <div class="profile-stack">
          <div class="profile-section">
            <h2 class="section-title">Highlights</h2>
            <ul class="profile-list">
              <li class="profile-item"><div><strong>Crop quality</strong><br><small>Grade A harvests</small></div><span class="status-badge status-active">Excellent</span></li>
              <li class="profile-item"><div><strong>On-time delivery</strong><br><small>98% handoff rate</small></div><span class="status-badge status-active">Reliable</span></li>
              <li class="profile-item"><div><strong>Buyer rating</strong><br><small>4.9 from recent trades</small></div><span class="status-badge status-active">Top rated</span></li>
            </ul>
          </div>

          <div class="profile-section">
            <h2 class="section-title">Contact</h2>
            <div class="metric-box"><strong><?= escape_html($farmer['email']) ?></strong><span>Email for crop orders and inquiries</span></div>
            <div class="metric-box" style="margin-top:12px;"><strong><?= escape_html($farmer['phone']) ?></strong><span>Phone for market coordination</span></div>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>