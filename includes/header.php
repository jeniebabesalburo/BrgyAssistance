<?php
$pageTitle = $pageTitle ?? 'Dashboard';
$activePage = $activePage ?? '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> · BarangayHub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="index.php"><span class="brand-mark">B</span><span>Barangay<span class="brand-light">Hub</span><small>RESIDENT SERVICES</small></span></a>
  <div class="nav-label">WORKSPACE</div>
  <nav class="nav-menu">
    <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php"><span class="nav-icon">▦</span> Dashboard</a>
    <a class="<?= $activePage === 'residents' ? 'active' : '' ?>" href="residents.php"><span class="nav-icon">♙</span> Resident records</a>
  </nav>
  <div class="sidebar-bottom"><div class="help-card"><span class="help-icon">✦</span><strong>Need a hand?</strong><p>Keep resident records accurate and up to date.</p></div>
    <div class="profile-mini"><span class="avatar"><?= e(strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1))) ?></span><span class="profile-name"><?= e($_SESSION['username'] ?? 'Administrator') ?><small>Administrator</small></span><a class="logout-icon" href="logout.php" title="Log out">↗</a></div>
  </div>
</aside>
<main class="main-content">
<header class="topbar"><button class="mobile-menu" id="menuToggle" aria-label="Toggle navigation">☰</button><div><span class="eyebrow">BARANGAY MANAGEMENT</span><h1><?= e($pageTitle) ?></h1></div><div class="topbar-right"><span class="status-dot"></span><span class="online-label">System online</span><span class="top-avatar"><?= e(strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1))) ?></span></div></header>
<div class="page-content">
