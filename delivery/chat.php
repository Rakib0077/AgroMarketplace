<?php
require_once __DIR__ . '/../database/app.php';
$user = require_role(['delivery']);
$database = app_database();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $conversationId = filter_var($_POST['conversation_id'] ?? null, FILTER_VALIDATE_INT);
  $body = trim(is_string($_POST['body'] ?? null) ? $_POST['body'] : '');
  if (!$database->canDeliveryMessageConversation((int) ($conversationId ?: 0), (int) $user['id']) || $body === '' || strlen($body) > 5000) {
    $error = 'Select an assigned job conversation and enter a message up to 5,000 characters.';
  } else {
    $database->sendConversationMessage((int) $conversationId, (int) $user['id'], $body);
    redirect_to('chat.php?conversation=' . (int) $conversationId);
  }
}

$conversations = $database->getDeliveryConversations((int) $user['id']);
$allConversations = $conversations;
$filterDate = is_string($_GET['date'] ?? null) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date']) ? $_GET['date'] : '';
$filterParticipant = is_string($_GET['participant'] ?? null) ? trim(substr($_GET['participant'], 0, 120)) : '';
$conversations = array_values(array_filter($allConversations, static function ($conversation) use ($filterDate, $filterParticipant) {
  $matchesDate = $filterDate === '' || substr($conversation['last_activity'], 0, 10) === $filterDate;
  $matchesParticipant = $filterParticipant === ''
    || stripos($conversation['buyer_name'] ?? '', $filterParticipant) !== false
    || stripos($conversation['farmer_name'] ?? '', $filterParticipant) !== false;
  return $matchesDate && $matchesParticipant;
}));
$hasConversations = count($allConversations) > 0;
$conversationId = filter_var($_GET['conversation'] ?? null, FILTER_VALIDATE_INT) ?: (int) ($conversations[0]['id'] ?? 0);
$conversationId = filter_var($_GET['conversation'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$selected = null;
foreach ($conversations as $conversation) {
  if ((int) $conversation['id'] === $conversationId) {
    $selected = $conversation;
    break;
  }
}
if (!$selected && $conversations) {
  $selected = $conversations[0];
  $conversationId = (int) $selected['id'];
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
  <title>Messages — Delivery</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="delivery.css">
</head>
<body class="theme-delivery">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Delivery navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="jobs.php">Jobs</a>
          <a class="nav-link" href="history.php">History</a>
        <a class="nav-link" href="route-detail.php">Route Detail</a>
        <a class="nav-link" href="hand-over.php">Order Handover</a>
        <a class="nav-link active" href="chat.php">Chat</a>
      </nav>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="chat-shell">
    <aside class="chat-sidebar" aria-label="Message filters and conversations">
      <div class="sidebar-card filter-card">
        <p class="filter-heading">Assigned order conversations</p>
        <form class="chat-filter-form" method="get" action="chat.php">
          <label class="chat-filter-field" for="chat-filter-date">
            <span>Last message date</span>
            <input id="chat-filter-date" type="date" name="date" value="<?= escape_html($filterDate) ?>">
          </label>
          <label class="chat-filter-field" for="chat-filter-participant">
            <span>Participant name</span>
            <input id="chat-filter-participant" type="search" name="participant" value="<?= escape_html($filterParticipant) ?>" placeholder="Search buyer or farmer" maxlength="120">
          </label>
          <div class="chat-filter-actions">
            <button class="button button-primary" type="submit">Apply</button>
            <a class="button button-secondary" href="chat.php">Clear</a>
          </div>
        </form>
      </div>

      <div class="sidebar-card thread-card">
        <div class="thread-list">
          <?php foreach ($conversations as $conversation): ?>
            <a class="thread-item <?= (int) $conversation['id'] === $conversationId ? 'active' : '' ?>" href="chat.php?conversation=<?= (int) $conversation['id'] ?>">
              <span class="thread-avatar avatar-rice">AM</span>
              <span class="thread-copy"><strong><?= escape_html($conversation['order_number']) ?></strong><small>Buyer: <?= escape_html($conversation['buyer_name']) ?> · Farmer: <?= escape_html($conversation['farmer_name']) ?></small></span>
            </a>
          <?php endforeach; ?>
          <?php if (!$conversations): ?><p><?= $hasConversations ? 'No conversations match these filters.' : 'No active assigned-order conversations.' ?></p><?php endif; ?>
        </div>
      </div>
    </aside>

    <section class="chat-panel" aria-label="Chat conversation">
      <header class="chat-header">
        <div class="chat-user">
          <span class="chat-avatar avatar-rice">RU</span>
          <div>
            <h1><?= $selected ? escape_html($selected['order_number']) : 'No conversation selected' ?></h1>
            <p><?= $selected ? 'Buyer: ' . escape_html($selected['buyer_name']) . ' · Farmer: ' . escape_html($selected['farmer_name']) : 'Accept a job to open its order conversation.' ?></p>
          </div>
        </div>
        <span class="chat-status"><span class="status-dot"></span>Online</span>
      </header>

      <div class="chat-thread">
        <?php foreach ($messages as $message): ?>
          <div class="message-row <?= (int) $message['sender_id'] === (int) $user['id'] ? 'outgoing' : 'incoming' ?>">
            <div class="message-bubble"><strong><?= escape_html($message['fullname'] ?: ucfirst($message['role'])) ?></strong><br><?= nl2br(escape_html($message['body'])) ?><small><?= escape_html(date('Y-m-d H:i', strtotime($message['created_at']))) ?></small></div>
          </div>
        <?php endforeach; ?>
        <?php if ($selected && !$messages): ?><p>No messages yet. Start the conversation.</p><?php endif; ?>
        <?php if (!$selected): ?><p>Accept a delivery job to message its buyer and farmer.</p><?php endif; ?>
      </div>

      <?php if ($error !== ''): ?><p role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <form class="message-compose" action="chat.php?conversation=<?= (int) $conversationId ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="conversation_id" value="<?= (int) $conversationId ?>">
        <label class="message-input-wrap" aria-label="Type a message">
          <input type="text" name="body" maxlength="5000" placeholder="Write operational update..." <?= $selected ? 'required' : 'disabled' ?>>
        </label>
        <button class="btn btn-primary" type="submit" <?= $selected ? '' : 'disabled' ?>>Send Message</button>
      </form>
    </section>
  </main>
</body>
</html>