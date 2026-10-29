<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Resident.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: residents.php'); exit; }
verifyCsrf();
$db = (new Database())->connect();
$resident = new Resident($db);
$action = (string)($_POST['action'] ?? '');
$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;

if ($action === 'delete') {
    try {
        if ($id && $resident->deleteResident($id)) {
            $_SESSION['flash'] = 'Resident record deleted successfully.';
        } else {
            $_SESSION['flash'] = 'No resident record was deleted.';
        }
    } catch (Throwable $e) {
        $_SESSION['flash'] = 'Unable to delete the record. Please try again.';
    }
    header('Location: residents.php'); exit;
}

$data = [
    'first_name' => trim((string)($_POST['first_name'] ?? '')),
    'middle_name' => trim((string)($_POST['middle_name'] ?? '')),
    'last_name' => trim((string)($_POST['last_name'] ?? '')),
    'sex' => trim((string)($_POST['sex'] ?? '')),
    'birth_date' => trim((string)($_POST['birth_date'] ?? '')),
    'phone' => trim((string)($_POST['phone'] ?? '')),
    'purok' => trim((string)($_POST['purok'] ?? '')),
    'address' => trim((string)($_POST['address'] ?? '')),
];
$errors = [];
if ($data['first_name'] === '' || mb_strlen($data['first_name']) > 80) $errors[] = 'First name is required and must be 80 characters or fewer.';
if (mb_strlen($data['middle_name']) > 80) $errors[] = 'Middle name must be 80 characters or fewer.';
if ($data['last_name'] === '' || mb_strlen($data['last_name']) > 80) $errors[] = 'Last name is required and must be 80 characters or fewer.';
if (!in_array($data['sex'], ['Male','Female','Prefer not to say'], true)) $errors[] = 'Please select a valid sex option.';
if ($data['purok'] === '' || mb_strlen($data['purok']) > 80) $errors[] = 'Purok / Zone is required and must be 80 characters or fewer.';
if ($data['address'] === '' || mb_strlen($data['address']) > 255) $errors[] = 'Address is required and must be 255 characters or fewer.';
if (mb_strlen($data['phone']) > 30) $errors[] = 'Contact number must be 30 characters or fewer.';
if ($data['birth_date'] !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
    if (!$date || $date->format('Y-m-d') !== $data['birth_date'] || $date > new DateTime('today')) $errors[] = 'Enter a valid date of birth that is not in the future.';
}
if ($errors) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $data;
    header('Location: resident-form.php' . (($action === 'update' && $id) ? '?id='.$id : '')); exit;
}
$data['middle_name'] = $data['middle_name'] !== '' ? $data['middle_name'] : null;
$data['birth_date'] = $data['birth_date'] !== '' ? $data['birth_date'] : null;
$data['phone'] = $data['phone'] !== '' ? $data['phone'] : null;
try {
    if ($action === 'create') {
        $newId = $resident->addResident($data);
        $_SESSION['flash'] = 'Resident record created successfully (RES-' . str_pad((string)$newId, 4, '0', STR_PAD_LEFT) . ').';
    } elseif ($action === 'update' && $id) {
        $resident->updateResident($id, $data);
        $_SESSION['flash'] = 'Resident record updated successfully.';
    } else {
        $_SESSION['flash'] = 'Invalid action. Please try again.';
    }
} catch (Throwable $e) {
    $_SESSION['form_errors'] = ['The record could not be saved. Check your input and database connection, then try again.'];
    $_SESSION['form_old'] = $data;
    header('Location: resident-form.php' . (($action === 'update' && $id) ? '?id='.$id : '')); exit;
}
header('Location: residents.php'); exit;
