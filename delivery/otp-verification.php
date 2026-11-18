<?php
require_once __DIR__ . '/../database/app.php';
$user = require_role(['delivery']);
$jobId = filter_var($_GET['job'] ?? null, FILTER_VALIDATE_INT);

if (!$jobId) {
  http_response_code(404);
  exit('Delivery job not found.');
}

if (!app_database()->isDeliveryJobInTransit($jobId, (int) $user['id'])) {
  http_response_code(404);
  exit('In-transit delivery job not found.');
}

redirect_to('hand-over.php?job=' . (int) $jobId);