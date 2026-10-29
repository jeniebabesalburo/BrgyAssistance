<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Resident.php';
$resident = new Resident((new Database())->connect());
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$record = $id ? $resident->getResident($id) : null;
if ($id && !$record) { http_response_code(404); exit('Resident not found.'); }
$pageTitle = $id ? 'Edit resident' : 'Add resident'; $activePage = 'residents';
$values = $record ?: ['first_name'=>'','middle_name'=>'','last_name'=>'','sex'=>'','birth_date'=>'','phone'=>'','purok'=>'','address'=>''];
$errors = (array)($_SESSION['form_errors'] ?? []); unset($_SESSION['form_errors']);
$old = (array)($_SESSION['form_old'] ?? []); unset($_SESSION['form_old']);
if ($old) $values = array_merge($values, $old);
require __DIR__ . '/includes/header.php';
?>
<section class="welcome-row"><div><h2><?= $id ? 'Update resident information' : 'Register a resident' ?></h2><p><?= $id ? 'Review the details and save your changes.' : 'Enter accurate information to create a resident record.' ?></p></div><a class="btn btn-light" href="residents.php">← Back to records</a></section>
<?php if ($errors): ?><div class="alert alert-error"><strong>Please check the following:</strong><ul style="margin-bottom:0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="resident-action.php" class="panel form-panel"><input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>"><input type="hidden" name="action" value="<?= $id ? 'update' : 'create' ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
<div class="form-body"><h3 class="form-section-title">Personal information</h3><div class="form-grid">
<div class="field"><label for="first_name">First name <span>*</span></label><input class="form-control" id="first_name" name="first_name" maxlength="80" required value="<?= e($values['first_name'] ?? '') ?>" placeholder="Enter first name"></div>
<div class="field"><label for="middle_name">Middle name</label><input class="form-control" id="middle_name" name="middle_name" maxlength="80" value="<?= e($values['middle_name'] ?? '') ?>" placeholder="Enter middle name (optional)"></div>
<div class="field"><label for="last_name">Last name <span>*</span></label><input class="form-control" id="last_name" name="last_name" maxlength="80" required value="<?= e($values['last_name'] ?? '') ?>" placeholder="Enter last name"></div>
<div class="field"><label for="sex">Sex <span>*</span></label><select class="form-control" id="sex" name="sex" required><option value="">Select sex</option><?php foreach (['Male','Female','Prefer not to say'] as $option): ?><option value="<?= e($option) ?>" <?= ($values['sex'] ?? '') === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?></select></div>
<div class="field"><label for="birth_date">Date of birth</label><input class="form-control" type="date" id="birth_date" name="birth_date" max="<?= date('Y-m-d') ?>" value="<?= e($values['birth_date'] ?? '') ?>"></div>
<div class="field"><label for="phone">Contact number</label><input class="form-control" id="phone" name="phone" maxlength="30" value="<?= e($values['phone'] ?? '') ?>" placeholder="e.g. 09XX XXX XXXX"></div>
<div class="field"><label for="purok">Purok / Zone <span>*</span></label><input class="form-control" id="purok" name="purok" maxlength="80" required value="<?= e($values['purok'] ?? '') ?>" placeholder="e.g. Purok 1"></div>
<div class="field full"><label for="address">Complete address <span>*</span></label><textarea class="form-control" id="address" name="address" rows="3" maxlength="255" required placeholder="House number, street, sitio, or other address details"><?= e($values['address'] ?? '') ?></textarea></div>
</div><p class="page-note">Fields marked with * are required. Use resident information only for authorized barangay purposes.</p></div>
<div class="form-actions"><a class="btn btn-light" href="residents.php">Cancel</a><button class="btn btn-primary" type="submit"><?= $id ? 'Save changes' : 'Create resident record' ?> <span>→</span></button></div></form>
<?php require __DIR__ . '/includes/footer.php'; ?>
