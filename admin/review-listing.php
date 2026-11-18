<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);
$listingId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$listingId) {
  http_response_code(404);
  exit('Listing not found.');
}

$listing = app_database()->getAdminListingReview((int) $listingId);
if (!$listing) {
  http_response_code(404);
  exit('Listing not found.');
}
$farmerName = $listing['farmer_name'] ?: $listing['farmer_username'];
$statusClass = $listing['status'] === 'active' ? 'status-approved' : ($listing['status'] === 'archived' ? 'status-rejected' : 'status-pending');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Review Listing — Admin</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
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
            <h1 class="page-title">Review Crop Listing</h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="marketplace.php">Back to Marketplace</a>
          </div>
        </div>
        <p class="meta-text">Review listing details and inspect the seller's public profile.</p>
      </section>

      <section class="detail-layout">
        <article class="detail-card">
          <div class="section-header">
            <h2><?= escape_html($listing['title']) ?></h2>
            <span class="status-badge <?= $statusClass ?>"><?= escape_html(ucfirst($listing['status'])) ?></span>
          </div>

          <img class="product-image" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" style="height: 280px; margin-bottom: 16px;">

          <div class="detail-meta">
            <div><span>Listing ID</span><strong>#<?= (int) $listing['id'] ?></strong></div>
            <div><span>Category</span><strong><?= escape_html($listing['category'] ?? 'Produce') ?></strong></div>
            <div><span>Submitted</span><strong><?= escape_html(date('Y-m-d H:i', strtotime($listing['created_at']))) ?></strong></div>
            <div><span>Quantity</span><strong><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></strong></div>
            <div><span>Unit price</span><strong><?= escape_html($listing['currency']) ?> <?= number_format((float) $listing['price_per_unit'], 2) ?> / <?= escape_html($listing['quantity_unit']) ?></strong></div>
            <div><span>Origin</span><strong><?= escape_html($listing['farmer_address'] ?: 'Not provided') ?></strong></div>
            <?php if ($listing['harvest_date']): ?><div><span>Harvest date</span><strong><?= escape_html($listing['harvest_date']) ?></strong></div><?php endif; ?>
            <?php if ($listing['quality_label']): ?><div><span>Quality</span><strong><?= escape_html($listing['quality_label']) ?></strong></div><?php endif; ?>
          </div>

          <div class="detail-copy">
            <strong>Listing description</strong>
            <p style="margin-top: 6px; margin-bottom: 0;"><?= nl2br(escape_html($listing['description'] ?: 'No description provided.')) ?></p>
            <?php if ($listing['delivery_notes']): ?><p><strong>Delivery notes</strong><br><?= nl2br(escape_html($listing['delivery_notes'])) ?></p><?php endif; ?>
          </div>
        </article>

        <aside class="detail-card">
          <div class="section-header">
            <h2>Seller Profile</h2>
          </div>

          <div class="user-cell" style="margin-bottom: 16px;">
            <span class="avatar avatar-farmer"><?= escape_html(strtoupper(substr($farmerName, 0, 1))) ?></span>
            <div>
              <a class="profile-link" href="../farmer/profile/profile-public.php?id=<?= (int) $listing['farmer_id'] ?>"><strong><?= escape_html($farmerName) ?></strong></a>
              <small><?= escape_html($listing['farmer_email']) ?></small>
            </div>
          </div>

          <div class="mini-metrics">
            <div><span>Account status</span><strong><?= escape_html(ucfirst($listing['farmer_status'])) ?></strong></div>
            <div><span>Business</span><strong><?= escape_html($listing['business_name'] ?: 'Not provided') ?></strong></div>
            <div><span>Primary crops</span><strong><?= escape_html($listing['primary_crops'] ?: 'Not provided') ?></strong></div>
            <div><span>Verification documents</span><strong><?= escape_html($listing['verification_documents'] ?: 'Not provided') ?></strong></div>
          </div>
        </aside>
      </section>
    </section>
  </main>
</body>
</html>