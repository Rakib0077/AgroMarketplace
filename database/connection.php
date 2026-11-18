<?php

require_once __DIR__ . '/classes/base.php';
require_once __DIR__ . '/classes/buyer.php';
require_once __DIR__ . '/classes/farmer.php';
require_once __DIR__ . '/classes/delivery.php';
require_once __DIR__ . '/classes/admin.php';
require_once __DIR__ . '/classes/user.php';

/**
 * Singleton Database Connection Manager & Service Provider
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $conn = null;

    // Private constructor initializes connection
    private function __construct()
    {
        $host = 'localhost';
        $port = '3306';
        $user = 'root';
        $pass = 'root';
        $database = 'agromarketplace';
        $charset = "utf8mb4";

        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";

        try {
            $this->conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,  // if true emulate prepared statements always, for non-supported drivers
            ]);
        } catch (PDOException $e) {
            $this->conn = null;
            error_log("Database connection failed: " . $e->getMessage());
        }
    }

    // Prevent cloning
    private function __clone() {}

    /**
     * Get the singleton Database instance
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Get the raw PDO connection instance
     */
    public function getConnection(): ?PDO
    {
        return $this->conn;
    }

    /* ==================== SHARED DATABASE AND ACCOUNT FUNCTIONS ==================== */

    // Check whether the database connection is active.
    public function is_open(): bool
    {
        if ($this->conn === null) {
            return false;
        }

        try {
            $this->conn->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Log in a user and return their role-specific object.
    public function login(string $username, string $password): ?User
    {
        if (!$this->is_open()) {
            throw new Exception('Database connection is not active.');
        }

        $statement = $this->conn->prepare(
            "SELECT * FROM users WHERE (username = ? OR email = ?) AND account_status = 'active' LIMIT 1"
        );
        $statement->execute([$username, $username]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        $data = [
            'id' => $user['id'],
            'fullname' => $user['fullname'] ?? null,
            'email' => $user['email'],
            'phone' => $user['phone'] ?? null,
            'address' => $user['address'] ?? null,
            'business_name' => $user['business_name'] ?? null,
            'vehicle_capacity' => $user['vehicle_capacity'] ?? null,
            'vehicle_license_plate' => $user['vehicle_license_plate'] ?? null,
            'username' => $user['username'],
        ];

        return new User($user['role'], $data);
    }

    // Register a new user account.
    public function register(string $role, string $username, string $password, array $data, string $accountStatus = 'active'): bool
    {
        if (!$this->is_open()) {
            throw new Exception('Database connection is not active.');
        }
        if (!in_array($accountStatus, ['pending', 'active'], true)) {
            throw new InvalidArgumentException('Invalid registration account status.');
        }

        $check = $this->conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->execute([$username, $data['email']]);
        if ($check->fetch()) {
            throw new Exception('Username or email already exists.');
        }

        $statement = $this->conn->prepare(
            'INSERT INTO users (
                role, username, password, email, fullname, phone, address,
                profile_description, market_preferences,
                business_name, preferred_buying_category, typical_order_volume,
                land_area, primary_crops, verification_documents,
                vehicle_capacity, vehicle_license_plate, account_status
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $statement->execute([
            strtolower($role),
            $username,
            password_hash($password, PASSWORD_BCRYPT),
            $data['email'],
            $data['fullname'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['profile_description'] ?? null,
            $data['market_preferences'] ?? null,
            $data['business_name'] ?? null,
            $data['preferred_buying_category'] ?? null,
            $data['typical_order_volume'] ?? null,
            $data['land_area'] ?? null,
            $data['primary_crops'] ?? null,
            $data['verification_documents'] ?? null,
            $data['vehicle_capacity'] ?? null,
            $data['vehicle_license_plate'] ?? null,
            $accountStatus,
        ]);
    }

    /* ==================== SHARED ACCOUNT AND COMMISSION FUNCTIONS ==================== */

    // Return the public account details for an active user.
    public function getUserInfo(int $userId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT id, role, username, email, fullname, vehicle_capacity, vehicle_license_plate FROM users WHERE id = ? AND account_status = ?'
        );
        $statement->execute([$userId, 'active']);
        $user = $statement->fetch();

        return $user ?: null;
    }

    // Check a password and return the matching account without its password.
    public function authenticateUser(string $identifier, string $password): ?array
    {
        $statement = $this->conn->prepare(
            "SELECT id, role, username, email, fullname, password
             FROM users
             WHERE (username = :username OR email = :email) AND account_status = 'active'
             LIMIT 1"
        );
        $statement->execute(['username' => $identifier, 'email' => $identifier]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }

    // Calculate the active commission for a sale or delivery.
    public function calculateCommission(string $ruleType, ?int $categoryId, float $basis, ?string $effectiveAt = null): float
    {
        if (!in_array($ruleType, ['sale', 'delivery'], true) || $basis < 0) {
            throw new InvalidArgumentException('Invalid commission calculation.');
        }

        if ($effectiveAt === null) {
            $effectiveAt = (string) $this->conn->query('SELECT NOW()')->fetchColumn();
        }

        $statement = $this->conn->prepare(
            'SELECT rate_percent, fixed_amount
             FROM commission_rules
             WHERE rule_type = ?
               AND effective_from <= ? AND (effective_until IS NULL OR effective_until > ?)
               AND (category_id = ? OR (category_id IS NULL AND ? IS NULL))
             ORDER BY (category_id IS NULL) ASC, effective_from DESC
             LIMIT 1'
        );
        $statement->execute([$ruleType, $effectiveAt, $effectiveAt, $categoryId, $categoryId]);
        $rule = $statement->fetch();

        if (!$rule) {
            return 0.0;
        }

        return round($basis * (float) $rule['rate_percent'] / 100 + (float) $rule['fixed_amount'], 2);
    }

    /* ==================== BUYER FUNCTIONS ==================== */

    // Return one order that belongs to a buyer.
    public function getBuyerOrder(int $orderId, int $buyerId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT o.*, l.title FROM orders o LEFT JOIN listings l ON l.id = o.listing_id WHERE o.id = ? AND o.buyer_id = ?'
        );
        $statement->execute([$orderId, $buyerId]);
        $order = $statement->fetch();

        return $order ?: null;
    }

    // Return a buyer's bids and their seller details.
    public function getBuyerBids(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT b.id, b.quantity, b.price_per_unit, b.delivery_date, b.status,
                    l.id AS listing_id, l.farmer_id, l.title, l.price_per_unit AS asking_price,
                    l.quantity_unit, u.fullname, u.business_name
             FROM bids b
             JOIN listings l ON l.id = b.listing_id
             JOIN users u ON u.id = l.farmer_id
             WHERE b.buyer_id = ? ORDER BY b.created_at DESC'
        );
        $statement->execute([$buyerId]);

        return $statement->fetchAll();
    }

    // Return a buyer's bid count and average price.
    public function getBuyerBidStats(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT COUNT(*) AS bid_count, AVG(price_per_unit) AS average_price FROM bids WHERE buyer_id = ?'
        );
        $statement->execute([$buyerId]);

        return $statement->fetch();
    }

    // Return a buyer's orders and farmer details.
    public function getBuyerOrders(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT o.id, o.farmer_id, o.order_number, o.quantity, o.quantity_unit, o.total_amount, o.currency,
                    o.status, o.expected_delivery_at, l.title, u.fullname, u.business_name
             FROM orders o
             LEFT JOIN listings l ON l.id = o.listing_id
             JOIN users u ON u.id = o.farmer_id
             WHERE o.buyer_id = ? ORDER BY o.created_at DESC'
        );
        $statement->execute([$buyerId]);

        return $statement->fetchAll();
    }

    // Return summary totals for a buyer's orders.
    public function getBuyerOrderStats(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT COUNT(*) AS order_count,
                    SUM(CASE WHEN status NOT IN ('completed', 'cancelled') THEN 1 ELSE 0 END) AS open_count,
                    COALESCE(SUM(total_amount), 0) AS total_spend
             FROM orders WHERE buyer_id = ?"
        );
        $statement->execute([$buyerId]);

        return $statement->fetch();
    }

    // Return a buyer's invoices.
    public function getBuyerInvoices(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT i.id, i.invoice_number, i.order_id, i.total_amount, i.currency, i.status, i.issued_at,
                    o.order_number, l.title
             FROM invoices i JOIN orders o ON o.id = i.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             WHERE i.billed_to = ? ORDER BY i.issued_at DESC'
        );
        $statement->execute([$buyerId]);

        return $statement->fetchAll();
    }

    // Return invoice totals and refunds for a buyer.
    public function getBuyerInvoiceSummary(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END), 0) AS total_paid,
                    SUM(CASE WHEN status IN ('issued', 'overdue') THEN 1 ELSE 0 END) AS open_count,
                    MAX(issued_at) AS last_invoice
             FROM invoices WHERE billed_to = ?"
        );
        $statement->execute([$buyerId]);
        $summary = $statement->fetch();

        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payer_id = ? AND transaction_type = 'refund' AND status = 'completed'"
        );
        $statement->execute([$buyerId]);
        $summary['refund_total'] = (float) $statement->fetchColumn();

        return $summary;
    }

    // Return the summary values shown on the buyer dashboard.
    public function getBuyerDashboardSummary(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(CASE WHEN payment_status = 'escrowed' THEN total_amount ELSE 0 END), 0) AS escrow,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS recent_orders
             FROM orders WHERE buyer_id = ?"
        );
        $statement->execute([$buyerId]);

        return $statement->fetch();
    }

    // Return the number of pending bids for a buyer.
    public function getPendingBuyerBidCount(int $buyerId): int
    {
        $statement = $this->conn->prepare("SELECT COUNT(*) FROM bids WHERE buyer_id = ? AND status = 'pending'");
        $statement->execute([$buyerId]);

        return (int) $statement->fetchColumn();
    }

    // Return a few active listings for the buyer dashboard.
    public function getRecommendedListings(): array
    {
        return $this->conn->query(
            "SELECT id, title, quantity_available, quantity_unit, price_per_unit
             FROM listings WHERE status = 'active' AND quantity_available > 0 ORDER BY created_at DESC LIMIT 3"
        )->fetchAll();
    }

    // Return recent orders, bids, and invoices for the buyer dashboard.
    public function getBuyerRecentActivity(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT activity, created_at FROM (
                 SELECT CONCAT('Order ', o.order_number, ' · ', l.title) AS activity, o.created_at
                 FROM orders o LEFT JOIN listings l ON l.id = o.listing_id WHERE o.buyer_id = ?
                 UNION ALL
                 SELECT CONCAT('Bid submitted · ', l.title) AS activity, b.created_at
                 FROM bids b JOIN listings l ON l.id = b.listing_id WHERE b.buyer_id = ?
                 UNION ALL
                 SELECT CONCAT('Invoice issued · ', invoice_number) AS activity, issued_at AS created_at
                 FROM invoices WHERE billed_to = ?
             ) recent ORDER BY created_at DESC LIMIT 4"
        );
        $statement->execute([$buyerId, $buyerId, $buyerId]);

        return $statement->fetchAll();
    }

    // Return a buyer's latest bids and seller details.
    public function getRecentBuyerBids(int $buyerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT b.price_per_unit, b.status, l.id AS listing_id, l.title, l.price_per_unit AS asking_price,
                    l.farmer_id, seller.fullname, seller.business_name
             FROM bids b JOIN listings l ON l.id = b.listing_id JOIN users seller ON seller.id = l.farmer_id
             WHERE b.buyer_id = ? ORDER BY b.created_at DESC LIMIT 5'
        );
        $statement->execute([$buyerId]);

        return $statement->fetchAll();
    }

    /* ==================== MARKETPLACE AND CATALOGUE FUNCTIONS ==================== */

    // Return active listings that match the buyer's search and category filters.
    public function searchMarketplaceListings(string $search, int $categoryId): array
    {
        $sql = "SELECT l.id, l.title, l.quantity_available, l.quantity_unit, l.price_per_unit,
                       l.quality_label, l.image_path, c.name AS category, u.fullname, u.business_name
                FROM listings l
                LEFT JOIN categories c ON c.id = l.category_id
                JOIN users u ON u.id = l.farmer_id
                WHERE l.status = 'active' AND l.quantity_available > 0";
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (l.title LIKE ? OR c.name LIKE ? OR u.fullname LIKE ? OR u.business_name LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term, $term);
        }
        if ($categoryId > 0) {
            $sql .= ' AND l.category_id = ?';
            $params[] = $categoryId;
        }

        $statement = $this->conn->prepare($sql . ' ORDER BY l.created_at DESC');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    // Return active category options.
    public function getActiveCategories(): array
    {
        return $this->conn->query('SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name')->fetchAll();
    }

    // Return active catalogue listings that match a text search.
    public function searchCatalogueListings(string $search): array
    {
        $sql = "SELECT l.id, l.title, l.image_path, l.quantity_available, l.quantity_unit, l.price_per_unit,
                       l.quality_label, c.name AS category
                FROM listings l LEFT JOIN categories c ON c.id = l.category_id
                WHERE l.status = 'active' AND l.quantity_available > 0";
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (l.title LIKE ? OR c.name LIKE ?)';
            $term = '%' . $search . '%';
            $params = [$term, $term];
        }

        $statement = $this->conn->prepare($sql . ' ORDER BY l.created_at DESC');
        $statement->execute($params);

        return $statement->fetchAll();
    }

    // Return one active listing with its category and farmer details.
    public function getCatalogueListing(int $listingId): ?array
    {
        $statement = $this->conn->prepare(
            "SELECT l.*, c.name AS category, u.id AS farmer_id, u.fullname, u.business_name, u.address
             FROM listings l LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             WHERE l.id = ? AND l.status = 'active'"
        );
        $statement->execute([$listingId]);
        $listing = $statement->fetch();

        return $listing ?: null;
    }

    // Return an active listing with the details shown before ordering or bidding.
    public function getActiveListingDetails(int $listingId): ?array
    {
        $statement = $this->conn->prepare(
            "SELECT l.*, c.name AS category, u.fullname, u.business_name
             FROM listings l LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             WHERE l.id = ? AND l.status = 'active'"
        );
        $statement->execute([$listingId]);
        $listing = $statement->fetch();

        return $listing ?: null;
    }

    /* ==================== BUYER ORDER AND BID FUNCTIONS ==================== */

    // Create an order and its related records in one transaction.
    public function createBuyerOrder(int $buyerId, int $listingId, float $quantity): int
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                "SELECT * FROM listings WHERE id = ? AND status = 'active' FOR UPDATE"
            );
            $statement->execute([$listingId]);
            $listing = $statement->fetch();

            if (!$listing || $quantity > (float) $listing['quantity_available']) {
                throw new RuntimeException('The available quantity has changed.');
            }

            $unitPrice = (float) $listing['price_per_unit'];
            $subtotal = round($unitPrice * $quantity, 2);
            $salesCommission = $this->calculateCommission('sale', (int) $listing['category_id'], $subtotal);
            $deliveryCommission = $this->calculateCommission('delivery', null, $subtotal);
            $totalCommission = $salesCommission + $deliveryCommission;
            $totalAmount = $subtotal + $totalCommission;
            $orderNumber = 'AM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            $statement = $this->conn->prepare(
                "INSERT INTO orders (order_number, buyer_id, farmer_id, listing_id, quantity, quantity_unit, price_per_unit, subtotal, commission_amount, total_amount, status, payment_status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')"
            );
            $statement->execute([
                $orderNumber,
                $buyerId,
                $listing['farmer_id'],
                $listingId,
                $quantity,
                $listing['quantity_unit'],
                $unitPrice,
                $subtotal,
                $salesCommission,
                $totalAmount,
            ]);
            $orderId = (int) $this->conn->lastInsertId();

            $statement = $this->conn->prepare(
                'INSERT INTO order_status_history (order_id, changed_by, status, note) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([$orderId, $buyerId, 'pending', 'Order placed by buyer.']);

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            $statement = $this->conn->prepare(
                "INSERT INTO invoices (invoice_number, order_id, issued_by, billed_to, subtotal, commission_amount, total_amount, currency, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'BDT', 'issued')"
            );
            $statement->execute([$invoiceNumber, $orderId, $listing['farmer_id'], $buyerId, $subtotal, $totalCommission, $totalAmount]);

            $statement = $this->conn->prepare(
                "INSERT INTO delivery_jobs (order_id, pickup_address, dropoff_address, status, commission_amount)
                 SELECT ?, farmer.address, buyer.address, 'unassigned', ?
                 FROM users farmer JOIN users buyer ON buyer.id = ? WHERE farmer.id = ?"
            );
            $statement->execute([$orderId, $deliveryCommission, $buyerId, $listing['farmer_id']]);

            $statement = $this->conn->prepare('INSERT INTO conversations (order_id, created_by) VALUES (?, ?)');
            $statement->execute([$orderId, $buyerId]);
            $conversationId = (int) $this->conn->lastInsertId();
            $statement = $this->conn->prepare(
                'INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)'
            );
            $statement->execute([$conversationId, $buyerId]);
            $statement->execute([$conversationId, $listing['farmer_id']]);

            $remaining = (float) $listing['quantity_available'] - $quantity;
            $statement = $this->conn->prepare('UPDATE listings SET quantity_available = ?, status = ? WHERE id = ?');
            $statement->execute([$remaining, $remaining > 0 ? 'active' : 'sold', $listingId]);

            if ($listing['inventory_item_id']) {
                $statement = $this->conn->prepare(
                    'UPDATE inventory_items SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?'
                );
                $statement->execute([$quantity, $listing['inventory_item_id']]);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Save a buyer's bid on a listing.
    public function createBuyerBid(int $buyerId, int $listingId, float $quantity, float $price, ?string $deliveryDate): void
    {
        $statement = $this->conn->prepare(
            "INSERT INTO bids (listing_id, buyer_id, quantity, price_per_unit, delivery_date, status)
             VALUES (?, ?, ?, ?, ?, 'pending')"
        );
        $statement->execute([$listingId, $buyerId, $quantity, $price, $deliveryDate]);
    }

    // Return a bid that belongs to a buyer.
    public function getBuyerBid(int $bidId, int $buyerId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT b.*, l.title, l.quantity_unit, l.price_per_unit AS asking_price
             FROM bids b JOIN listings l ON l.id = b.listing_id WHERE b.id = ? AND b.buyer_id = ?'
        );
        $statement->execute([$bidId, $buyerId]);
        $bid = $statement->fetch();

        return $bid ?: null;
    }

    // Cancel a pending buyer bid.
    public function cancelBuyerBid(int $bidId, int $buyerId): void
    {
        $statement = $this->conn->prepare(
            "UPDATE bids SET status = 'withdrawn' WHERE id = ? AND buyer_id = ? AND status = 'pending'"
        );
        $statement->execute([$bidId, $buyerId]);
    }

    // Update the price, quantity, and message on a pending buyer bid.
    public function updateBuyerBid(int $bidId, int $buyerId, float $price, float $quantity, string $message): void
    {
        $statement = $this->conn->prepare(
            "UPDATE bids SET price_per_unit = ?, quantity = ?, message = ?
             WHERE id = ? AND buyer_id = ? AND status = 'pending'"
        );
        $statement->execute([$price, $quantity, $message, $bidId, $buyerId]);
    }

    // Return a buyer's profile details.
    public function getBuyerProfile(int $buyerId): ?array
    {
        $statement = $this->conn->prepare('SELECT * FROM users WHERE id = ? AND role = ?');
        $statement->execute([$buyerId, 'buyer']);
        $profile = $statement->fetch();

        return $profile ?: null;
    }

    // Save a buyer's profile details.
    public function updateBuyerProfile(int $buyerId, array $profile): bool
    {
        $statement = $this->conn->prepare(
            'UPDATE users SET business_name = ?, profile_description = ?, primary_crops = ?, market_preferences = ? WHERE id = ? AND role = ?'
        );

        return $statement->execute([
            $profile['business_name'],
            $profile['profile_description'],
            $profile['primary_crops'],
            $profile['market_preferences'],
            $buyerId,
            'buyer',
        ]);
    }

    /* ==================== SHARED CONVERSATION FUNCTIONS ==================== */

    // Check whether a user belongs to a conversation.
    public function isConversationParticipant(int $conversationId, int $userId): bool
    {
        $statement = $this->conn->prepare(
            'SELECT 1 FROM conversation_participants WHERE conversation_id = ? AND user_id = ?'
        );
        $statement->execute([$conversationId, $userId]);

        return (bool) $statement->fetchColumn();
    }

    // Save a message in a conversation.
    public function sendConversationMessage(int $conversationId, int $senderId, string $body): void
    {
        $statement = $this->conn->prepare(
            'INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)'
        );
        $statement->execute([$conversationId, $senderId, $body]);
    }

    // Return conversations and the other participants for a user.
    public function getUserConversations(int $userId): array
    {
        $statement = $this->conn->prepare(
            "SELECT c.id, o.order_number, c.created_at,
                    GROUP_CONCAT(DISTINCT CONCAT(CASE other.role WHEN 'buyer' THEN 'Buyer' WHEN 'farmer' THEN 'Farmer' WHEN 'delivery' THEN 'Delivery' ELSE 'User' END, ': ', COALESCE(NULLIF(other.business_name, ''), other.fullname)) ORDER BY other.role SEPARATOR ' | ') AS participants
             FROM conversations c
             JOIN conversation_participants mine ON mine.conversation_id = c.id AND mine.user_id = ?
             JOIN conversation_participants peer ON peer.conversation_id = c.id AND peer.user_id <> ?
             JOIN users other ON other.id = peer.user_id
             LEFT JOIN orders o ON o.id = c.order_id
             GROUP BY c.id, o.order_number, c.created_at
             ORDER BY c.created_at DESC"
        );
        $statement->execute([$userId, $userId]);

        return $statement->fetchAll();
    }

    // Return all messages in a conversation.
    public function getConversationMessages(int $conversationId): array
    {
        $statement = $this->conn->prepare(
            'SELECT m.sender_id, m.body, m.created_at, u.fullname, u.username, u.role FROM messages m JOIN users u ON u.id = m.sender_id WHERE m.conversation_id = ? ORDER BY m.id'
        );
        $statement->execute([$conversationId]);

        return $statement->fetchAll();
    }

    /* ==================== FARMER FUNCTIONS ==================== */

    // Return the summary values for a farmer's dashboard.
    public function getFarmerDashboardSummary(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_amount ELSE 0 END), 0) AS sales,
                    COALESCE(SUM(CASE WHEN payment_status IN ('unpaid', 'pending', 'escrowed') THEN total_amount ELSE 0 END), 0) AS pending,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS recent_orders
             FROM orders WHERE farmer_id = ?"
        );
        $statement->execute([$farmerId]);
        $summary = $statement->fetch();

        $statement = $this->conn->prepare("SELECT COUNT(*) FROM listings WHERE farmer_id = ? AND status = 'active'");
        $statement->execute([$farmerId]);
        $summary['active_listings'] = (int) $statement->fetchColumn();

        $statement = $this->conn->prepare(
            "SELECT COUNT(*) FROM bids b JOIN listings l ON l.id = b.listing_id WHERE l.farmer_id = ? AND b.status = 'pending'"
        );
        $statement->execute([$farmerId]);
        $summary['pending_bids'] = (int) $statement->fetchColumn();

        return $summary;
    }

    // Return sales ledger rows for a farmer.
    public function getFarmerOrders(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT o.id, o.order_number, o.quantity, o.quantity_unit, o.total_amount, o.currency,
                    o.status, o.payment_status, o.created_at, l.title, b.fullname AS buyer_name
             FROM orders o LEFT JOIN listings l ON l.id = o.listing_id
             JOIN users b ON b.id = o.buyer_id
             WHERE o.farmer_id = ? ORDER BY o.created_at DESC'
        );
        $statement->execute([$farmerId]);

        return $statement->fetchAll();
    }

    // Return payment totals for a farmer's sales ledger.
    public function getFarmerSalesSummary(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(total_amount), 0) AS gross_sales,
                    COALESCE(SUM(CASE WHEN payment_status = 'released' THEN total_amount ELSE 0 END), 0) AS settled,
                    COALESCE(SUM(CASE WHEN payment_status IN ('unpaid', 'pending', 'escrowed') THEN total_amount ELSE 0 END), 0) AS pending
             FROM orders WHERE farmer_id = ?"
        );
        $statement->execute([$farmerId]);

        return $statement->fetch();
    }

    // Return active listings used to filter the farmer marketplace.
    public function getMarketplaceListings(): array
    {
        return $this->conn->query(
            "SELECT l.id, l.farmer_id, l.title, l.quantity_available, l.quantity_unit,
                    l.price_per_unit, l.quality_label, l.image_path, l.category_id,
                    c.name AS category, u.fullname, u.business_name, u.address
             FROM listings l
             LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             WHERE l.status = 'active' AND l.quantity_available > 0
             ORDER BY l.created_at DESC"
        )->fetchAll();
    }

    // Return a farmer's public profile when allowed.
    public function getFarmerPublicProfile(int $farmerId, bool $isAdmin): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ? AND role = 'farmer'";
        if (!$isAdmin) {
            $sql .= " AND account_status = 'active'";
        }

        $statement = $this->conn->prepare($sql);
        $statement->execute([$farmerId]);
        $farmer = $statement->fetch();

        return $farmer ?: null;
    }

    // Return a farmer's own profile.
    public function getFarmerProfile(int $farmerId): ?array
    {
        $statement = $this->conn->prepare('SELECT * FROM users WHERE id = ? AND role = ?');
        $statement->execute([$farmerId, 'farmer']);
        $profile = $statement->fetch();

        return $profile ?: null;
    }

    // Save a farmer's profile details.
    public function updateFarmerProfile(int $farmerId, array $profile): bool
    {
        $statement = $this->conn->prepare(
            'UPDATE users SET business_name = ?, profile_description = ?, primary_crops = ?, market_preferences = ? WHERE id = ? AND role = ?'
        );

        return $statement->execute([
            $profile['business_name'],
            $profile['profile_description'],
            $profile['primary_crops'],
            $profile['market_preferences'],
            $farmerId,
            'farmer',
        ]);
    }

    // Return a farmer's listing unless it has been archived.
    public function getFarmerListing(int $listingId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT l.*, c.name AS category, u.fullname, u.business_name, u.address
             FROM listings l
             LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             WHERE l.id = ? AND l.status <> ?'
        );
        $statement->execute([$listingId, 'archived']);
        $listing = $statement->fetch();

        return $listing ?: null;
    }

    // Return a listing owned by a farmer for editing.
    public function getFarmerListingForEdit(int $listingId, int $farmerId): ?array
    {
                $statement = $this->conn->prepare(
                        'SELECT l.*,
                                        CASE WHEN i.id IS NULL THEN l.quantity_available
                                                 ELSE GREATEST(i.quantity - COALESCE((
                                                     SELECT SUM(other.quantity_available)
                                                     FROM listings other
                                                     WHERE other.inventory_item_id = i.id AND other.id <> l.id
                                                         AND other.status IN (\'active\', \'paused\', \'draft\')
                                                 ), 0), 0)
                                        END AS max_quantity
                         FROM listings l
                         LEFT JOIN inventory_items i ON i.id = l.inventory_item_id
                         WHERE l.id = ? AND l.farmer_id = ?'
                );
        $statement->execute([$listingId, $farmerId]);
        $listing = $statement->fetch();

        return $listing ?: null;
    }

    // Return a farmer's listings.
    public function getFarmerListings(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT l.id, l.title, c.name AS category, l.price_per_unit, l.quantity_available, l.quantity_unit, l.status
             FROM listings l LEFT JOIN categories c ON c.id = l.category_id
             WHERE l.farmer_id = ? ORDER BY l.created_at DESC'
        );
        $statement->execute([$farmerId]);

        return $statement->fetchAll();
    }

    // Return inventory quantities that are not already listed.
    public function getAvailableFarmerInventory(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT i.id, i.name, i.unit, i.quantity, i.harvest_date, i.quality_label,
                    i.category_id, c.name AS category,
                    GREATEST(i.quantity - COALESCE(listed.quantity, 0), 0) AS available_quantity
             FROM inventory_items i
             LEFT JOIN categories c ON c.id = i.category_id
             LEFT JOIN (
               SELECT inventory_item_id, SUM(quantity_available) AS quantity
               FROM listings
               WHERE status IN ('active', 'paused', 'draft')
               GROUP BY inventory_item_id
             ) listed ON listed.inventory_item_id = i.id
             WHERE i.farmer_id = ? AND i.status = 'available' AND i.quantity > COALESCE(listed.quantity, 0)
             ORDER BY i.name"
        );
        $statement->execute([$farmerId]);

        return $statement->fetchAll();
    }

    // Create a listing from a farmer's unlisted inventory quantity.
    public function createFarmerListing(int $farmerId, int $inventoryItemId, float $quantity, float $price, ?string $imagePath, string $deliveryNotes): void
    {
        $this->conn->beginTransaction();

        try {
            $inventoryStatement = $this->conn->prepare(
                "SELECT id, category_id, name, quantity, unit, harvest_date, quality_label
                 FROM inventory_items
                 WHERE id = ? AND farmer_id = ? AND status = 'available'
                 FOR UPDATE"
            );
            $inventoryStatement->execute([$inventoryItemId, $farmerId]);
            $inventory = $inventoryStatement->fetch();

            if (!$inventory || $quantity <= 0) {
                throw new RuntimeException('Choose available inventory and enter a valid quantity.');
            }

            $listedStatement = $this->conn->prepare(
                "SELECT COALESCE(SUM(quantity_available), 0)
                 FROM listings
                 WHERE inventory_item_id = ? AND status IN ('active', 'paused', 'draft')"
            );
            $listedStatement->execute([$inventoryItemId]);
            $availableQuantity = (float) $inventory['quantity'] - (float) $listedStatement->fetchColumn();

            if ($quantity > $availableQuantity) {
                throw new RuntimeException('The listing quantity cannot exceed the available inventory.');
            }

            $listingStatement = $this->conn->prepare(
                "INSERT INTO listings (farmer_id, inventory_item_id, category_id, title, image_path, quantity_available, quantity_unit, price_per_unit, harvest_date, quality_label, delivery_notes, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')"
            );
            $listingStatement->execute([
                $farmerId,
                $inventoryItemId,
                $inventory['category_id'],
                $inventory['name'],
                $imagePath,
                $quantity,
                $inventory['unit'],
                $price,
                $inventory['harvest_date'],
                $inventory['quality_label'],
                $deliveryNotes,
            ]);

            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Update a listing owned by a farmer.
    public function updateFarmerListing(int $listingId, int $farmerId, array $listing): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'SELECT inventory_item_id, quantity_available FROM listings WHERE id = ? AND farmer_id = ? FOR UPDATE'
            );
            $statement->execute([$listingId, $farmerId]);
            $currentListing = $statement->fetch();
            if (!$currentListing) {
                throw new RuntimeException('Listing not found.');
            }

            if ($currentListing['inventory_item_id']) {
                $statement = $this->conn->prepare(
                    "SELECT quantity FROM inventory_items WHERE id = ? AND farmer_id = ? AND status = 'available' FOR UPDATE"
                );
                $statement->execute([$currentListing['inventory_item_id'], $farmerId]);
                $inventoryQuantity = $statement->fetchColumn();
                if ($inventoryQuantity === false) {
                    throw new RuntimeException('The linked inventory is not available.');
                }

                $statement = $this->conn->prepare(
                    "SELECT COALESCE(SUM(quantity_available), 0)
                     FROM listings
                     WHERE inventory_item_id = ? AND id <> ? AND status IN ('active', 'paused', 'draft')"
                );
                $statement->execute([$currentListing['inventory_item_id'], $listingId]);
                $maxQuantity = (float) $inventoryQuantity - (float) $statement->fetchColumn();
            } else {
                $maxQuantity = (float) $currentListing['quantity_available'];
            }

            if ($listing['quantity'] > $maxQuantity) {
                throw new RuntimeException('The listing quantity cannot exceed the available inventory.');
            }

            $statement = $this->conn->prepare(
                'UPDATE listings SET title = ?, category_id = ?, quantity_available = ?, price_per_unit = ?, quality_label = ?, description = ?, delivery_notes = ?, image_path = ? WHERE id = ? AND farmer_id = ?'
            );
            $statement->execute([
                $listing['title'],
                $listing['category_id'],
                $listing['quantity'],
                $listing['price'],
                $listing['quality_label'],
                $listing['description'],
                $listing['delivery_notes'],
                $listing['image_path'],
                $listingId,
                $farmerId,
            ]);
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Archive a listing owned by a farmer.
    public function archiveFarmerListing(int $listingId, int $farmerId): void
    {
        $statement = $this->conn->prepare("UPDATE listings SET status = 'archived' WHERE id = ? AND farmer_id = ?");
        $statement->execute([$listingId, $farmerId]);
    }

    // Save a farmer's inventory item.
    public function saveFarmerInventoryItem(int $farmerId, int $itemId, array $item): void
    {
        if ($itemId > 0) {
            $statement = $this->conn->prepare(
                'UPDATE inventory_items SET category_id = ?, name = ?, quantity = ?, unit = ?, status = ?, notes = ? WHERE id = ? AND farmer_id = ?'
            );
            $statement->execute([
                $item['category_id'], $item['name'], $item['quantity'], $item['unit'],
                $item['status'], $item['notes'], $itemId, $farmerId,
            ]);
            return;
        }

        $statement = $this->conn->prepare(
            'INSERT INTO inventory_items (farmer_id, category_id, name, quantity, unit, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $farmerId, $item['category_id'], $item['name'], $item['quantity'],
            $item['unit'], $item['status'], $item['notes'],
        ]);
    }

    // Archive an inventory item owned by a farmer.
    public function archiveFarmerInventoryItem(int $itemId, int $farmerId): void
    {
        $statement = $this->conn->prepare("UPDATE inventory_items SET status = 'archived' WHERE id = ? AND farmer_id = ?");
        $statement->execute([$itemId, $farmerId]);
    }

    // Return inventory item details for editing.
    public function getFarmerInventoryItem(int $itemId, int $farmerId): ?array
    {
        $statement = $this->conn->prepare('SELECT * FROM inventory_items WHERE id = ? AND farmer_id = ?');
        $statement->execute([$itemId, $farmerId]);
        $item = $statement->fetch();

        return $item ?: null;
    }

    // Return inventory items and totals for a farmer.
    public function getFarmerInventory(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            "SELECT i.*, c.name AS category FROM inventory_items i LEFT JOIN categories c ON c.id = i.category_id WHERE i.farmer_id = ? AND i.status <> 'archived' ORDER BY i.created_at DESC"
        );
        $statement->execute([$farmerId]);
        $items = $statement->fetchAll();

        $statement = $this->conn->prepare(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available,
                    SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) AS reserved
             FROM inventory_items WHERE farmer_id = ? AND status <> 'archived'"
        );
        $statement->execute([$farmerId]);

        return ['items' => $items, 'totals' => $statement->fetch()];
    }

    // Accept or reject a farmer's pending bid in one transaction.
    public function updateFarmerBid(int $bidId, int $farmerId, string $action): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'SELECT b.*, l.farmer_id, l.category_id, l.quantity_available, l.quantity_unit, l.inventory_item_id, l.status AS listing_status
                 FROM bids b JOIN listings l ON l.id = b.listing_id
                 WHERE b.id = ? AND l.farmer_id = ? FOR UPDATE'
            );
            $statement->execute([$bidId, $farmerId]);
            $bid = $statement->fetch();

            if (!$bid || $bid['status'] !== 'pending') {
                throw new RuntimeException('This offer is no longer available.');
            }

            if ($action === 'reject') {
                $statement = $this->conn->prepare("UPDATE bids SET status = 'rejected' WHERE id = ?");
                $statement->execute([$bidId]);
            } elseif ($action === 'accept' && $bid['listing_status'] === 'active' && (float) $bid['quantity'] <= (float) $bid['quantity_available']) {
                $subtotal = round((float) $bid['quantity'] * (float) $bid['price_per_unit'], 2);
                $salesCommission = $this->calculateCommission('sale', (int) $bid['category_id'], $subtotal);
                $deliveryCommission = $this->calculateCommission('delivery', null, $subtotal);
                $totalCommission = $salesCommission + $deliveryCommission;
                $totalAmount = $subtotal + $totalCommission;
                $orderNumber = 'AM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

                $statement = $this->conn->prepare(
                    "INSERT INTO orders (order_number, buyer_id, farmer_id, listing_id, accepted_bid_id, quantity, quantity_unit, price_per_unit, subtotal, commission_amount, total_amount, status, payment_status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')"
                );
                $statement->execute([
                    $orderNumber, $bid['buyer_id'], $farmerId, $bid['listing_id'], $bidId,
                    $bid['quantity'], $bid['quantity_unit'], $bid['price_per_unit'], $subtotal,
                    $salesCommission, $totalAmount,
                ]);
                $orderId = (int) $this->conn->lastInsertId();

                $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
                $statement = $this->conn->prepare(
                    "INSERT INTO invoices (invoice_number, order_id, issued_by, billed_to, subtotal, commission_amount, total_amount, currency, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'BDT', 'issued')"
                );
                $statement->execute([$invoiceNumber, $orderId, $farmerId, $bid['buyer_id'], $subtotal, $totalCommission, $totalAmount]);

                $statement = $this->conn->prepare(
                    "INSERT INTO delivery_jobs (order_id, pickup_address, dropoff_address, status, commission_amount)
                     SELECT ?, farmer.address, buyer.address, 'unassigned', ?
                     FROM users farmer JOIN users buyer ON buyer.id = ? WHERE farmer.id = ?"
                );
                $statement->execute([$orderId, $deliveryCommission, $bid['buyer_id'], $farmerId]);

                $statement = $this->conn->prepare('INSERT INTO conversations (order_id, created_by) VALUES (?, ?)');
                $statement->execute([$orderId, $farmerId]);
                $conversationId = (int) $this->conn->lastInsertId();
                $statement = $this->conn->prepare(
                    'INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)'
                );
                $statement->execute([$conversationId, $farmerId]);
                $statement->execute([$conversationId, $bid['buyer_id']]);

                $statement = $this->conn->prepare("UPDATE bids SET status = 'accepted' WHERE id = ?");
                $statement->execute([$bidId]);
                $remaining = (float) $bid['quantity_available'] - (float) $bid['quantity'];
                $statement = $this->conn->prepare('UPDATE listings SET quantity_available = ?, status = ? WHERE id = ?');
                $statement->execute([$remaining, $remaining > 0 ? 'active' : 'sold', $bid['listing_id']]);

                if ($bid['inventory_item_id']) {
                    $statement = $this->conn->prepare(
                        'UPDATE inventory_items SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?'
                    );
                    $statement->execute([$bid['quantity'], $bid['inventory_item_id']]);
                }

                $statement = $this->conn->prepare(
                    'INSERT INTO order_status_history (order_id, changed_by, status, note) VALUES (?, ?, ?, ?)'
                );
                $statement->execute([$orderId, $farmerId, 'pending', 'Farmer accepted buyer bid.']);
            } else {
                throw new RuntimeException('The offer or available quantity is not valid.');
            }

            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Return farmer listings with their bids grouped by listing.
    public function getFarmerBids(int $farmerId): array
    {
        $statement = $this->conn->prepare(
            'SELECT l.id AS listing_id, l.title, l.status AS listing_status, l.quantity_unit,
                    b.id AS bid_id, b.quantity AS bid_quantity, b.price_per_unit AS bid_price,
                    b.status AS bid_status, b.delivery_date, u.fullname, u.business_name
             FROM listings l LEFT JOIN bids b ON b.listing_id = l.id
             LEFT JOIN users u ON u.id = b.buyer_id
             WHERE l.farmer_id = ? ORDER BY l.created_at DESC, b.created_at DESC'
        );
        $statement->execute([$farmerId]);
        $listings = [];

        foreach ($statement->fetchAll() as $row) {
            if (!isset($listings[$row['listing_id']])) {
                $listings[$row['listing_id']] = [
                    'id' => $row['listing_id'],
                    'title' => $row['title'],
                    'status' => $row['listing_status'],
                    'unit' => $row['quantity_unit'],
                    'bids' => [],
                ];
            }
            if ($row['bid_id'] !== null) {
                $listings[$row['listing_id']]['bids'][] = $row;
            }
        }

        return $listings;
    }

    /* ==================== DELIVERY FUNCTIONS ==================== */

    // Return the most recent delivery job for an order.
    public function getLatestDeliveryJob(int $orderId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT id, status, pickup_address, dropoff_address, scheduled_at, picked_up_at, delivered_at FROM delivery_jobs WHERE order_id = ? ORDER BY id DESC LIMIT 1'
        );
        $statement->execute([$orderId]);
        $job = $statement->fetch();

        return $job ?: null;
    }

    // Return and mark the latest handover notification for a buyer as read.
    public function getDeliveryHandoverNotification(int $userId, string $targetUrl): ?array
    {
        $statement = $this->conn->prepare(
            "SELECT id, body FROM notifications
             WHERE user_id = ? AND notification_type = 'delivery_otp' AND target_url = ?
             ORDER BY id DESC LIMIT 1"
        );
        $statement->execute([$userId, $targetUrl]);
        $notification = $statement->fetch();

        if ($notification) {
            $this->conn->prepare('UPDATE notifications SET read_at = NOW() WHERE id = ?')
                ->execute([$notification['id']]);
        }

        return $notification ?: null;
    }

    // Return active delivery jobs and counts for the delivery dashboard.
    public function getDeliveryDashboard(int $deliveryUserId): array
    {
        $statement = $this->conn->prepare(
            "SELECT d.id, d.status, d.pickup_address, d.dropoff_address, o.order_number,
                    o.quantity, o.quantity_unit, l.title
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             WHERE d.delivery_user_id = ? AND d.status IN ('assigned', 'accepted', 'picked_up', 'in_transit')
             ORDER BY d.created_at DESC LIMIT 5"
        );
        $statement->execute([$deliveryUserId]);
        $jobs = $statement->fetchAll();

        $statement = $this->conn->prepare(
            "SELECT COUNT(*) FROM delivery_jobs WHERE delivery_user_id = ? AND status NOT IN ('delivered', 'cancelled', 'failed')"
        );
        $statement->execute([$deliveryUserId]);
        $activeCount = (int) $statement->fetchColumn();

        $availableCount = (int) $this->conn->query(
            "SELECT COUNT(*) FROM delivery_jobs WHERE status = 'unassigned' AND delivery_user_id IS NULL"
        )->fetchColumn();

        return ['jobs' => $jobs, 'active_count' => $activeCount, 'available_count' => $availableCount];
    }

    // Return completed delivery jobs for a delivery agent.
    public function getDeliveryHistory(int $deliveryUserId): array
    {
        $statement = $this->conn->prepare(
            "SELECT d.id, d.pickup_address, d.dropoff_address, d.delivered_at,
                    o.order_number, o.quantity, o.quantity_unit, l.title, buyer.fullname AS buyer_name
             FROM delivery_jobs d
             JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             JOIN users buyer ON buyer.id = o.buyer_id
             WHERE d.delivery_user_id = ? AND d.status = 'delivered'
             ORDER BY COALESCE(d.delivered_at, d.updated_at) DESC"
        );
        $statement->execute([$deliveryUserId]);

        return $statement->fetchAll();
    }

    // Return active delivery jobs assigned to an agent.
    public function getAssignedDeliveryJobs(int $deliveryUserId): array
    {
        $statement = $this->conn->prepare(
            "SELECT d.id, d.status, d.pickup_address, d.dropoff_address, d.scheduled_at,
                    o.order_number, o.quantity, o.quantity_unit, l.title
             FROM delivery_jobs d
             JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             WHERE d.delivery_user_id = ? AND d.status IN ('assigned', 'accepted', 'picked_up', 'in_transit')
             ORDER BY d.created_at DESC"
        );
        $statement->execute([$deliveryUserId]);

        return $statement->fetchAll();
    }

    // Check that a delivery job is assigned to an agent and in transit.
    public function isDeliveryJobInTransit(int $jobId, int $deliveryUserId): bool
    {
        $statement = $this->conn->prepare(
            'SELECT id FROM delivery_jobs WHERE id = ? AND delivery_user_id = ? AND status = ?'
        );
        $statement->execute([$jobId, $deliveryUserId, 'in_transit']);

        return (bool) $statement->fetchColumn();
    }

    // Return a damage photo only when the requesting agent owns its job.
    public function getDeliveryDamagePhoto(int $photoId, int $deliveryUserId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT p.mime_type, p.image_data
             FROM delivery_damage_photos p
             JOIN delivery_damage_reports r ON r.id = p.report_id
             JOIN delivery_jobs d ON d.id = r.delivery_job_id
             WHERE p.id = ? AND d.delivery_user_id = ?'
        );
        $statement->execute([$photoId, $deliveryUserId]);
        $photo = $statement->fetch();

        return $photo ?: null;
    }

    // Return available jobs and jobs assigned to a delivery agent.
    public function getDeliveryJobs(int $deliveryUserId): array
    {
        $availableJobs = $this->conn->query(
            "SELECT d.id, d.status, d.pickup_address, d.dropoff_address, o.order_number,
                    o.quantity, o.quantity_unit, o.total_amount, o.currency, l.title,
                    farmer.fullname AS farmer_name, buyer.fullname AS buyer_name
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             JOIN users farmer ON farmer.id = o.farmer_id
             JOIN users buyer ON buyer.id = o.buyer_id
             WHERE d.status = 'unassigned' AND d.delivery_user_id IS NULL ORDER BY d.created_at"
        )->fetchAll();

        $statement = $this->conn->prepare(
            "SELECT d.id, d.status, d.pickup_address, d.dropoff_address, o.order_number,
                    o.quantity, o.quantity_unit, o.total_amount, l.title
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             WHERE d.delivery_user_id = ? AND d.status IN ('assigned', 'accepted', 'picked_up', 'in_transit')
             ORDER BY d.created_at DESC"
        );
        $statement->execute([$deliveryUserId]);

        return ['available' => $availableJobs, 'assigned' => $statement->fetchAll()];
    }

    // Claim an available delivery job and add all order participants to its conversation.
    public function claimDeliveryJob(int $jobId, int $deliveryUserId): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                "SELECT id, order_id FROM delivery_jobs
                 WHERE id = ? AND delivery_user_id IS NULL AND status = 'unassigned' FOR UPDATE"
            );
            $statement->execute([$jobId]);
            $job = $statement->fetch();
            if (!$job) {
                throw new RuntimeException('This job is no longer available.');
            }

            $statement = $this->conn->prepare("UPDATE delivery_jobs SET delivery_user_id = ?, status = 'assigned' WHERE id = ?");
            $statement->execute([$deliveryUserId, $job['id']]);

            $statement = $this->conn->prepare('SELECT id FROM conversations WHERE order_id = ? LIMIT 1');
            $statement->execute([$job['order_id']]);
            $conversationId = (int) $statement->fetchColumn();
            if ($conversationId === 0) {
                $statement = $this->conn->prepare('INSERT INTO conversations (order_id, created_by) VALUES (?, ?)');
                $statement->execute([$job['order_id'], $deliveryUserId]);
                $conversationId = (int) $this->conn->lastInsertId();
            }

            $statement = $this->conn->prepare('SELECT buyer_id, farmer_id FROM orders WHERE id = ?');
            $statement->execute([$job['order_id']]);
            $parties = $statement->fetch();
            $statement = $this->conn->prepare(
                'INSERT IGNORE INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?), (?, ?)'
            );
            $statement->execute([
                $conversationId, $parties['buyer_id'],
                $conversationId, $parties['farmer_id'],
                $conversationId, $deliveryUserId,
            ]);

            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Return job details for the assigned delivery agent.
    public function getDeliveryJobDetails(int $jobId, int $deliveryUserId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT d.*, o.id AS order_id, o.order_number, o.quantity, o.quantity_unit, o.total_amount, o.payment_status,
                    l.title, l.delivery_notes, farmer.fullname AS farmer_name, farmer.phone AS farmer_phone,
                    buyer.fullname AS buyer_name, buyer.phone AS buyer_phone
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             JOIN users farmer ON farmer.id = o.farmer_id
             JOIN users buyer ON buyer.id = o.buyer_id
             WHERE d.id = ? AND d.delivery_user_id = ?'
        );
        $statement->execute([$jobId, $deliveryUserId]);
        $job = $statement->fetch();

        return $job ?: null;
    }

    // Return the status of a job assigned to a delivery agent.
    public function getDeliveryJobStatus(int $jobId, int $deliveryUserId): ?string
    {
        $statement = $this->conn->prepare(
            'SELECT status FROM delivery_jobs WHERE id = ? AND delivery_user_id = ?'
        );
        $statement->execute([$jobId, $deliveryUserId]);
        $status = $statement->fetchColumn();

        return $status === false ? null : (string) $status;
    }

    // Advance or cancel an assigned delivery job.
    public function updateDeliveryJob(int $jobId, int $deliveryUserId, string $action): ?string
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'SELECT order_id, status FROM delivery_jobs WHERE id = ? AND delivery_user_id = ? FOR UPDATE'
            );
            $statement->execute([$jobId, $deliveryUserId]);
            $job = $statement->fetch();
            if (!$job) {
                throw new RuntimeException('This delivery is no longer assigned to you.');
            }

            if ($action === 'cancel') {
                if (!in_array($job['status'], ['assigned', 'accepted'], true)) {
                    throw new RuntimeException('A pickup can only be cancelled before the goods are collected.');
                }
                $statement = $this->conn->prepare(
                    "UPDATE delivery_jobs SET delivery_user_id = NULL, status = 'unassigned' WHERE id = ?"
                );
                $statement->execute([$jobId]);
                $this->conn->commit();
                return null;
            }

            if ($action !== 'advance') {
                throw new RuntimeException('Choose a valid delivery action.');
            }

            if (in_array($job['status'], ['assigned', 'accepted'], true)) {
                $newStatus = 'picked_up';
                $orderStatus = 'processing';
                $statement = $this->conn->prepare('UPDATE delivery_jobs SET status = ?, picked_up_at = NOW() WHERE id = ?');
                $statement->execute([$newStatus, $jobId]);
            } elseif ($job['status'] === 'picked_up') {
                $newStatus = 'in_transit';
                $orderStatus = 'in_transit';
                $otp = '123456';
                $statement = $this->conn->prepare(
                    'UPDATE delivery_jobs SET status = ?, handover_otp_hash = ? WHERE id = ?'
                );
                $statement->execute([$newStatus, password_hash($otp, PASSWORD_DEFAULT), $jobId]);

                $statement = $this->conn->prepare('SELECT buyer_id, order_number FROM orders WHERE id = ?');
                $statement->execute([$job['order_id']]);
                $order = $statement->fetch();
                $targetUrl = app_url('buyer/transaction.php?id=' . (int) $job['order_id']);
                $statement = $this->conn->prepare(
                    'INSERT INTO notifications (user_id, notification_type, title, body, target_url) VALUES (?, ?, ?, ?, ?)'
                );
                $statement->execute([
                    $order['buyer_id'],
                    'delivery_otp',
                    'Delivery handover code for ' . $order['order_number'],
                    'Give this code to your delivery agent when the order arrives: ' . $otp,
                    $targetUrl,
                ]);
            } else {
                throw new RuntimeException('This delivery cannot move to another status.');
            }

            $statement = $this->conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $statement->execute([$orderStatus, $job['order_id']]);
            $statement = $this->conn->prepare(
                'INSERT INTO order_status_history (order_id, changed_by, status, note) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([
                $job['order_id'],
                $deliveryUserId,
                $orderStatus,
                'Delivery status updated to ' . $newStatus . '.',
            ]);

            $this->conn->commit();
            return $newStatus;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Verify a handover code and mark its order delivered.
    public function verifyDeliveryHandover(int $jobId, int $deliveryUserId, string $otp): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'SELECT id, order_id, status FROM delivery_jobs WHERE id = ? AND delivery_user_id = ? FOR UPDATE'
            );
            $statement->execute([$jobId, $deliveryUserId]);
            $job = $statement->fetch();
            if (!$job || $job['status'] !== 'in_transit' || !hash_equals('123456', $otp)) {
                throw new RuntimeException('The handover code is invalid or this job is not in transit.');
            }

            $this->conn->prepare(
                "UPDATE delivery_jobs SET status = 'delivered', delivered_at = NOW(), handover_otp_hash = NULL WHERE id = ?"
            )->execute([$jobId]);
            $this->conn->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?")
                ->execute([$job['order_id']]);
            $statement = $this->conn->prepare(
                'INSERT INTO order_status_history (order_id, changed_by, status, note) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([$job['order_id'], $deliveryUserId, 'delivered', 'Delivery handover verified by OTP.']);
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Save a damage report and its photo together.
    public function saveDeliveryDamageReport(int $jobId, int $deliveryUserId, array $report, string $mimeType, string $imageData): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'INSERT INTO delivery_damage_reports (delivery_job_id, reported_by, damage_type, affected_quantity, description) VALUES (?, ?, ?, ?, ?)'
            );
            $statement->execute([
                $jobId, $deliveryUserId, $report['damage_type'],
                $report['affected_quantity'], $report['description'],
            ]);
            $statement = $this->conn->prepare(
                'INSERT INTO delivery_damage_photos (report_id, mime_type, image_data) VALUES (?, ?, ?)'
            );
            $statement->bindValue(1, (int) $this->conn->lastInsertId(), PDO::PARAM_INT);
            $statement->bindValue(2, $mimeType);
            $statement->bindValue(3, $imageData, PDO::PARAM_LOB);
            $statement->execute();
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Return active delivery jobs in handover display order.
    public function getDeliveryHandoverJobs(int $deliveryUserId): array
    {
        $statement = $this->conn->prepare(
            "SELECT d.id, d.status, d.handover_otp_hash, o.order_number, o.quantity, o.quantity_unit, l.title
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             WHERE d.delivery_user_id = ? AND d.status IN ('assigned', 'accepted', 'picked_up', 'in_transit')
             ORDER BY (d.status = 'in_transit') DESC, d.created_at DESC"
        );
        $statement->execute([$deliveryUserId]);

        return $statement->fetchAll();
    }

    // Return damage reports and photo identifiers for a delivery job.
    public function getDeliveryDamageReports(int $jobId): array
    {
        $statement = $this->conn->prepare(
            'SELECT r.damage_type, r.affected_quantity, r.description, r.created_at, p.id AS photo_id
             FROM delivery_damage_reports r
             LEFT JOIN delivery_damage_photos p ON p.report_id = r.id
             WHERE r.delivery_job_id = ? ORDER BY r.created_at DESC'
        );
        $statement->execute([$jobId]);

        return $statement->fetchAll();
    }

    // Check whether a delivery agent can send a message in a job conversation.
    public function canDeliveryMessageConversation(int $conversationId, int $deliveryUserId): bool
    {
        $statement = $this->conn->prepare(
            'SELECT 1 FROM conversations c
             JOIN conversation_participants p ON p.conversation_id = c.id
             JOIN delivery_jobs d ON d.order_id = c.order_id
             WHERE c.id = ? AND p.user_id = ? AND d.delivery_user_id = ? AND d.status <> ?'
        );
        $statement->execute([$conversationId, $deliveryUserId, $deliveryUserId, 'delivered']);

        return (bool) $statement->fetchColumn();
    }

    // Return the conversations assigned to a delivery agent.
    public function getDeliveryConversations(int $deliveryUserId): array
    {
        $statement = $this->conn->prepare(
            'SELECT c.id, o.order_number, buyer.id AS buyer_id, buyer.fullname AS buyer_name,
                    farmer.id AS farmer_id, farmer.fullname AS farmer_name, c.created_at,
                    COALESCE(MAX(m.created_at), c.created_at) AS last_activity
             FROM delivery_jobs d
             JOIN orders o ON o.id = d.order_id
             JOIN conversations c ON c.order_id = o.id
             JOIN conversation_participants p ON p.conversation_id = c.id AND p.user_id = d.delivery_user_id
             JOIN users buyer ON buyer.id = o.buyer_id
             JOIN users farmer ON farmer.id = o.farmer_id
             LEFT JOIN messages m ON m.conversation_id = c.id
             WHERE d.delivery_user_id = ? AND d.status <> ?
             GROUP BY c.id, o.order_number, buyer.id, buyer.fullname, farmer.id, farmer.fullname, c.created_at
             ORDER BY last_activity DESC'
        );
        $statement->execute([$deliveryUserId, 'delivered']);

        return $statement->fetchAll();
    }

    /* ==================== ADMIN FUNCTIONS ==================== */

    // Return categories, districts, and filtered listings for the admin marketplace.
    public function getAdminMarketplace(array $filters): array
    {
        $categories = $this->conn->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $districts = $this->conn->query(
            "SELECT DISTINCT u.address
             FROM users u JOIN listings l ON l.farmer_id = u.id
             WHERE u.role = 'farmer' AND u.address IS NOT NULL AND u.address <> ''
             ORDER BY u.address"
        )->fetchAll(PDO::FETCH_COLUMN);

        $conditions = [];
        $parameters = [];
        if ($filters['q'] !== '') {
            $conditions[] = '(l.title LIKE :search_title OR l.description LIKE :search_description OR u.fullname LIKE :search_name OR u.username LIKE :search_username)';
            $term = '%' . $filters['q'] . '%';
            $parameters['search_title'] = $term;
            $parameters['search_description'] = $term;
            $parameters['search_name'] = $term;
            $parameters['search_username'] = $term;
        }
        foreach (['category' => 'l.category_id', 'district' => 'u.address', 'status' => 'l.status'] as $filter => $column) {
            if ($filters[$filter] !== '') {
                $conditions[] = $column . ' = :' . $filter;
                $parameters[$filter] = $filters[$filter];
            }
        }
        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $statement = $this->conn->prepare(
            "SELECT l.id, l.title, l.image_path, l.description, l.quantity_available, l.quantity_unit,
                    l.price_per_unit, l.currency, l.quality_label, l.status, l.created_at,
                    l.farmer_id, l.category_id, c.name AS category, u.fullname AS farmer_name,
                    u.username AS farmer_username, u.address AS farmer_address
             FROM listings l
             LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             $where
             ORDER BY FIELD(l.status, 'active', 'draft', 'paused', 'sold', 'archived'), l.created_at DESC"
        );
        $statement->execute($parameters);

        return ['categories' => $categories, 'districts' => $districts, 'listings' => $statement->fetchAll()];
    }

    // Return the status of a listing for an admin action.
    public function getListingStatus(int $listingId): ?string
    {
        $statement = $this->conn->prepare('SELECT id, status FROM listings WHERE id = ?');
        $statement->execute([$listingId]);
        $listing = $statement->fetch();

        return $listing ? (string) $listing['status'] : null;
    }

    // Archive a listing from the admin marketplace.
    public function archiveAdminListing(int $listingId): void
    {
        $this->conn->prepare("UPDATE listings SET status = 'archived' WHERE id = ?")->execute([$listingId]);
    }

    // Restore an archived listing to active or sold status.
    public function restoreAdminListing(int $listingId): void
    {
        $this->conn->prepare(
            "UPDATE listings SET status = IF(quantity_available > 0, 'active', 'sold') WHERE id = ?"
        )->execute([$listingId]);
    }

    // Return the full review details for an admin listing review.
    public function getAdminListingReview(int $listingId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT l.id, l.title, l.image_path, l.description, l.quantity_available, l.quantity_unit,
                    l.price_per_unit, l.currency, l.harvest_date, l.quality_label, l.delivery_notes,
                    l.status, l.created_at, l.farmer_id, c.name AS category,
                    u.fullname AS farmer_name, u.username AS farmer_username, u.email AS farmer_email,
                    u.address AS farmer_address, u.business_name, u.account_status AS farmer_status,
                    u.primary_crops, u.verification_documents
             FROM listings l
             LEFT JOIN categories c ON c.id = l.category_id
             JOIN users u ON u.id = l.farmer_id
             WHERE l.id = ?'
        );
        $statement->execute([$listingId]);
        $listing = $statement->fetch();

        return $listing ?: null;
    }

    // Return a user profile for the admin profile page.
    public function getAdminUserProfile(int $userId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT id, role, username, email, fullname, phone, address, profile_description,
                    market_preferences, business_name, preferred_buying_category,
                    typical_order_volume, land_area, primary_crops, verification_documents,
                    vehicle_capacity, vehicle_license_plate, account_status, created_at
             FROM users WHERE id = ?'
        );
        $statement->execute([$userId]);
        $profile = $statement->fetch();

        return $profile ?: null;
    }

    // Return account and verification details for admin review.
    public function getAdminUserReview(int $userId): ?array
    {
        $statement = $this->conn->prepare(
            'SELECT id, role, username, email, fullname, phone, address, business_name,
                    preferred_buying_category, typical_order_volume, land_area, primary_crops,
                    verification_documents, vehicle_capacity, vehicle_license_plate,
                    account_status, created_at
             FROM users WHERE id = ?'
        );
        $statement->execute([$userId]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    // Return submitted verification documents for a user.
    public function getUserVerifications(int $userId): array
    {
        $statement = $this->conn->prepare(
            'SELECT document_type, document_reference, status, review_notes, reviewed_at, submitted_at
             FROM user_verifications WHERE user_id = ? ORDER BY submitted_at DESC, id DESC'
        );
        $statement->execute([$userId]);

        return $statement->fetchAll();
    }

    // Update an account status and return the number of changed rows.
    public function updateAccountStatus(int $userId, string $oldStatus, string $newStatus): int
    {
        $statement = $this->conn->prepare('UPDATE users SET account_status = ? WHERE id = ? AND account_status = ?');
        $statement->execute([$newStatus, $userId, $oldStatus]);

        return $statement->rowCount();
    }

    // Set an account status without checking its previous value.
    public function setAccountStatus(int $userId, string $status): void
    {
        $statement = $this->conn->prepare('UPDATE users SET account_status = ? WHERE id = ?');
        $statement->execute([$status, $userId]);
    }

    // Delete a pending account if it has no related marketplace activity.
    public function deletePendingUser(int $userId): bool
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare("DELETE FROM users WHERE id = ? AND account_status = 'pending'");
            $statement->execute([$userId]);
            if ($statement->rowCount() !== 1) {
                throw new RuntimeException('Applicant is no longer pending.');
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Return filtered user accounts for the admin verification queue.
    public function getAdminUsers(array $filters): array
    {
        $conditions = [];
        $parameters = [];
        if ($filters['q'] !== '') {
            $conditions[] = '(u.fullname LIKE :search_name OR u.username LIKE :search_username OR u.email LIKE :search_email)';
            $term = '%' . $filters['q'] . '%';
            $parameters['search_name'] = $term;
            $parameters['search_username'] = $term;
            $parameters['search_email'] = $term;
        }
        foreach (['role' => 'u.role', 'account_status' => 'u.account_status'] as $filter => $column) {
            if ($filters[$filter] !== '') {
                $conditions[] = $column . ' = :' . $filter;
                $parameters[$filter] = $filters[$filter];
            }
        }
        if ($filters['date_from'] !== '') {
            $conditions[] = 'u.created_at >= :date_from';
            $parameters['date_from'] = $filters['date_from'];
        }
        if ($filters['date_to'] !== '') {
            $conditions[] = 'u.created_at < DATE_ADD(:date_to, INTERVAL 1 DAY)';
            $parameters['date_to'] = $filters['date_to'];
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $having = $filters['review_status'] === '' ? '' : 'HAVING review_status = :review_status';
        if ($filters['review_status'] !== '') {
            $parameters['review_status'] = $filters['review_status'];
        }
        $statement = $this->conn->prepare(
            "SELECT u.id, u.role, u.username, u.email, u.fullname, u.address, u.verification_documents,
                    u.account_status, u.created_at,
                    COUNT(v.id) AS document_count,
                    COALESCE(NULLIF(GROUP_CONCAT(DISTINCT v.document_type ORDER BY v.document_type SEPARATOR ', '), ''),
                             NULLIF(u.verification_documents, ''), 'Not submitted') AS documents,
                    CASE u.account_status
                      WHEN 'pending' THEN 'pending'
                      WHEN 'closed' THEN 'rejected'
                      ELSE 'approved'
                    END AS review_status
             FROM users u
             LEFT JOIN user_verifications v ON v.user_id = u.id
             $where
             GROUP BY u.id
             $having
             ORDER BY (review_status = 'pending') DESC, u.created_at DESC"
        );
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    // Return conversations with the real accounts in each thread.
    public function getAdminConversations(): array
    {
        $statement = $this->conn->query(
            'SELECT c.id AS conversation_id, c.created_at, o.order_number,
                    MAX(m.created_at) AS last_message_at,
                    u.id AS user_id, u.fullname, u.username, u.role
             FROM conversations c
             JOIN conversation_participants p ON p.conversation_id = c.id
             JOIN users u ON u.id = p.user_id
             LEFT JOIN orders o ON o.id = c.order_id
             LEFT JOIN messages m ON m.conversation_id = c.id
             GROUP BY c.id, c.created_at, o.order_number, u.id, u.fullname, u.username, u.role
             ORDER BY COALESCE(MAX(m.created_at), c.created_at) DESC, c.id DESC, u.role, u.id'
        );
        $conversations = [];

        foreach ($statement->fetchAll() as $row) {
            $conversationId = (int) $row['conversation_id'];
            if (!isset($conversations[$conversationId])) {
                $conversations[$conversationId] = [
                    'id' => $conversationId,
                    'order_number' => $row['order_number'],
                    'created_at' => $row['created_at'],
                    'last_activity' => $row['last_message_at'] ?: $row['created_at'],
                    'participants' => [],
                ];
            }
            $conversations[$conversationId]['participants'][] = [
                'id' => (int) $row['user_id'],
                'name' => $row['fullname'] ?: $row['username'],
                'role' => $row['role'],
            ];
        }

        return array_values($conversations);
    }

    // Add an admin reply to a conversation and join the thread if needed.
    public function sendAdminConversationMessage(int $conversationId, int $adminId, string $body): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare('SELECT id FROM conversations WHERE id = ?');
            $statement->execute([$conversationId]);
            if (!$statement->fetchColumn()) {
                throw new RuntimeException('Conversation not found.');
            }

            $statement = $this->conn->prepare(
                'INSERT IGNORE INTO conversation_participants (conversation_id, user_id) VALUES (?, ?)'
            );
            $statement->execute([$conversationId, $adminId]);
            $statement = $this->conn->prepare(
                'INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)'
            );
            $statement->execute([$conversationId, $adminId, $body]);
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    /* ==================== COMMISSION FUNCTIONS ==================== */

    // Return the current database date and time.
    public function getDatabaseNow(): string
    {
        return (string) $this->conn->query('SELECT NOW()')->fetchColumn();
    }

    // Save a commission rule and end earlier active rules of the same type.
    public function saveCommissionRule(string $ruleType, ?int $categoryId, float $rate, float $fixedAmount, string $effectiveAt, ?string $endAt): void
    {
        $this->conn->beginTransaction();

        try {
            $statement = $this->conn->prepare(
                'UPDATE commission_rules
                 SET is_active = FALSE, effective_until = ?
                 WHERE rule_type = ? AND category_id <=> ? AND is_active = TRUE AND effective_from < ?'
            );
            $statement->execute([$effectiveAt, $ruleType, $categoryId, $effectiveAt]);
            $statement = $this->conn->prepare(
                'INSERT INTO commission_rules
                   (rule_type, category_id, rate_percent, fixed_amount, currency, effective_from, effective_until, is_active)
                 VALUES (?, ?, ?, ?, \'BDT\', ?, ?, TRUE)'
            );
            $statement->execute([$ruleType, $categoryId, $rate, $fixedAmount, $effectiveAt, $endAt]);
            $this->conn->commit();
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Deactivate an active commission rule.
    public function disableCommissionRule(int $ruleId): int
    {
        $statement = $this->conn->prepare(
            'UPDATE commission_rules
             SET is_active = FALSE, effective_until = LEAST(COALESCE(effective_until, NOW()), NOW())
             WHERE id = ? AND is_active = TRUE'
        );
        $statement->execute([$ruleId]);

        return $statement->rowCount();
    }

    // Reactivate an ended commission rule and replace the current matching rule.
    public function reactivateCommissionRule(int $ruleId): bool
    {
        $statement = $this->conn->prepare(
            'SELECT rule_type, category_id FROM commission_rules WHERE id = ? AND (is_active = FALSE OR effective_until <= NOW())'
        );
        $statement->execute([$ruleId]);
        $rule = $statement->fetch();
        if (!$rule) {
            return false;
        }

        $this->conn->beginTransaction();
        try {
            $statement = $this->conn->prepare(
                'UPDATE commission_rules
                 SET is_active = FALSE, effective_until = NOW()
                 WHERE rule_type = ? AND category_id <=> ? AND is_active = TRUE AND id <> ? AND effective_from <= NOW()'
            );
            $statement->execute([$rule['rule_type'], $rule['category_id'], $ruleId]);
            $statement = $this->conn->prepare(
                'UPDATE commission_rules SET is_active = TRUE, effective_from = NOW(), effective_until = NULL WHERE id = ?'
            );
            $statement->execute([$ruleId]);
            $this->conn->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    // Permanently delete a commission rule.
    public function cancelCommissionRule(int $ruleId): int
    {
        $statement = $this->conn->prepare('DELETE FROM commission_rules WHERE id = ?');
        $statement->execute([$ruleId]);

        return $statement->rowCount();
    }

    // Return categories, commission totals, activity rows, and rules for the admin report.
    public function getCommissionReport(array $filters): array
    {
        $categories = $this->conn->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $conditions = [];
        $parameters = [];
        if ($filters['date_from'] !== '') {
            $conditions[] = 'o.created_at >= :date_from';
            $parameters['date_from'] = $filters['date_from'];
        }
        if ($filters['date_to'] !== '') {
            $conditions[] = 'o.created_at < DATE_ADD(:date_to, INTERVAL 1 DAY)';
            $parameters['date_to'] = $filters['date_to'];
        }
        if ($filters['category_id'] !== '') {
            $conditions[] = 'l.category_id = :category_id';
            $parameters['category_id'] = $filters['category_id'];
        }
        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $statement = $this->conn->prepare(
            "SELECT COUNT(*) AS order_count, COALESCE(SUM(o.subtotal), 0) AS sales_volume,
                    COALESCE(SUM(o.commission_amount), 0) AS sales_commission
             FROM orders o LEFT JOIN listings l ON l.id = o.listing_id $where"
        );
        $statement->execute($parameters);
        $salesSummary = $statement->fetch();

        $statement = $this->conn->prepare(
            "SELECT COUNT(d.id) AS delivery_count, COALESCE(SUM(d.commission_amount), 0) AS delivery_commission,
                    SUM(d.status IN ('delivered')) AS completed_deliveries
             FROM delivery_jobs d JOIN orders o ON o.id = d.order_id
             LEFT JOIN listings l ON l.id = o.listing_id $where"
        );
        $statement->execute($parameters);
        $deliverySummary = $statement->fetch();

        $collectedWhere = $conditions === [] ? 'WHERE' : $where . ' AND';
        $statement = $this->conn->prepare(
            "SELECT COALESCE(SUM(p.amount), 0)
             FROM payments p JOIN orders o ON o.id = p.order_id
             LEFT JOIN listings l ON l.id = o.listing_id
             $collectedWhere p.transaction_type = 'commission' AND p.status = 'completed'"
        );
        $statement->execute($parameters);
        $commissionCollected = (float) $statement->fetchColumn();

        $ledgerConditions = $conditions;
        if ($filters['type'] === 'sale') {
            $ledgerConditions[] = 'o.commission_amount > 0';
        } elseif ($filters['type'] === 'delivery') {
            $ledgerConditions[] = 'delivery.job_count > 0';
        }
        $ledgerWhere = $ledgerConditions === [] ? '' : 'WHERE ' . implode(' AND ', $ledgerConditions);
        $statement = $this->conn->prepare(
            "SELECT o.id, o.order_number, o.created_at, o.subtotal, o.commission_amount AS sales_commission,
                    o.currency, o.status AS order_status, l.title, c.name AS category,
                    buyer.fullname AS buyer_name, farmer.fullname AS farmer_name,
                    COALESCE(delivery.commission_amount, 0) AS delivery_commission,
                    COALESCE(delivery.job_count, 0) AS job_count, delivery.delivery_status,
                    delivery.driver_names, COALESCE(collected.amount, 0) AS collected_amount
             FROM orders o
             LEFT JOIN listings l ON l.id = o.listing_id
             LEFT JOIN categories c ON c.id = l.category_id
             JOIN users buyer ON buyer.id = o.buyer_id
             JOIN users farmer ON farmer.id = o.farmer_id
             LEFT JOIN (
               SELECT d.order_id, SUM(d.commission_amount) AS commission_amount,
                      COUNT(*) AS job_count, GROUP_CONCAT(DISTINCT d.status ORDER BY d.status SEPARATOR ', ') AS delivery_status,
                      GROUP_CONCAT(DISTINCT driver.fullname ORDER BY driver.fullname SEPARATOR ', ') AS driver_names
               FROM delivery_jobs d LEFT JOIN users driver ON driver.id = d.delivery_user_id
               GROUP BY d.order_id
             ) delivery ON delivery.order_id = o.id
             LEFT JOIN (
               SELECT order_id, SUM(amount) AS amount FROM payments
               WHERE transaction_type = 'commission' AND status = 'completed'
               GROUP BY order_id
             ) collected ON collected.order_id = o.id
             $ledgerWhere
             ORDER BY o.created_at DESC, o.id DESC"
        );
        $statement->execute($parameters);
        $ledger = $statement->fetchAll();

        $rules = $this->conn->query(
            'SELECT r.id, r.rule_type, r.category_id, r.rate_percent, r.fixed_amount, r.currency,
                    r.effective_from, r.effective_until, r.is_active, c.name AS category
             FROM commission_rules r LEFT JOIN categories c ON c.id = r.category_id
             ORDER BY r.rule_type, r.effective_from DESC'
        )->fetchAll();

        return [
            'categories' => $categories,
            'sales_summary' => $salesSummary,
            'delivery_summary' => $deliverySummary,
            'commission_collected' => $commissionCollected,
            'ledger' => $ledger,
            'rules' => $rules,
            'database_now' => $this->getDatabaseNow(),
        ];
    }

}
