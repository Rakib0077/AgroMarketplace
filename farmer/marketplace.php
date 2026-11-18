<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$search = strtolower(trim($_GET['q'] ?? ''));
$categoryId = (int) ($_GET['category_id'] ?? 0);
$district = trim($_GET['district'] ?? '');
$maxPriceText = trim($_GET['max_price'] ?? '');
$maxPrice = is_numeric($maxPriceText) ? (float) $maxPriceText : null;
$quality = strtolower(trim($_GET['quality'] ?? ''));

$database = app_database();
$allListings = $database->getMarketplaceListings();
$listings = [];
$districts = [];

foreach ($allListings as $listing) {
  $addressParts = explode(',', $listing['address'] ?? '');
  $listingDistrict = trim($addressParts[0]);
  $listing['district'] = $listingDistrict;

  if ($listingDistrict !== '' && !in_array($listingDistrict, $districts, true)) {
    $districts[] = $listingDistrict;
  }

  $seller = $listing['business_name'] ?: $listing['fullname'];
  $searchText = strtolower($listing['title'] . ' ' . ($listing['category'] ?? '') . ' ' . $seller);
  if ($search !== '' && strpos($searchText, $search) === false) {
    continue;
  }
  if ($categoryId > 0 && (int) $listing['category_id'] !== $categoryId) {
    continue;
  }
  if ($district !== '' && $listingDistrict !== $district) {
    continue;
  }
  if ($maxPrice !== null && ($maxPrice < 0 || (float) $listing['price_per_unit'] > $maxPrice)) {
    continue;
  }
  if ($quality !== '' && strpos(strtolower($listing['quality_label'] ?? ''), $quality) === false) {
    continue;
  }

  $listings[] = $listing;
}

sort($districts);
$categories = $database->getActiveCategories();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Marketplace — Farmer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="farmer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body class="theme-farmer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Farmer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile/profile-self.php">Profile</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
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

  <main class="page-shell marketplace-page">
    <section class="context-bar" aria-label="Marketplace context menu">
      <button class="context-btn active" type="button">Browse</button>
      <button class="context-btn" type="button">My listings</button>
      <button class="context-btn" type="button">Offers</button>
      <button class="context-btn" type="button">Insights</button>
    </section>

    <div class="marketplace-main">
      <aside class="filters" aria-label="Marketplace filters">
        <h2>Filters</h2>
        <form method="get" action="marketplace.php">
          <div class="filter-group">
            <label for="market-search">Crop or seller</label>
            <input id="market-search" name="q" type="search" value="<?= escape_html($search) ?>" placeholder="Search listings">
          </div>
          <div class="filter-group">
            <label for="market-category">Crop category</label>
            <select id="market-category" name="category_id">
              <option value="0">Any crop</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label for="market-district">District</label>
            <select id="market-district" name="district">
              <option value="">Any district</option>
              <?php foreach ($districts as $districtOption): ?>
                <option value="<?= escape_html($districtOption) ?>" <?= $district === $districtOption ? 'selected' : '' ?>><?= escape_html($districtOption) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label for="market-max-price">Max price (BDT/unit)</label>
            <input id="market-max-price" name="max_price" type="number" min="0" step="0.01" value="<?= escape_html($maxPriceText) ?>">
          </div>
          <div class="filter-group">
            <label for="market-quality">Quality contains</label>
            <input id="market-quality" name="quality" type="search" value="<?= escape_html($quality) ?>" placeholder="Grade A, organic">
          </div>
          <button class="button button-primary" type="submit">Apply filters</button>
          <a class="button button-secondary" href="marketplace.php">Clear</a>
        </form>
      </aside>

      <section class="product-grid" aria-label="Farmer marketplace listings">
        <header class="product-grid-header">
          <div>
            <p class="eyebrow">Farmer marketplace</p>
            <h1>Crop marketplace</h1>
          </div>
          <div class="header-actions">
            <p><?= count($listings) ?> active listings</p>
            <a class="btn btn-primary" href="create-listing.php">New listing</a>
          </div>
        </header>

        <div class="grid">
          <?php foreach ($listings as $listing): ?>
            <article class="product-card">
              <img class="product-image" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" loading="lazy">
              <p class="eyebrow"><?= escape_html($listing['category'] ?? 'Produce') ?></p>
              <h3><?= escape_html($listing['title']) ?></h3>
              <p class="meta"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> · <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT/unit</p>
              <p class="meta">Seller: <?= escape_html($listing['business_name'] ?: $listing['fullname']) ?> · <?= escape_html($listing['district']) ?></p>
              <?php if ($listing['quality_label']): ?><p class="meta"><?= escape_html($listing['quality_label']) ?></p><?php endif; ?>
              <div class="product-actions">
                <?php if ((int) $listing['farmer_id'] === (int) $user['id']): ?>
                  <a class="btn btn-small" href="edit-listing.php?id=<?= (int) $listing['id'] ?>">Edit listing</a>
                <?php endif; ?>
                <a class="btn btn-small btn-light" href="view-listing.php?id=<?= (int) $listing['id'] ?>">View</a>
              </div>
            </article>
          <?php endforeach; ?>
          <?php if (!$listings): ?><p>No active listings match these filters.</p><?php endif; ?>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
