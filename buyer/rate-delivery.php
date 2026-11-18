<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Rate Delivery — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
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

  <main class="page-shell">
    <section class="panel">
      <div class="page-head">
        <div>
          <p class="eyebrow">Order feedback</p>
          <h1 class="page-title">Rate the farmer & product</h1>
        </div>
      </div>

      <div class="content-grid" style="grid-template-columns: 1fr; gap:16px;">
        <article class="context-card">
          <h2>How was the delivery?</h2>
          <form action="#" method="post">
            <label style="display:block; margin-bottom:12px;">
              <strong>Farmer rating</strong>
              <div style="margin-top:8px;">
                <select name="farmer-rating">
                  <option>5 — Excellent</option>
                  <option>4 — Good</option>
                  <option>3 — Average</option>
                  <option>2 — Poor</option>
                  <option>1 — Very poor</option>
                </select>
              </div>
            </label>

            <label style="display:block; margin-bottom:12px;">
              <strong>Product rating</strong>
              <div style="margin-top:8px;">
                <select name="product-rating">
                  <option>5 — Excellent</option>
                  <option>4 — Good</option>
                  <option>3 — Average</option>
                  <option>2 — Poor</option>
                  <option>1 — Very poor</option>
                </select>
              </div>
            </label>

            <label style="display:block; margin-bottom:12px;">
              <strong>Comments</strong>
              <textarea name="comments" rows="4" style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--color-border);"></textarea>
            </label>

            <div style="display:flex; gap:12px;">
              <button class="button button-primary" type="submit">Submit rating</button>
              <a class="button button-secondary" href="orders.php">Skip</a>
            </div>
          </form>
        </article>
      </div>
    </section>
  </main>
</body>
</html>
