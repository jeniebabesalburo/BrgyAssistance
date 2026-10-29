<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Resident.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$resident = new Resident((new Database())->connect());
$record = $id ? $resident->getResident($id) : null;
if (!$record) { http_response_code(404); exit('Resident not found.'); }
$pageTitle = 'Resident profile'; $activePage = 'residents';
require __DIR__ . '/includes/header.php';
?>
<section class="welcome-row"><div><h2>Resident profile</h2><p>Review the full details for this resident record.</p></div><div style="display:flex;gap:8px;flex-wrap:wrap"><a class="btn btn-light" href="residents.php">← Back</a><a class="btn btn-primary" href="resident-form.php?id=<?= (int)$record['resident_id'] ?>">✎ Edit resident</a></div></section>
<section class="panel"><div class="panel-head"><div><h3><?= e(trim($record['first_name'].' '.$record['middle_name'].' '.$record['last_name'])) ?></h3><p>Resident ID · RES-<?= str_pad((string)$record['resident_id'], 4, '0', STR_PAD_LEFT) ?></p></div><span class="resident-avatar" style="width:48px;height:48px;font-size:15px"><?= e(strtoupper(substr($record['first_name'],0,1).substr($record['last_name'],0,1))) ?></span></div><div class="detail-grid">
<div class="detail-item"><small>First name</small><strong><?= e($record['first_name']) ?></strong></div><div class="detail-item"><small>Middle name</small><strong><?= e($record['middle_name'] ?: '—') ?></strong></div><div class="detail-item"><small>Last name</small><strong><?= e($record['last_name']) ?></strong></div><div class="detail-item"><small>Sex</small><strong><?= e($record['sex']) ?></strong></div><div class="detail-item"><small>Date of birth</small><strong><?= e($record['birth_date'] ? date('F j, Y', strtotime($record['birth_date'])) : '—') ?></strong></div><div class="detail-item"><small>Contact number</small><strong><?= e($record['phone'] ?: '—') ?></strong></div><div class="detail-item"><small>Purok / Zone</small><strong><?= e($record['purok']) ?></strong></div><div class="detail-item full"><small>Complete address</small><strong><?= e($record['address']) ?></strong></div><div class="detail-item"><small>Date registered</small><strong><?= e(date('F j, Y · g:i A', strtotime($record['created_at']))) ?></strong></div><div class="detail-item"><small>Last updated</small><strong><?= e(date('F j, Y · g:i A', strtotime($record['updated_at']))) ?></strong></div>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
