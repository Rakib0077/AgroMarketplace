<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Harvest Calendar — Farmer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="farmer.css">
  <style>
    .events-list { display:flex; flex-direction:column; gap:10px; }
    .event-item { padding:10px; border:1px solid var(--color-border); border-radius:8px; background:var(--color-card-bg); }
    .event-form { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .event-form input, .event-form button { min-height:40px; }
  </style>
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
          <p class="eyebrow">Harvest planning</p>
          <h1 class="page-title">Availability calendar</h1>
        </div>
      </div>

      <div class="content-grid" style="grid-template-columns: 1fr 320px; gap:16px;">
        <article class="context-card" style="box-shadow: none;">
          <h2>Upcoming harvests</h2>
          <div id="events" class="events-list">
            <!-- events populated by JS -->
          </div>
        </article>

        <aside class="context-card" style="box-shadow: none;">
          <h2>Add harvest / availability</h2>
          <form id="eventForm" class="event-form" onsubmit="return addEvent();">
            <input id="title" placeholder="Event title" required>
            <input id="date" type="date" required>
            <button class="button button-primary" type="submit">Add</button>
          </form>
        </aside>
      </div>
    </section>
  </main>

  <script>
    const STORAGE_KEY = 'harvestEvents_v1';
    const eventsEl = document.getElementById('events');
    const form = document.getElementById('eventForm');

    function loadEvents() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
      } catch(e) {
        return [];
      }
    }

    function saveEvents(list) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    }

    function renderEvents() {
      eventsEl.innerHTML = '';
      const list = loadEvents();
      if (list.length === 0) {
        eventsEl.innerHTML = '<div class="meta-text">No scheduled harvests. Add one on the right.</div>';
        return;
      }
      list.slice().reverse().forEach((ev, idx) => {
        const div = document.createElement('div');
        div.className = 'event-item';
        const realIdx = list.length - 1 - idx;
        div.innerHTML = `
          <strong>${escapeHtml(ev.title)}</strong>
          <div style="font-size:12px;color:var(--color-muted);">${escapeHtml(ev.date)}</div>
          <div style="margin-top:8px; display:flex; gap:8px; justify-content:flex-end;">
            <button class="button button-secondary" data-remove="${realIdx}">Remove</button>
          </div>
        `;
        eventsEl.appendChild(div);
      });
    }

    function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function addEvent() {
      const t = document.getElementById('title').value.trim();
      const d = document.getElementById('date').value;
      if (!t || !d) return false;
      const list = loadEvents();
      list.push({ title: t, date: d });
      saveEvents(list);
      form.reset();
      renderEvents();
      return false; // prevent reload
    }

    eventsEl.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-remove]');
      if (!btn) return;
      const idx = Number(btn.getAttribute('data-remove'));
      const list = loadEvents();
      if (isNaN(idx) || idx < 0 || idx >= list.length) return;
      list.splice(idx, 1);
      saveEvents(list);
      renderEvents();
    });

    // seed if empty
    if (loadEvents().length === 0) {
      saveEvents([
        { title: 'Nazirshail harvest - ready', date: '2026-10-04' },
        { title: 'Corn harvest - expected', date: '2026-11-12' }
      ]);
    }

    renderEvents();
  </script>
</body>
</html>
