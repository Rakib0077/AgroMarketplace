<?php
require_once __DIR__ . '/../database/app.php';

$search = trim($_GET['q'] ?? '');
$categoryId = (int) ($_GET['category_id'] ?? 0);
$database = app_database();
$listings = $database->searchMarketplaceListings($search, $categoryId);
$categories = $database->getActiveCategories();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Marketplace — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body class="theme-buyer">
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

  <main class="page-shell marketplace-page">
    <section class="context-bar" aria-label="Marketplace context menu">
      <button class="context-btn active" type="button">Browse</button>
      <button class="context-btn" type="button">Saved</button>
      <button class="context-btn" type="button">Watchlist</button>
      <button class="context-btn" type="button">My bids</button>
    </section>

    <div class="marketplace-main">
      <aside class="filters" aria-label="Marketplace filters">
        <h2>Filters</h2>
        <form method="get" action="marketplace.php">
          <div class="filter-group">
            <label for="search-listings">Crop or seller</label>
            <input id="search-listings" type="search" name="q" value="<?= escape_html($search) ?>" placeholder="Search listings">
          </div>
          <div class="filter-group">
            <label for="category-filter">Category</label>
            <select id="category-filter" name="category_id">
              <option value="0">Any category</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="button button-primary" type="submit">Apply filters</button>
        </form>
      </aside>

      <section class="product-grid" aria-label="Buyer marketplace listings">
        <header class="product-grid-header">
          <div>
            <p class="eyebrow">Buyer marketplace</p>
            <h1>Crop marketplace</h1>
          </div>
          <p><?= count($listings) ?> active listings</p>
        </header>

        <div class="grid">
          <?php foreach ($listings as $listing): ?>
            <article class="product-card">
              <img class="product-image" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" loading="lazy">
              <p class="eyebrow"><?= escape_html($listing['category'] ?? 'Produce') ?></p>
              <h3><?= escape_html($listing['title']) ?></h3>
              <p class="meta"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> available • <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT/<?= escape_html($listing['quantity_unit']) ?></p>
              <p class="meta">Seller: <?= escape_html($listing['business_name'] ?: $listing['fullname']) ?></p>
              <?php if ($listing['quality_label']): ?><p class="meta"><?= escape_html($listing['quality_label']) ?></p><?php endif; ?>
              <div class="product-actions">
                <a class="btn btn-small" href="buy-now-detail.php?id=<?= (int) $listing['id'] ?>">Buy now</a>
                <a class="btn btn-small btn-light" href="place-bid-detail.php?id=<?= (int) $listing['id'] ?>">Place bid</a>
              </div>
            </article>
          <?php endforeach; ?>
          <?php if (!$listings): ?><p>No active listings match your search.</p><?php endif; ?>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
