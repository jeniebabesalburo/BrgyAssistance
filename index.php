<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Resident.php';
try {
    $resident = new Resident((new Database())->connect());
    $stats = $resident->getStats();
    $activity = $resident->getRecentActivity();
    $latest = array_slice($resident->getResidents(), 0, 5);
} catch (Throwable $e) {
    // TEMPORARY DEBUG: shows the real error. Restore the generic message once fixed.
    http_response_code(500);
    error_log($e->getMessage());
    exit('Error: ' . $e->getMessage());
}
$pageTitle = 'Dashboard'; $activePage = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="welcome-row"><div><h2>Good day, <?= e($_SESSION['username'] ?? 'Administrator') ?> 👋</h2><p>Here’s what’s happening with your resident records today.</p></div><a class="btn btn-primary" href="resident-form.php"><span>＋</span> Add resident</a></section>
<section class="stats-grid">
  <article class="stat-card"><div class="stat-top"><span>Total residents</span><span class="stat-icon">♙</span></div><div class="stat-value"><?= number_format($stats['total']) ?></div><div class="stat-foot">All registered resident records</div></article>
  <article class="stat-card"><div class="stat-top"><span>Puroks represented</span><span class="stat-icon">⌖</span></div><div class="stat-value"><?= number_format($stats['puroks']) ?></div><div class="stat-foot">Distinct puroks with residents</div></article>
  <article class="stat-card"><div class="stat-top"><span>Added this week</span><span class="stat-icon">↗</span></div><div class="stat-value"><?= number_format($stats['recent']) ?></div><div class="stat-foot"><em>Last 7 days</em> · New records</div></article>
</section>
<section class="content-grid"><article class="panel"><div class="panel-head"><div><h3>Recently added residents</h3><p>Latest records added to your registry</p></div><a class="text-link" href="residents.php">View all →</a></div>
<?php if (!$latest): ?><div class="empty-state">No residents yet. Add your first resident to get started.</div><?php else: ?><div class="table-wrap"><table><thead><tr><th>Resident</th><th>Purok</th><th>Contact</th><th>Added</th></tr></thead><tbody><?php foreach ($latest as $r): ?><tr><td><div class="resident-name"><span class="resident-avatar"><?= e(strtoupper(substr($r['first_name'],0,1).substr($r['last_name'],0,1))) ?></span><?= e($r['first_name'].' '.$r['last_name']) ?></div></td><td><?= e($r['purok']) ?></td><td><?= e($r['phone'] ?: '—') ?></td><td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></article>
<article class="panel"><div class="panel-head"><div><h3>Recent activity</h3><p>A simple audit trail of changes</p></div><span class="stat-icon">↻</span></div><div class="activity-list"><?php if (!$activity): ?><div class="empty-state">Activity will appear here as records are managed.</div><?php else: foreach ($activity as $item): ?><div class="activity-item"><span class="activity-mark">✓</span><div class="activity-copy"><?= e($item['description']) ?><small><?= e(date('M j, Y · g:i A', strtotime($item['created_at']))) ?></small></div></div><?php endforeach; endif; ?></div></article></section>
<section class="panel" style="margin-top:18px"><div class="panel-head"><div><h3>Quick actions</h3><p>Common tasks to keep your registry organized</p></div></div><div class="quick-links"><a class="quick-link" href="resident-form.php"><span class="quick-icon">＋</span><span>Add a resident<small>Register a new resident record</small></span><span class="arrow">→</span></a><a class="quick-link" href="residents.php"><span class="quick-icon">⌕</span><span>Find a resident<small>Search by name, ID, phone, purok, or address</small></span><span class="arrow">→</span></a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>