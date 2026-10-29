<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Resident.php';
$db = (new Database())->connect();
$resident = new Resident($db);
$keyword = trim((string)($_GET['q'] ?? ''));
$records = $keyword !== '' ? $resident->searchResidents($keyword) : $resident->getResidents();
$pageTitle = 'Resident records'; $activePage = 'residents';
$flash = (string)($_SESSION['flash'] ?? ''); unset($_SESSION['flash']);
require __DIR__ . '/includes/header.php';
?>
<section class="welcome-row"><div><h2>Resident registry</h2><p>Manage resident information in one organized place.</p></div><a class="btn btn-primary" href="resident-form.php"><span>＋</span> Add resident</a></section>
<?php if ($flash): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>
<section class="panel table-panel"><div class="panel-head"><div><h3>All residents</h3><p><?= count($records) ?> record<?= count($records) === 1 ? '' : 's' ?><?= $keyword !== '' ? ' matching “'.e($keyword).'”' : ' in the registry' ?></p></div><span class="stat-icon">♙</span></div>
<div class="toolbar"><form class="search-form" method="get"><div class="search-wrap"><span>⌕</span><input class="search-input" type="search" name="q" value="<?= e($keyword) ?>" placeholder="Search name, ID, phone, purok or address..." aria-label="Search residents"></div><button class="btn btn-light" type="submit">Search</button><?php if ($keyword !== ''): ?><a class="btn btn-light" href="residents.php">Clear</a><?php endif; ?></form><span class="text-link">PDO · Prepared statements</span></div>
<div class="table-wrap"><table><thead><tr><th>Resident ID</th><th>Resident name</th><th>Sex</th><th>Phone</th><th>Purok</th><th>Registered</th><th>Actions</th></tr></thead><tbody>
<?php if (!$records): ?><tr><td colspan="7"><div class="empty-state">No matching residents found. Try another keyword or add a new record.</div></td></tr><?php else: foreach ($records as $r): ?><tr><td><span class="id-pill">RES-<?= str_pad((string)$r['resident_id'], 4, '0', STR_PAD_LEFT) ?></span></td><td><div class="resident-name"><span class="resident-avatar"><?= e(strtoupper(substr($r['first_name'],0,1).substr($r['last_name'],0,1))) ?></span><span><?= e(trim($r['first_name'].' '.$r['middle_name'].' '.$r['last_name'])) ?></span></div></td><td><?= e($r['sex']) ?></td><td><?= e($r['phone'] ?: '—') ?></td><td><?= e($r['purok']) ?></td><td><?= e(date('M j, Y', strtotime($r['created_at']))) ?></td><td><div class="row-actions"><a class="icon-btn" href="resident-view.php?id=<?= (int)$r['resident_id'] ?>" title="View">↗</a><a class="icon-btn" href="resident-form.php?id=<?= (int)$r['resident_id'] ?>" title="Edit">✎</a><form method="post" action="resident-action.php" style="margin:0"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['resident_id'] ?>"><button class="icon-btn delete" type="submit" data-confirm="Delete this resident record? This cannot be undone." title="Delete">×</button></form></div></td></tr><?php endforeach; endif; ?>
</tbody></table></div><div class="table-footer"><span>Showing <?= count($records) ?> resident record<?= count($records) === 1 ? '' : 's' ?></span><span>Records are stored in your MySQL database</span></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
