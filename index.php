<?php
require_once __DIR__ . '/database/app.php';
redirect_authenticated_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>AgroMarketplace — Home</title>
  <link rel="stylesheet" href="colors.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="index.php">
        AgroMarketplace
      </a>
      <nav class="nav">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="catalogue/catalogue.php">Catalogue</a>
        <a class="nav-link" href="how-it-works.php">How It Works</a>
        <a class="nav-link" href="contact.php">Contact</a>
      </nav>
      <div class="site-header-actions">
        <a class="account-link" href="auth/login.php" aria-label="Guest account">
          <img class="avatar" alt="guest avatar" src="assets/guest-avatar.png">
          <span class="account-type">Guest</span>
        </a>
        <a class="btn btn-ghost" href="auth/login.php">Login</a>
      </div>
    </div>
  </header>

  <main>
    <section class="hero" role="region" aria-label="Hero">
      <div class="hero-inner">
        <h1 class="hero-title">Direct Farm-to-Buyer Trading</h1>
        <p class="hero-lead">Compare current market rates, explore verified commodities, and connect with trusted sellers instantly.</p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="auth/login.php">Login</a>
          <a class="btn btn-secondary" href="auth/register.php">Register</a>
        </div>
      </div>
    </section>

    <section class="features" aria-label="Key features">
      <div class="features-inner">
        <article class="feature-card">
          <h3 class="feature-card-title">Live Price Feed</h3>
          <p class="feature-card-desc">Built for traders and farmers</p>
        </article>
        <article class="feature-card">
          <h3 class="feature-card-title">Verified Farmers</h3>
          <p class="feature-card-desc">Built for traders and farmers</p>
        </article>
        <article class="feature-card">
          <h3 class="feature-card-title">Secure Escrow</h3>
          <p class="feature-card-desc">Built for traders and farmers</p>
        </article>
      </div>
    </section>

    <section class="featured" aria-label="Featured Commodities">
      <h2 class="section-title">Featured Commodities</h2>
      <div class="commodities-grid">
        <article class="commodity-card">
          <img class="product-image" src="assets/nazirshail rice.jpeg" alt="Nazirshail rice" loading="lazy">
          <h3 class="commodity-card-title">Nazirshail Rice</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/winter wheat.jpeg" alt="Winter wheat" loading="lazy">
          <h3 class="commodity-card-title">Winter Wheat</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/yellow corn.jpeg" alt="Yellow corn" loading="lazy">
          <h3 class="commodity-card-title">Yellow Corn</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/chili.jpeg" alt="Red lentils" loading="lazy">
          <h3 class="commodity-card-title">Assorted Peppers</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/organic potatoes.jpeg" alt="Organic potatoes" loading="lazy">
          <h3 class="commodity-card-title">Organic Potatoes</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/basmati rice.jpg" alt="Premium basmati" loading="lazy">
          <h3 class="commodity-card-title">Premium Basmati</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/sunflower seeds.jpg" alt="Sunflower seeds" loading="lazy">
          <h3 class="commodity-card-title">Sunflower Seeds</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>

        <article class="commodity-card">
          <img class="product-image" src="assets/crop watering.jpeg" alt="Green mustard" loading="lazy">
          <h3 class="commodity-card-title">Green Mustard</h3>
          <p class="commodity-card-meta">Verified seller • Live price</p>
          <a class="btn btn-small" href="catalogue/catalogue.php">Explore</a>
        </article>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="site-footer-inner">
      <p>© AgroMarketplace</p>
    </div>
  </footer>
</body>
</html>
