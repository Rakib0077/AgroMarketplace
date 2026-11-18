<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $conversationId = (int) ($_POST['conversation_id'] ?? 0);
  $body = trim($_POST['body'] ?? '');
  if (!$database->isConversationParticipant($conversationId, (int) $user['id']) || $body === '' || strlen($body) > 5000) {
    $error = 'Choose a conversation and enter a message of up to 5,000 characters.';
  } else {
    $database->sendConversationMessage($conversationId, (int) $user['id'], $body);
    redirect_to('chat.php?conversation=' . $conversationId);
  }
}

$conversations = $database->getUserConversations((int) $user['id']);
$conversationId = (int) ($_GET['conversation'] ?? ($conversations[0]['id'] ?? 0));
$selected = null;
foreach ($conversations as $conversation) {
  if ((int) $conversation['id'] === $conversationId) {
    $selected = $conversation;
    break;
  }
}
$messages = [];
if ($selected) {
  $messages = $database->getConversationMessages($conversationId);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Messages — Farmer</title>
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

  <main class="chat-shell">
    <aside class="chat-sidebar" aria-label="Message filters and conversations">
      <div class="sidebar-card filter-card">
        <p class="filter-heading">Inbox</p>
        <div class="filter-list">
          <button class="filter-chip active" type="button"><span class="filter-graphic graphic-all">✓</span>All</button>
          <button class="filter-chip" type="button"><span class="filter-graphic graphic-buyers">B</span>Buyers</button>
          <button class="filter-chip" type="button"><span class="filter-graphic graphic-sellers">S</span>Sellers</button>
          <button class="filter-chip" type="button"><span class="filter-graphic graphic-urgent">!</span>Urgent</button>
        </div>
      </div>

      <div class="sidebar-card filter-card">
        <p class="filter-heading">Filter chats</p>
        <label class="date-filter-group">
          <span>From</span>
          <input type="date" value="2026-09-01">
        </label>
        <label class="date-filter-group">
          <span>To</span>
          <input type="date" value="2026-09-30">
        </label>
        <label class="search-filter-group">
          <span>Search user</span>
          <input type="search" placeholder="Type a name">
        </label>
        <button class="btn btn-primary" type="button">Apply</button>
      </div>

      <div class="sidebar-card thread-card">
        <div class="thread-list">
          <?php foreach ($conversations as $conversation): ?>
            <a class="thread-item <?= (int) $conversation['id'] === $conversationId ? 'active' : '' ?>" href="chat.php?conversation=<?= (int) $conversation['id'] ?>">
              <span class="thread-avatar avatar-rice">AM</span>
              <span class="thread-copy">
                <strong><?= escape_html($conversation['participants'] ?? 'Order participants') ?></strong>
                <small><?= escape_html($conversation['order_number'] ?? 'Marketplace chat') ?></small>
              </span>
            </a>
          <?php endforeach; ?>
          <?php if (!$conversations): ?><p>No order conversations yet.</p><?php endif; ?>
        </div>
      </div>
    </aside>

    <section class="chat-panel" aria-label="Chat conversation">
      <header class="chat-header">
        <div class="chat-user">
          <span class="chat-avatar avatar-rice">AM</span>
          <div>
            <h1><?= $selected ? escape_html($selected['order_number'] ?? 'Marketplace chat') : 'No conversation selected' ?></h1>
            <p><?= $selected ? escape_html($selected['participants']) : 'Chats are created after an order is placed' ?></p>
          </div>
        </div>
        <span class="chat-status"><span class="status-dot"></span>Online</span>
      </header>

      <div class="chat-thread">
        <?php foreach ($messages as $message): ?>
          <div class="message-row <?= (int) $message['sender_id'] === (int) $user['id'] ? 'outgoing' : 'incoming' ?>">
            <div class="message-bubble"><strong><?= escape_html($message['fullname'] ?: ucfirst($message['role'])) ?> · <?= escape_html(ucfirst($message['role'])) ?></strong><br><?= nl2br(escape_html($message['body'])) ?></div>
          </div>
        <?php endforeach; ?>
        <?php if ($selected && !$messages): ?><p>No messages yet. Start the conversation.</p><?php endif; ?>
        <?php if (!$selected): ?><p>Accept a buyer offer or order to open a conversation.</p><?php endif; ?>
      </div>

      <?php if ($error !== ''): ?><p role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <form class="message-compose" action="chat.php?conversation=<?= (int) $conversationId ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="conversation_id" value="<?= (int) $conversationId ?>">
        <label class="message-input-wrap" aria-label="Type a message">
          <input type="text" name="body" maxlength="5000" placeholder="Type a message..." <?= $selected ? 'required' : 'disabled' ?>>
        </label>
        <button class="btn btn-primary" type="submit">Send</button>
      </form>
    </section>
  </main>
</body>
</html>
