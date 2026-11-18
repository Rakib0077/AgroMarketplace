<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$listingId = (int) ($_GET['id'] ?? 0);
$listing = app_database()->getFarmerListing($listingId);
if (!$listing) {
  http_response_code(404);
  exit('Listing not found.');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>View Crop Listing — Farmer Dashboard</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="farmer.css">
</head>
<body class="theme-farmer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Farmer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile/profile-self.php">Profile</a>
        <a class="nav-link active" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="create-listing.php">Listing</a>
        <a class="nav-link" href="bids.php">Bids</a>
        <a class="nav-link" href="inventory.php">Inventory</a>
        <a class="nav-link" href="sales-ledger.php">Sales Ledger</a>
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
            <p class="eyebrow">Marketplace Offer</p>
            <h1 class="page-title"><?= escape_html($listing['title']) ?></h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="marketplace.php">Back to Marketplace</a>
          </div>
        </div>
        <p class="meta-text">Listing #<?= (int) $listing['id'] ?> · Posted by <?= escape_html($listing['business_name'] ?: $listing['fullname']) ?></p>
      </section>

      <section class="content-grid">
        <!-- Crop Details Section -->
        <article class="context-card" style="grid-column: span 2;">
          <div style="margin-bottom: 16px;">
            <img src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" style="width:100%; max-height:320px; object-fit:cover; border-radius:12px; border:1px solid var(--color-border);">
          </div>

          <div class="section-header">
            <h2>Listing Overview</h2>
            <span class="pill pill-success"><?= escape_html(ucfirst($listing['status'])) ?></span>
          </div>

          <div class="mini-metrics" style="margin-top:16px; margin-bottom:20px;">
            <div><span>Price per <?= escape_html($listing['quantity_unit']) ?></span><strong><?= number_format((float) $listing['price_per_unit'], 2) ?> BDT</strong></div>
            <div><span>Available Quantity</span><strong><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></strong></div>
            <div><span>Quality Grade</span><strong><?= escape_html($listing['quality_label'] ?? 'Not specified') ?></strong></div>
            <div><span>Est. Total Value</span><strong><?= number_format((float) $listing['quantity_available'] * (float) $listing['price_per_unit'], 2) ?> BDT</strong></div>
          </div>

          <div style="display:grid; gap:12px; margin-bottom:20px;">
            <div>
              <strong style="font-size:14px; color:var(--color-muted);">Location:</strong>
              <p style="margin:4px 0 0 0; font-weight:700;"><?= escape_html($listing['address']) ?></p>
            </div>
            <div>
              <strong style="font-size:14px; color:var(--color-muted);">Description:</strong>
              <p style="margin:4px 0 0 0; line-height:1.6;"><?= escape_html($listing['description'] ?: $listing['delivery_notes']) ?></p>
            </div>
          </div>
        </article>

        <!-- Sidebar: Seller Profile & Actions -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          <article class="context-card">
            <div class="section-header">
              <h2>Seller Information</h2>
            </div>
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
              <img src="../assets/guest-avatar.png" alt="Seller Avatar" style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:1px solid var(--color-border);">
              <div>
                <a href="profile/profile-public.php?id=<?= (int) $listing['farmer_id'] ?>" style="text-decoration:none; color:var(--color-brand); font-weight:700; font-size:16px;"><?= escape_html($listing['fullname']) ?></a>
                <div style="font-size:12px; color:var(--color-muted);"> <?= escape_html($listing['business_name']) ?></div>
              </div>
            </div>

            <ul class="context-list" style="margin-bottom:16px;">
              <li class="context-item">
                <div><strong>Verification</strong><br><small>National NID Verified</small></div>
                <span class="pill pill-success">Verified</span>
              </li>
              <li class="context-item">
                <div><strong>Seller Rating</strong><br><small>14 completed orders</small></div>
                <span class="context-figure">4.9 / 5.0</span>
              </li>
              <li class="context-item"><div><strong>Location</strong><br><small><?= escape_html($listing['address']) ?></small></div></li>
            </ul>

            <div style="display:flex; flex-direction:column; gap:8px;">
              <?php if ((int) $listing['farmer_id'] === (int) $user['id']): ?>
                <a class="button button-secondary" href="edit-listing.php?id=<?= (int) $listing['id'] ?>" style="text-align:center;">Edit listing</a>
              <?php else: ?>
                <a class="button button-secondary" href="chat.php" style="text-align:center;">Contact Seller</a>
              <?php endif; ?>
            </div>
        </div>
      </section>
    </section>
  </main>
</body>
</html>