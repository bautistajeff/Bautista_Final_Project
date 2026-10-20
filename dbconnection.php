<?php
session_start();

function getDatabaseConfig(): array
{
    return [
        'driver' => getenv('DB_DRIVER') ?: 'sqlite',
        'host' => getenv('DB_HOST') ?: 'localhost',
        'dbname' => getenv('DB_NAME') ?: 'inventory_management',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ];
}

function initializeDatabase(PDO $pdo): void
{
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS medicines (
                id INT AUTO_INCREMENT PRIMARY KEY,
                medicine_name VARCHAR(150) NOT NULL,
                category VARCHAR(100) NOT NULL,
                stock INT NOT NULL DEFAULT 0,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                expiry_date DATE NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS medicine_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                medicine_name VARCHAR(150) NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                notes TEXT,
                status VARCHAR(30) NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    } else {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('admin', 'user')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS medicines (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                medicine_name TEXT NOT NULL,
                category TEXT NOT NULL,
                stock INTEGER NOT NULL DEFAULT 0,
                price REAL NOT NULL DEFAULT 0,
                expiry_date TEXT NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS medicine_requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                medicine_name TEXT NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                notes TEXT,
                status TEXT NOT NULL DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }

    $adminEmail = 'admin@pharmacy.local';
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $adminEmail]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, :created_at)');
        $stmt->execute([
            ':username' => 'Admin',
            ':email' => $adminEmail,
            ':password' => password_hash('admin123', PASSWORD_DEFAULT),
            ':role' => 'admin',
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

try {
    $config = getDatabaseConfig();
    $driver = $config['driver'];

    if ($driver === 'mysql') {
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } else {
        $sqlitePath = __DIR__ . '/inventory.db';
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    initializeDatabase($pdo);
} catch (Throwable $e) {
    try {
        $fallback = new PDO('sqlite:' . __DIR__ . '/inventory.db', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $fallback->exec('PRAGMA foreign_keys = ON');
        initializeDatabase($fallback);
        $pdo = $fallback;
    } catch (Throwable $fallbackError) {
        $pdo = null;
    }
}

if ($pdo === null) {
    $pdo = new PDO('sqlite::memory:', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    initializeDatabase($pdo);
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUserRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

function requireRole(string $role): void
{
    if (!isLoggedIn() || currentUserRole() !== $role) {
        header('Location: login.php');
        exit;
    }
}

function redirectToDashboard(): void
{
    if (!isLoggedIn()) {
        return;
    }

    if (currentUserRole() === 'admin') {
        header('Location: admin-dashboard.php');
    } else {
        header('Location: user-dashboard.php');
    }
    exit;
}

function ensureValidPositiveNumber($value): float
{
    $number = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($number === false || $number < 0) {
        throw new InvalidArgumentException('Please provide a valid positive number.');
    }
    return (float) $number;
}

function ensureValidStock($value): int
{
    $stock = filter_var($value, FILTER_VALIDATE_INT);
    if ($stock === false || $stock < 0) {
        throw new InvalidArgumentException('Stock must be a valid non-negative number.');
    }
    return (int) $stock;
}

function displayMessage(string $type, string $message): void
{
    if (empty($message)) {
        return;
    }

    echo '<div class="alert alert-' . htmlspecialchars($type) . ' mt-3">' . htmlspecialchars($message) . '</div>';
}

function formatCurrency(float $value): string
{
    return '₱' . number_format($value, 2);
}

function getInventoryStats(PDO $pdo): array
{
    $stats = [
        'total_medicines' => 0,
        'total_stock' => 0,
        'low_stock' => 0,
        'expired' => 0,
    ];

    $stmt = $pdo->query('SELECT COUNT(*) AS total_medicines FROM medicines');
    $stats['total_medicines'] = (int) ($stmt->fetch()['total_medicines'] ?? 0);

    $stmt = $pdo->query('SELECT COALESCE(SUM(stock), 0) AS total_stock FROM medicines');
    $stats['total_stock'] = (int) ($stmt->fetch()['total_stock'] ?? 0);

    $stmt = $pdo->query("SELECT COUNT(*) AS low_stock FROM medicines WHERE stock <= 10");
    $stats['low_stock'] = (int) ($stmt->fetch()['low_stock'] ?? 0);

    $stmt = $pdo->query("SELECT COUNT(*) AS expired FROM medicines WHERE expiry_date < date('now')");
    $stats['expired'] = (int) ($stmt->fetch()['expired'] ?? 0);

    return $stats;
}

?>
