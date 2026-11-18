<?php
require_once __DIR__ . '/../database/app.php';
redirect_authenticated_user();

$listing = app_database()->getCatalogueListing((int) ($_GET['id'] ?? 0));
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
  <title>Nazirshail Rice Details — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="catalogue.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <style>
    .details-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
    }

    .detail-card-large {
      min-height: auto;
    }

    .detail-product-img {
      width: 100%;
      height: 320px;
      object-fit: cover;
      border-radius: 14px;
      border: 1px solid var(--color-border);
    }

    .metrics-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 12px;
      background: var(--color-buyer-light-bg);
      padding: 16px;
      border-radius: 12px;
      margin: 16px 0;
    }

    .metric-item span {
      display: block;
      font-size: 12px;
      color: var(--color-muted);
      font-weight: 700;
    }

    .metric-item strong {
      font-size: 16px;
      color: var(--color-buyer-dark);
    }

    .guest-cta-box {
      border: 1px solid var(--color-buyer-primary);
      background: var(--color-buyer-light-bg);
      border-radius: 14px;
      padding: 20px;
      margin-top: 24px;
    }

    .guest-cta-box h3 {
      margin: 0 0 8px 0;
      color: var(--color-buyer-primary);
    }

    .seller-info {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .seller-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid var(--color-border);
    }

    @media (max-width: 820px) {
      .details-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav">
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link active" href="catalogue.php">Catalogue</a>
        <a class="nav-link" href="../how-it-works.php">How It Works</a>
        <a class="nav-link" href="../contact.php">Contact</a>
      </nav>
      <div class="site-header-actions">
        <a class="account-link" href="../auth/login.php" aria-label="Guest account">
          <img class="avatar" alt="guest avatar" src="../assets/guest-avatar.png">
          <span class="account-type">Guest</span>
        </a>
        <a class="btn btn-ghost" href="../auth/login.php">Login</a>
      </div>
    </div>
  </header>

  <main class="catalogue-main">
    <header class="catalogue-header">
      <div>
        <p class="eyebrow">Guest view · <?= escape_html($listing['category'] ?? 'Produce') ?></p>
        <h1><?= escape_html($listing['title']) ?></h1>
      </div>
      <div class="catalogue-toolbar">
        <a class="btn btn-ghost" href="catalogue.php">← Back to Catalogue</a>
      </div>
    </header>

    <div class="details-grid">
      <!-- Main Content Block -->
      <article class="catalogue-card detail-card-large">
        <img class="detail-product-img" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>">

        <div class="catalogue-card-body">
          <div style="display:flex; justify-content:space-between; align-items:center;">
              <h3><?= escape_html($listing['title']) ?></h3>
              <span class="badge"><?= escape_html($listing['quality_label'] ?? ($listing['category'] ?? 'Produce')) ?></span>
          </div>
            <p class="meta">Location: <?= escape_html($listing['address']) ?></p>
        </div>

        <div class="metrics-row">
          <div class="metric-item">
            <span>Price / <?= escape_html($listing['quantity_unit']) ?></span>
            <strong><?= number_format((float) $listing['price_per_unit'], 2) ?> BDT</strong>
          </div>
          <div class="metric-item">
            <span>Available</span>
            <strong><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?></strong>
          </div>
          <div class="metric-item">
            <span>Status</span>
            <strong><?= escape_html(ucfirst($listing['status'])) ?></strong>
          </div>
          <div class="metric-item">
            <span>Harvest date</span>
            <strong><?= escape_html($listing['harvest_date'] ?? 'Not provided') ?></strong>
          </div>
        </div>

        <div>
          <h4>Produce Overview</h4>
          <p class="meta" style="line-height:1.6; color:var(--color-text);"><?= escape_html($listing['description'] ?: $listing['delivery_notes']) ?></p>
        </div>

        <!-- Guest Registration Box -->
        <div class="guest-cta-box">
          <h3>Register to Buy or Place a Bid</h3>
          <p class="meta" style="margin-bottom:16px;">
            To submit purchase orders, place direct bids, or communicate with the producer, please log in or register a free buyer account.
          </p>
          <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a class="btn btn-small" href="../auth/register.php">Register to Purchase</a>
            <a class="btn btn-ghost" href="../auth/login.php">Log In to Account</a>
          </div>
        </div>
      </article>

      <!-- Sidebar Block -->
      <aside style="display:flex; flex-direction:column; gap:24px;">
        <article class="catalogue-card">
          <h3>Producer Information</h3>
          <div class="seller-info">
            <img class="seller-avatar" src="../assets/guest-avatar.png" alt="Seller Avatar">
            <div>
              <strong style="display:block; font-size:15px;"><?= escape_html($listing['business_name'] ?: $listing['fullname']) ?></strong>
              <span class="meta" style="font-size:12px;"><?= escape_html($listing['address']) ?></span>
            </div>
          </div>
          <span class="badge">Verified Seller</span>
          <p class="meta" style="font-size:13px; margin-top:12px;">
            <a href="../farmer/profile/profile-public.php?id=<?= (int) $listing['farmer_id'] ?>">View farmer profile</a>
          </p>
        </article>

        <article class="catalogue-card">
          <h3>Marketplace Assurance</h3>
          <ul class="meta" style="padding-left:18px; margin:0; line-height:1.8;">
            <li><strong>Escrow Protection:</strong> Payment held securely until inspection.</li>
            <li><strong>Quality Guaranteed:</strong> Verified batch inspection metrics.</li>
            <li><strong>Direct Sourcing:</strong> Direct transaction with registered producers.</li>
          </ul>
        </article>
      </aside>
    </div>
  </main>
</body>
</html>
