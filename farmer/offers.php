<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buyer Offers — Farmer</title>
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
    <section class="panel">
      <div class="page-head">
        <div>
          <p class="eyebrow">Incoming offers</p>
          <h1 class="page-title">Manage buyer offers</h1>
        </div>
      </div>

      <div class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Offers</h2>
          </div>

          <ul class="context-list">
            <li class="context-item">
              <div>
                <strong>Rahim Uddin — 10 MT</strong>
                <small>Offer: Tk 67,500 — 2 hours ago</small>
              </div>
              <div style="display:flex; gap:8px;">
                <button class="button button-primary" onclick="action('accept','Rahim Uddin')">Accept</button>
                <button class="button button-secondary" onclick="action('reject','Rahim Uddin')">Reject</button>
              </div>
            </li>

            <li class="context-item">
              <div>
                <strong>Jamal Mia — 5 MT</strong>
                <small>Offer: Tk 31,800 — 4 hours ago</small>
              </div>
              <div style="display:flex; gap:8px;">
                <button class="button button-primary" onclick="action('accept','Jamal Mia')">Accept</button>
                <button class="button button-secondary" onclick="action('reject','Jamal Mia')">Reject</button>
              </div>
            </li>
          </ul>
        </article>
      </div>
    </section>
  </main>

  <script>
    function action(type, name) {
      alert(`${type === 'accept' ? 'Accepted' : 'Rejected'} offer from ${name} (dummy)`);
    }
  </script>
</body>
</html>
