<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Transaction Details — Farmer</title>
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

  <main class="page-shell">
    <section class="content-area">
      <section class="panel">
        <div class="page-head">
          <div>
            <p class="eyebrow">Farmer workspace</p>
            <h1 class="page-title">Transaction details</h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="sales-ledger.php">Back to ledger</a>
          </div>
        </div>
        <p class="meta-text">This page is a sample detail view for a clicked transaction and can be loaded dynamically with PHP later.</p>
      </section>

      <section class="detail-layout">
        <article class="detail-card summary-card" aria-label="Sale summary">
          <div class="section-header">
            <h2>Sale summary</h2>
            <span class="pill pill-success">Settled</span>
          </div>

          <p class="meta-text">Day 01 — Rice sale</p>

          <div class="summary-list">
            <div>
              <span>Buyer</span>
              <a class="profile-link" href="profile/profile-public.php"><strong>Rahim Uddin</strong></a>
            </div>
            <div>
              <span>Commodity</span>
              <strong>Premium Nazirshail Rice</strong>
            </div>
            <div>
              <span>Quantity</span>
              <strong>10 MT</strong>
            </div>
            <div>
              <span>Unit price</span>
              <strong>Tk 68,000/MT</strong>
            </div>
            <div>
              <span>Total amount</span>
              <strong>Tk 680,000</strong>
            </div>
            <div>
              <span>Payment date</span>
              <strong>2026-08-01</strong>
            </div>
          </div>

          <div class="detail-actions">
            <button class="button button-primary" type="button">Print receipt</button>
            <button class="button button-secondary" type="button">Export PDF</button>
          </div>
        </article>

        <aside class="detail-card status-card">
          <div class="section-header">
            <h2>Delivery status</h2>
            <span class="pill pill-muted">Complete</span>
          </div>
          <ul class="context-list">
            <li class="context-item">
              <div>
                <strong>Pickup cleared</strong>
                <small>Warehouse confirmed</small>
              </div>
              <span class="context-figure">OK</span>
            </li>
            <li class="context-item">
              <div>
                <strong>OTP verified</strong>
                <small>Payment authorization complete</small>
              </div>
              <span class="context-figure">Verified</span>
            </li>
            <li class="context-item">
              <div>
                <strong>Settlement</strong>
                <small>Escrow released to farmer account</small>
              </div>
              <span class="context-figure warn">Paid</span>
            </li>
          </ul>
        </aside>
      </section>
    </section>
  </main>
</body>
</html>
