<?php
require_once __DIR__ . '/../database/app.php';
redirect_authenticated_user();

$search = trim($_GET['q'] ?? '');
$listings = app_database()->searchCatalogueListings($search);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Catalogue — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="catalogue.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav">
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link" href="catalogue.php">Catalogue</a>
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
        <p class="eyebrow">Product catalogue</p>
        <h1>Explore commodities</h1>
      </div>
      <form class="catalogue-toolbar" method="get" action="catalogue.php">
        <input type="search" name="q" value="<?= escape_html($search) ?>" placeholder="Search crop or category" aria-label="Search catalog">
        <button class="btn btn-ghost" type="submit">Search</button>
      </form>
    </header>

    <div class="catalogue-grid">
      <?php foreach ($listings as $listing): ?>
        <article class="catalogue-card">
          <img class="product-image" src="<?= escape_html(listing_image_url($listing)) ?>" alt="<?= escape_html($listing['title']) ?>" loading="lazy">
          <div class="catalogue-card-body">
            <span class="badge"><?= escape_html($listing['category'] ?? 'Produce') ?></span>
            <h3><?= escape_html($listing['title']) ?></h3>
            <p class="meta"><?= number_format((float) $listing['quantity_available'], 3) ?> <?= escape_html($listing['quantity_unit']) ?> available · <?= number_format((float) $listing['price_per_unit'], 2) ?> BDT/<?= escape_html($listing['quantity_unit']) ?></p>
            <?php if ($listing['quality_label']): ?><p class="meta"><?= escape_html($listing['quality_label']) ?></p><?php endif; ?>
          </div>
          <a class="btn btn-small" href="catalogue-details.php?id=<?= (int) $listing['id'] ?>">View details</a>
        </article>
      <?php endforeach; ?>
      <?php if (!$listings): ?><p>No active listings found.</p><?php endif; ?>
    </div>
  </main>
</body>
</html>
