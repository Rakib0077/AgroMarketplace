<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$summary = app_database()->getFarmerDashboardSummary((int) $user['id']);
$activeListings = $summary['active_listings'];
$pendingBids = $summary['pending_bids'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Farmer Dashboard — AgroMarketplace</title>
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
            <h1 class="page-title">Harvest overview</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">Quick harvest upload</button>
            <button class="button button-secondary" type="button">Inventory alerts</button>
          </div>
        </div>
        <p class="meta-text">Manage listings, track produce movement, and stay current with buyer demand.</p>
      </section>
      <section class="stats-grid">
        <div class="stat-card"><h3>30-Day Sales</h3><p class="stat-value"><?= number_format((float) $summary['sales'], 2) ?> BDT</p></div>
        <div class="stat-card"><h3>30-Day Orders</h3><p class="stat-value"><?= (int) $summary['recent_orders'] ?></p></div>
        <div class="stat-card"><h3>Payment Pending</h3><p class="stat-value"><?= number_format((float) $summary['pending'], 2) ?> BDT</p></div>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Today’s priorities</h2>
            <span class="pill pill-success"><?= $activeListings ?> active listings</span>
          </div>
          <ul class="context-list">
            <li class="context-item"><div><strong>Incoming bids</strong><small>Offers awaiting your review</small></div><a class="context-figure" href="bids.php"><?= $pendingBids ?> bids</a></li>
            <li class="context-item"><div><strong>Active listings</strong><small>Currently visible in the marketplace</small></div><a class="context-figure" href="create-listing.php"><?= $activeListings ?> listings</a></li>
            <li class="context-item"><div><strong>Recent sales</strong><small>Orders created in the last 30 days</small></div><a class="context-figure" href="sales-ledger.php"><?= (int) $summary['recent_orders'] ?> orders</a></li>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Demand watch</h2>
            <span class="pill pill-muted">This week</span>
          </div>
          <div class="mini-metrics">
            <div><span>Rice</span><strong>+18%</strong></div>
            <div><span>Wheat</span><strong>+9%</strong></div>
            <div><span>Lentils</span><strong>+14%</strong></div>
            <div><span>Best lead time</span><strong>3 days</strong></div>
          </div>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Harvest calendar</h2>
            <a class="button button-secondary" href="harvest-calendar.php">Open calendar</a>
          </div>
          <div id="events-mini" style="display:flex; flex-direction:column; gap:10px; margin-bottom:12px;"></div>

          <form id="miniEventForm" class="event-form" onsubmit="return miniAddEvent();">
            <input id="mini-title" placeholder="Event title" required>
            <input id="mini-date" type="date" required>
            <button class="button button-primary" type="submit">Add</button>
          </form>
        </article>
      </section>
    </section>
  </main>
  <script>
    // Mini calendar on dashboard — shares storage with harvest-calendar.php
    const MINI_STORAGE = 'harvestEvents_v1';
    const miniEventsEl = document.getElementById('events-mini');
    const miniForm = document.getElementById('miniEventForm');

    function loadMini(){
      try { return JSON.parse(localStorage.getItem(MINI_STORAGE) || '[]'); } catch(e){return []}
    }
    function saveMini(list){ localStorage.setItem(MINI_STORAGE, JSON.stringify(list)); }

    function renderMini(){
      miniEventsEl.innerHTML = '';
      const list = loadMini();
      if (!list.length) { miniEventsEl.innerHTML = '<div class="meta-text">No upcoming harvests.</div>'; return; }
      list.slice().reverse().forEach((ev, idx)=>{
        const realIdx = list.length - 1 - idx;
        const div = document.createElement('div');
        div.className = 'event-item';
        div.style.display = 'flex';
        div.style.justifyContent = 'space-between';
        div.innerHTML = `<div><strong>${ev.title}</strong><div style="font-size:12px;color:var(--color-muted);">${ev.date}</div></div><div><button class="button button-secondary" data-remove="${realIdx}">Remove</button></div>`;
        miniEventsEl.appendChild(div);
      });
    }

    function miniAddEvent(){
      const t = document.getElementById('mini-title').value.trim();
      const d = document.getElementById('mini-date').value;
      if (!t || !d) return false;
      const list = loadMini(); list.push({title:t,date:d}); saveMini(list); miniForm.reset(); renderMini(); return false;
    }

    miniEventsEl.addEventListener('click', (e)=>{
      const btn = e.target.closest('button[data-remove]'); if (!btn) return;
      const idx = Number(btn.getAttribute('data-remove')); const list = loadMini(); if (isNaN(idx)) return; list.splice(idx,1); saveMini(list); renderMini();
    });

    // initial render
    renderMini();
  </script>
</body>
</html>
