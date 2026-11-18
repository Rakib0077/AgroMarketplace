<?php
require_once __DIR__ . '/../database/app.php';
$admin = require_role(['admin']);
$database = app_database();
$statusOptions = ['draft', 'active', 'paused', 'sold', 'archived'];
$searchValue = $_GET['q'] ?? '';
$filters = [
  'q' => is_string($searchValue) ? trim($searchValue) : '',
  'category' => filter_var($_GET['category'] ?? null, FILTER_VALIDATE_INT) ?: '',
  'district' => is_string($_GET['district'] ?? null) ? trim($_GET['district']) : '',
  'status' => is_string($_GET['status'] ?? null) && in_array($_GET['status'], $statusOptions, true) ? $_GET['status'] : '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $listingId = filter_var($_POST['listing_id'] ?? null, FILTER_VALIDATE_INT);
  $action = $_POST['action'] ?? '';
  if (!$listingId || !in_array($action, ['archive', 'restore'], true)) {
    set_flash('error', 'The listing action is invalid.');
  } else {
    $listingStatus = $database->getListingStatus((int) $listingId);
    if ($listingStatus === null) {
      set_flash('error', 'The listing could not be found.');
    } elseif ($action === 'archive' && $listingStatus !== 'archived') {
      $database->archiveAdminListing((int) $listingId);
      set_flash('success', 'Listing removed from the public marketplace.');
    } elseif ($action === 'restore' && $listingStatus === 'archived') {
      $database->restoreAdminListing((int) $listingId);
      set_flash('success', 'Listing restored.');
    } else {
      set_flash('error', 'That listing action is not available.');
    }
  }

  $queryString = http_build_query($_GET);
  redirect_to('marketplace.php' . ($queryString === '' ? '' : '?' . $queryString));
}

$marketplace = $database->getAdminMarketplace($filters);
$categories = $marketplace['categories'];
$districts = $marketplace['districts'];
$listings = $marketplace['listings'];
$notice = take_flash();
$returnQuery = http_build_query(array_filter($filters, static fn($value) => $value !== ''));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Marketplace — Admin</title>
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

  <main class="page-shell marketplace-page">
    <div class="marketplace-main">
      <form class="filters" method="get" action="marketplace.php" aria-label="Marketplace filters">
        <h2>Filters</h2>
        <div class="filter-group">
          <label for="market-search">Search crops or farmers</label>
          <input id="market-search" name="q" type="search" value="<?= escape_html($filters['q']) ?>" placeholder="Crop, description, or farmer">
        </div>
        <div class="filter-group">
          <label for="market-category">Crop category</label>
          <select id="market-category" name="category">
            <option value="">Any category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= (int) $category['id'] ?>" <?= (string) $filters['category'] === (string) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label for="market-district">Farmer location</label>
          <select id="market-district" name="district">
            <option value="">Any location</option>
            <?php foreach ($districts as $district): ?>
              <option value="<?= escape_html($district) ?>" <?= $filters['district'] === $district ? 'selected' : '' ?>><?= escape_html($district) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="filter-group">
          <label for="market-status">Listing status</label>
          <select id="market-status" name="status">
            <option value="">All statuses</option>
            <?php foreach ($statusOptions as $status): ?>
              <option value="<?= escape_html($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= escape_html(ucfirst($status)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="button button-primary" type="submit">Apply filters</button>
        <a class="button button-secondary" href="marketplace.php">Clear</a>
      </form>

      <section class="product-grid" aria-label="Admin marketplace listings">
        <header class="product-grid-header">
          <div>
            <p class="eyebrow">Admin marketplace</p>
            <h1>Crop marketplace</h1>
          </div>
          <span class="pill pill-muted"><?= count($listings) ?> listings</span>
        </header>
        <?php if ($notice): ?>
          <p class="marketplace-notice <?= ($notice['type'] ?? '') === 'error' ? 'is-error' : '' ?>" role="status"><?= escape_html($notice['message']) ?></p>
        <?php endif; ?>
        <div class="grid">
          <?php foreach ($listings as $listing): ?>
            <?php
              $statusClass = $listing['status'] === 'active' ? 'status-approved' : ($listing['status'] === 'archived' ? 'status-rejected' : 'status-pending');
              $sellerName = $listing['farmer_name'] ?: $listing['farmer_username'];
            ?>
            <article class="product-card">
              <img class="product-image" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" loading="lazy">
              <div class="listing-heading">
                <span class="status-badge <?= $statusClass ?>"><?= escape_html(ucfirst($listing['status'])) ?></span>
                <span class="listing-category"><?= escape_html($listing['category'] ?? 'Produce') ?></span>
              </div>
              <h3><?= escape_html($listing['title']) ?></h3>
              <p class="meta"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> · <?= escape_html($listing['currency']) ?> <?= number_format((float) $listing['price_per_unit'], 2) ?>/<?= escape_html($listing['quantity_unit']) ?></p>
              <p class="meta">Farmer: <a class="seller-profile-link" href="../farmer/profile/profile-public.php?id=<?= (int) $listing['farmer_id'] ?>"><?= escape_html($sellerName) ?></a></p>
              <p class="meta"><?= escape_html($listing['farmer_address'] ?: 'Location not provided') ?></p>
              <div class="product-actions">
                <a class="btn btn-small" href="review-listing.php?id=<?= (int) $listing['id'] ?>">Review listing</a>
                <form method="post" action="marketplace.php<?= $returnQuery === '' ? '' : '?' . escape_html($returnQuery) ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>">
                  <input type="hidden" name="action" value="<?= $listing['status'] === 'archived' ? 'restore' : 'archive' ?>">
                  <button class="btn btn-small <?= $listing['status'] === 'archived' ? 'btn-light' : 'btn-remove' ?>" type="submit"><?= $listing['status'] === 'archived' ? 'Restore' : 'Remove' ?></button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
          <?php if ($listings === []): ?>
            <p class="empty-state">No listings match these filters.</p>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
