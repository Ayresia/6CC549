<?php

require_once(__DIR__ . '/../includes/auth.php');

$staff = require_staff();

$id       = (int) ($_GET['id'] ?? 0);
$existing = $id ? branches_find($id) : null;

if ($id && !$existing) {
    flash_set('danger', 'That branch is not there any more.');
    header('Location: branches.php');
    exit;
}

$editing = (bool) $existing;

$values = [
    'name'         => $existing['name']     ?? '',
    'location'     => $existing['location'] ?? '',
    'street'       => $existing['street']   ?? '',
    'area'         => $existing['area']     ?? '',
    'phone'        => $existing['phone']    ?? '',
    'tel'          => $existing['tel']      ?? '',
    'email'        => $existing['email']    ?? '',
    'hours'        => $existing['hours']    ?? '',
    'isHeadOffice' => $existing['isHeadOffice'] ?? false,
];

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    foreach (['name', 'location', 'street', 'area', 'phone', 'tel', 'email', 'hours'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['isHeadOffice'] = isset($_POST['isHeadOffice']);

    if ($values['name'] === '') {
        $errors['name'] = 'Give the branch a name, such as “Manchester”.';
    }

    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter the branch email address like name@example.com, or leave it empty.';
    }

    if ($values['phone'] !== '' && $values['tel'] === '') {
        $errors['tel'] = 'Enter the number the link should dial, for example +441614960117.';
    }

    if ($values['tel'] !== '') {
        if ($values['phone'] === '') {
            $errors['phone'] = 'Enter the number as it should look on the page, for example 0161 496 0117.';
        } elseif (!preg_match('/^\+?[0-9]{7,15}$/', $values['tel'])) {
            $errors['tel'] = 'Enter the dialling number as digits only, with no spaces, such as +441614960117.';
        }
    }

    if (!$errors) {
        $branch = [
            'id'           => $editing ? (int) $existing['id'] : 0,
            'name'         => $values['name'],
            'location'     => $values['location'],
            'street'       => $values['street'],
            'area'         => $values['area'],
            'phone'        => $values['phone'],
            'tel'          => $values['tel'],
            'email'        => $values['email'],
            'hours'        => $values['hours'],
            'isHeadOffice' => $values['isHeadOffice'],
        ];

        $savedId = branches_put($branch);

        if ($values['isHeadOffice']) {
            branches_clear_head_office($savedId);
        }

        flash_set('success', $editing
            ? '“' . $branch['name'] . '” has been updated.'
            : '“' . $branch['name'] . '” has been added.');

        header('Location: branches.php');
        exit;
    }
}

$pageTitle = $editing ? 'Edit Branch' : 'Add Branch';
$navHere   = 'branches';
require_once(__DIR__ . '/../includes/staff-header.php');
?>

<div class="col-lg-9 col-xl-7">

  <h1 class="h2 mb-1"><?= $editing ? 'Edit Branch' : 'Add Branch' ?></h1>
  <p class="text-body-secondary mb-4">
    What you save here shows up on the branches page, the contact page and the home page straight away.
  </p>

  <?php if ($errors): ?>
    <?php $errorSummaryFor = 'form'; ?>
    <?php require(__DIR__ . '/../includes/error-summary.php'); ?>
  <?php endif; ?>

  <form method="post" action="branch-form.php<?= $editing ? '?id=' . (int) $existing['id'] : '' ?>"
        class="row g-3" novalidate>

    <div class="col-md-6">
      <label for="name" class="form-label">
        Branch name <span class="text-danger" aria-hidden="true">*</span>
      </label>
      <input type="text" class="form-control<?= invalid($errors, 'name') ?>"
             id="name" name="name" value="<?= e($values['name']) ?>"
             <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?> required>
      <?= field_error($errors, 'name') ?>
    </div>

    <div class="col-md-6">
      <label for="location" class="form-label">
        Where it is <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control" id="location" name="location"
             value="<?= e($values['location']) ?>" aria-describedby="location-help">
      <div class="form-text" id="location-help">The line that sits under the name, for example “City centre, on Harbrook Row”.</div>
    </div>

    <div class="col-md-6">
      <label for="street" class="form-label">
        Street <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control" id="street" name="street" value="<?= e($values['street']) ?>">
    </div>

    <div class="col-md-6">
      <label for="area" class="form-label">
        Town and postcode <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control" id="area" name="area" value="<?= e($values['area']) ?>">
    </div>

    <div class="col-md-6">
      <label for="phone" class="form-label">
        Phone number <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control<?= invalid($errors, 'phone') ?>"
             id="phone" name="phone" value="<?= e($values['phone']) ?>"
             aria-describedby="phone-help<?= isset($errors['phone']) ? ' phone-error' : '' ?>"
             <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
      <?= field_error($errors, 'phone') ?>
      <div class="form-text" id="phone-help">How the number should look on the page, for example 0161 496 0117.</div>
    </div>

    <div class="col-md-6">
      <label for="tel" class="form-label">
        Dialling number <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control<?= invalid($errors, 'tel') ?>"
             id="tel" name="tel" value="<?= e($values['tel']) ?>"
             aria-describedby="tel-help<?= isset($errors['tel']) ? ' tel-error' : '' ?>"
             <?= isset($errors['tel']) ? 'aria-invalid="true"' : '' ?>>
      <?= field_error($errors, 'tel') ?>
      <div class="form-text" id="tel-help">What the tap to call link actually dials. Digits only, for example +441614960117.</div>
    </div>

    <div class="col-md-6">
      <label for="email" class="form-label">
        Email address <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="email" class="form-control<?= invalid($errors, 'email') ?>"
             id="email" name="email" value="<?= e($values['email']) ?>"
             <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>>
      <?= field_error($errors, 'email') ?>
    </div>

    <div class="col-md-6">
      <label for="hours" class="form-label">
        Opening hours <span class="text-body-secondary fw-normal">(optional)</span>
      </label>
      <input type="text" class="form-control" id="hours" name="hours" value="<?= e($values['hours']) ?>"
             aria-describedby="hours-help">
      <div class="form-text" id="hours-help">For example “Monday to Saturday, 9am to 5.30pm”.</div>
    </div>

    <div class="col-12">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" value="1"
               id="isHeadOffice" name="isHeadOffice" <?= $values['isHeadOffice'] ? 'checked' : '' ?>
               aria-describedby="isHeadOffice-help">
        <label class="form-check-label" for="isHeadOffice">
          This is the head office
        </label>
      </div>
      <div class="form-text" id="isHeadOffice-help">
        The head office gets its own section and is not counted as one of the high street branches.
        Only one branch can be the head office, so ticking this unticks whichever one had it.
      </div>
    </div>

    <div class="col-12 d-grid gap-2 d-sm-flex mt-4">
      <button type="submit" class="btn btn-primary"><?= $editing ? 'Save changes' : 'Add branch' ?></button>
      <a class="btn btn-outline-secondary" href="branches.php">Cancel</a>
    </div>

  </form>

</div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
