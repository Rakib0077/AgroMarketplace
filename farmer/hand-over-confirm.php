<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Confirm Handover — Farmer</title>
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
          <p class="eyebrow">Order dispatch</p>
          <h1 class="page-title">Confirm order & hand over</h1>
        </div>
      </div>

      <div class="content-grid">
        <article class="context-card">
          <h2>Confirm readiness</h2>
          <p class="meta-text">Mark the order as ready and hand over to the assigned delivery agent.</p>
          <div style="margin-top:12px; display:flex; gap:12px;">
            <button class="button button-primary" onclick="confirmHandover()">Confirm & Hand over</button>
            <a class="button button-secondary" href="../delivery/hand-over.php">View delivery handover</a>
          </div>
        </article>
      </div>
    </section>
  </main>

  <script>
    function confirmHandover(){
      alert('Order confirmed and marked for pickup (dummy)');
    }
  </script>
</body>
</html>
