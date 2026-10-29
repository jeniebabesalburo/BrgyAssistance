<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $db = (new Database())->connect();
        $stmt = $db->prepare('SELECT user_id, username, password_hash FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php'); exit;
        }
        $error = 'The username or password is incorrect.';
    } catch (Throwable $e) {
        $error = 'Unable to connect to the database. Run setup.php and check your database settings.';
    }
}
function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign in · BarangayHub</title><link rel="stylesheet" href="assets/css/style.css"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet"></head>
<body><div class="login-page"><section class="login-visual"><a class="brand" href="#"><span class="brand-mark">B</span><span>Barangay<span class="brand-light">Hub</span><small>RESIDENT SERVICES</small></span></a><div class="login-message"><span class="eyebrow">COMMUNITY. CARE. CONNECTION.</span><h1>Better records.<br>Stronger communities.</h1><p>A simple, organized workspace for maintaining accurate resident information and supporting efficient barangay services.</p></div><div class="login-bottom">Resident Management System · Secure staff access</div></section><section class="login-form-side"><form class="login-form" method="post"><span class="eyebrow" style="color:#27804f">WELCOME BACK</span><h2>Sign in to your account</h2><p>Enter your staff credentials to continue.</p><?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>"><div class="field"><label for="username">Username</label><input class="form-control" id="username" name="username" autocomplete="username" placeholder="Enter your username" required></div><div class="field"><label for="password">Password</label><input class="form-control" id="password" type="password" name="password" autocomplete="current-password" placeholder="Enter your password" required></div><button class="btn btn-primary" type="submit">Sign in <span>→</span></button><div class="login-hint">First time setting up? Open <strong>setup.php</strong> once to create the tables and default administrator.</div></form></section></div><script src="assets/js/app.js"></script></body></html>
