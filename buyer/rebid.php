<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Rebid — Buyer</title>
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
        <a class="btn btn-ghost" href="../index.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="page-shell">
    <section class="panel">
      <div class="page-head">
        <div>
          <p class="eyebrow">Buyer bids</p>
          <h1 class="page-title">Rebid</h1>
        </div>
        <a class="button button-secondary" href="bids.php">Back to bids</a>
      </div>

      <div class="content-grid" style="grid-template-columns: 1.4fr 0.8fr; align-items:start;">
        <article class="context-card">
          <img class="product-image" src="../assets/winter wheat.jpeg" alt="Winter wheat" style="width:100%; height:260px; object-fit:cover; border-radius:16px; margin-bottom:16px;">
          <h2>Winter Wheat</h2>
          <p class="meta-text">Sold by <a class="profile-link" href="../farmer/profile/profile-public.php">Jamal Mia</a> • Seller counter: Tk 32,000</p>

          <div class="stats-grid" style="margin-top:16px; padding:0; background:transparent; border:none; box-shadow:none; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px;">
            <div style="padding:14px 14px; border: 1px solid var(--color-border); border-radius:14px; background:linear-gradient(180deg, var(--color-card-bg), var(--color-buyer-light-bg));">
              <h3 style="margin:0; color:var(--color-text-soft); font-size:12px;">Your last offer</h3>
              <p class="stat-value" style="margin-top:6px; font-size:20px;">Tk 31,500</p>
            </div>
            <div style="padding:14px 14px; border: 1px solid var(--color-border); border-radius:14px; background:linear-gradient(180deg, var(--color-card-bg), var(--color-buyer-light-bg));">
              <h3 style="margin:0; color:var(--color-text-soft); font-size:12px;">Time left</h3>
              <p class="stat-value" style="margin-top:6px; font-size:20px;">09h 03m</p>
            </div>
          </div>
        </article>

        <aside class="context-card">
          <div class="section-header">
            <h2>Submit a new bid</h2>
            <span class="pill pill-muted">Counter</span>
          </div>

          <form action="bids.php" method="post">
            <div class="field-group" style="margin-bottom:16px;">
              <span>New bid amount</span>
              <input type="number" value="32000" style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;">
            </div>

            <div class="field-group" style="margin-bottom:16px;">
              <span>Quantity</span>
              <input type="number" value="8" style="width:100%; min-height:46px; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;">
            </div>

            <div class="field-group" style="margin-bottom:16px;">
              <span>Reason</span>
              <textarea rows="4" style="width:100%; border-radius:12px; border: 1px solid var(--color-border); padding:12px 14px; margin-top:6px; box-sizing:border-box;">I can match the seller price for a full shipment with delivery within 7 days.</textarea>
            </div>

            <button class="button button-primary" type="submit">Send rebid</button>
          </form>
        </aside>
      </div>

      <section class="panel" style="margin-top:20px;">
        <div class="section-header" style="margin-bottom:12px;">
          <h2>Bid timeline</h2>
          <span class="pill pill-muted">Competitive</span>
        </div>

        <ul class="timeline-list">
          <li class="timeline-item">
            <span class="dot dot-warn"></span>
            <div>
              <strong>Rashed Ahmed — Tk 32,800</strong>
              <small>Today • 09:56 AM • Highest current offer</small>
            </div>
            <span class="pill pill-muted">Ahead</span>
          </li>
          <li class="timeline-item">
            <span class="dot dot-muted"></span>
            <div>
              <strong>Anika Sultana — Tk 32,350</strong>
              <small>Today • 09:41 AM</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-success"></span>
            <div>
              <strong>You — Tk 31,500</strong>
              <small>Today • 09:15 AM • Your last offer</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-muted"></span>
            <div>
              <strong>Jamil Hossain — Tk 31,100</strong>
              <small>Today • 08:57 AM</small>
            </div>
          </li>
          <li class="timeline-item">
            <span class="dot dot-muted"></span>
            <div>
              <strong>Nazmul Islam — Tk 30,600</strong>
              <small>Today • 08:42 AM</small>
            </div>
          </li>
        </ul>
      </section>
    </section>
  </main>
</body>
</html>
