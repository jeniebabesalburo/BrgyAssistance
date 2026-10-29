<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';
try {
    $db = (new Database())->connect();
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(60) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS residents (
        resident_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(80) NOT NULL,
        middle_name VARCHAR(80) NULL,
        last_name VARCHAR(80) NOT NULL,
        sex ENUM('Male','Female','Prefer not to say') NOT NULL,
        birth_date DATE NULL,
        phone VARCHAR(30) NULL,
        purok VARCHAR(80) NOT NULL,
        address VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_resident_name (last_name, first_name),
        INDEX idx_purok (purok)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $db->exec("CREATE TABLE IF NOT EXISTS activity_logs (
        log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        description VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $check = $db->prepare('SELECT user_id FROM users WHERE username = :username');
    $check->execute(['username' => 'admin']);
    if (!$check->fetch()) {
        $insert = $db->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :hash)');
        $insert->execute(['username' => 'admin', 'hash' => password_hash('Admin@12345', PASSWORD_DEFAULT)]);
    }
    $message = 'Setup completed. The database tables and administrator account are ready.';
} catch (Throwable $e) {
    $error = 'Setup failed. Check the database name and credentials in config/database.php. Details: ' . $e->getMessage();
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup · BarangayHub</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body style="display:grid;place-items:center;min-height:100vh;padding:20px"><section class="panel" style="max-width:650px;width:100%;padding:28px"><div class="brand" style="color:#17432f;padding:0 0 20px"><span class="brand-mark">B</span><span>Barangay<span style="color:#329b62">Hub</span><small style="color:#7b8981">INITIAL SETUP</small></span></div><h1 style="font-family:Manrope">System setup</h1><?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?><p>Login username: <strong>admin</strong><br>Password: <strong>Admin@12345</strong></p><p><a class="btn btn-primary" href="login.php">Go to login →</a></p></div><?php else: ?><div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?><p class="page-note">For safety, delete or rename setup.php after setup is complete. Change the default password before using real resident data.</p></section></body></html>
