<?php

require_once(__DIR__ . '/../includes/auth.php');

$staff = require_staff();

const MAX_IMAGE_BYTES = 3 * 1024 * 1024;
const IMAGE_TYPES     = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

$id       = (int) ($_GET['id'] ?? 0);
$existing = $id ? offers_find($id) : null;

if ($id && !$existing) {
    flash_set('danger', 'That offer is not there any more.');
    header('Location: offers.php');
    exit;
}

$editing = (bool) $existing;

$values = [
    'title'       => $existing['title']       ?? '',
    'destination' => $existing['destination'] ?? '',
    'description' => $existing['description'] ?? '',
    'price'       => isset($existing['price']) ? (string) $existing['price'] : '',
    'startDate'   => $existing['startDate']   ?? '',
    'endDate'     => $existing['endDate']     ?? '',
    'type'        => $existing['type']        ?? OFFER_TYPES[1],
    'alt'         => $existing['alt']         ?? '',
    'active'      => $existing['active']      ?? true,
    'bestseller'  => $existing['bestseller']  ?? false,
];

$errors = [];

function store_upload(array $file, array &$errors): ?string
{
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $errors['image'] = 'That image is too big. The limit is 3MB.';

        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        $errors['image'] = 'We could not upload that image. Try again.';

        return null;
    }

    if ($file['size'] > MAX_IMAGE_BYTES) {
        $errors['image'] = 'That image is too big. The limit is 3MB.';

        return null;
    }

    $info = getimagesize($file['tmp_name']);

    if (!$info || !isset(IMAGE_TYPES[$info['mime']])) {
        $errors['image'] = 'Upload a JPEG, PNG or WebP image.';

        return null;
    }

    if (!is_dir(OFFER_UPLOADS)) {
        mkdir(OFFER_UPLOADS, 0777, true);
    }

    $name = bin2hex(random_bytes(8)) . '.' . IMAGE_TYPES[$info['mime']];

    if (!move_uploaded_file($file['tmp_name'], OFFER_UPLOADS . '/' . $name)) {
        $errors['image'] = 'We could not save that image. Try again.';

        return null;
    }

    return 'assets/img/offers/' . $name;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    foreach (['title', 'destination', 'description', 'price', 'startDate', 'endDate', 'type', 'alt'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $values['active']     = isset($_POST['active']);
    $values['bestseller'] = isset($_POST['bestseller']);

    if ($values['title'] === '') {
        $errors['title'] = 'Give the offer a title, such as “Venetian Long Weekend”.';
    }

    if ($values['destination'] === '') {
        $errors['destination'] = 'Enter the destination, such as “Venice, Italy”.';
    }

    if ($values['description'] === '') {
        $errors['description'] = 'Describe what is included.';
    }

    if ($values['price'] === '') {
        $errors['price'] = 'Enter the price per person.';
    } elseif (!is_numeric($values['price']) || (float) $values['price'] < 0) {
        $errors['price'] = 'Enter the price as a number, without the pound sign.';
    }

    $start = date_create($values['startDate'] ?: 'invalid');
    $end   = date_create($values['endDate'] ?: 'invalid');

    if (!$start) {
        $errors['startDate'] = 'Choose the date the offer starts.';
    }

    if (!$end) {
        $errors['endDate'] = 'Choose the date the offer ends.';
    } elseif ($start && $end < $start) {
        $errors['endDate'] = 'The end date cannot be before the start date. Pick a later one.';
    }

    if (!in_array($values['type'], OFFER_TYPES, true)) {
        $errors['type'] = 'Choose one of the three package types.';
    }

    $uploaded = null;
    $sent     = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($sent) {
        $uploaded = store_upload($_FILES['image'], $errors);
    } elseif (!$editing) {
        $errors['image'] = 'Choose an image for the offer.';
    }

    if ($values['alt'] === '') {
        $errors['alt'] = 'Describe the image for anyone who cannot see it.';
    }

    if (!$errors) {
        $offer = [
            'id'          => $editing ? (int) $existing['id'] : 0,
            'title'       => $values['title'],
            'destination' => $values['destination'],
            'type'        => $values['type'],
            'description' => $values['description'],
            'price'       => round((float) $values['price'], 2),
            'startDate'   => $start->format('Y-m-d'),
            'endDate'     => $end->format('Y-m-d'),
            'image'       => $uploaded ?? $existing['image'],
            'alt'         => $values['alt'],
            'active'      => $values['active'],
            'bestseller'  => $values['bestseller'],
        ];

        offers_put($offer);

        flash_set('success', $editing
            ? '“' . $offer['title'] . '” has been updated.'
            : '“' . $offer['title'] . '” has been added.');

        header('Location: offers.php');
        exit;
    }
}

$pageTitle = $editing ? 'Edit Offer' : 'Add Offer';
$navHere   = 'offers';
require_once(__DIR__ . '/../includes/staff-header.php');
?>

<div class="col-lg-9 col-xl-7">

  <h1 class="h2 mb-1"><?= $editing ? 'Edit Offer' : 'Add Offer' ?></h1>
  <p class="text-body-secondary mb-4">
    <?= $editing
        ? 'Changes appear on the website as soon as you save, as long as the offer is active.'
        : 'A new offer goes live on the website straight away if you leave it active.' ?>
  </p>

  <?php if ($errors): ?>
    <?php $errorSummaryFor = 'form'; ?>
    <?php require(__DIR__ . '/../includes/error-summary.php'); ?>
  <?php endif; ?>

  <form method="post" action="offer-form.php<?= $editing ? '?id=' . (int) $existing['id'] : '' ?>"
        enctype="multipart/form-data" class="row g-3" novalidate>

    <div class="col-12">
      <label for="title" class="form-label">Offer title</label>
      <input type="text" class="form-control<?= invalid($errors, 'title') ?>"
             id="title" name="title" value="<?= e($values['title']) ?>"
             <?= isset($errors['title']) ? 'aria-invalid="true" aria-describedby="title-error"' : '' ?> required>
      <?= field_error($errors, 'title') ?>
    </div>

    <div class="col-md-6">
      <label for="destination" class="form-label">Destination</label>
      <input type="text" class="form-control<?= invalid($errors, 'destination') ?>"
             id="destination" name="destination" value="<?= e($values['destination']) ?>"
             <?= isset($errors['destination']) ? 'aria-invalid="true" aria-describedby="destination-error"' : '' ?> required>
      <?= field_error($errors, 'destination') ?>
    </div>

    <div class="col-md-6">
      <label for="type" class="form-label">Package type</label>
      <select class="form-select<?= invalid($errors, 'type') ?>" id="type" name="type"
              <?= isset($errors['type']) ? 'aria-invalid="true" aria-describedby="type-error"' : '' ?> required>
        <?php foreach (OFFER_TYPES as $type): ?>
          <option value="<?= e($type) ?>" <?= $values['type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
        <?php endforeach; ?>
      </select>
      <?= field_error($errors, 'type') ?>
    </div>

    <div class="col-12">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control<?= invalid($errors, 'description') ?>"
                id="description" name="description" rows="4" aria-describedby="description-help<?= isset($errors['description']) ? ' description-error' : '' ?>"
                <?= isset($errors['description']) ? 'aria-invalid="true"' : '' ?> required><?= e($values['description']) ?></textarea>
      <?= field_error($errors, 'description') ?>
      <div class="form-text" id="description-help">Say what is included: nights, board, transfers and which airport.</div>
    </div>

    <div class="col-md-4">
      <label for="price" class="form-label">Price per person</label>
      <div class="input-group<?= isset($errors['price']) ? ' has-validation' : '' ?>">
        <span class="input-group-text" aria-hidden="true">£</span>
        <input type="number" class="form-control<?= invalid($errors, 'price') ?>"
               id="price" name="price" value="<?= e($values['price']) ?>" min="0" step="1"
               <?= isset($errors['price']) ? 'aria-invalid="true" aria-describedby="price-error"' : '' ?> required>
        <?= field_error($errors, 'price') ?>
      </div>
    </div>

    <div class="col-md-4">
      <label for="startDate" class="form-label">Start date</label>
      <input type="date" class="form-control<?= invalid($errors, 'startDate') ?>"
             id="startDate" name="startDate" value="<?= e($values['startDate']) ?>"
             <?= isset($errors['startDate']) ? 'aria-invalid="true" aria-describedby="startDate-error"' : '' ?> required>
      <?= field_error($errors, 'startDate') ?>
    </div>

    <div class="col-md-4">
      <label for="endDate" class="form-label">End date</label>
      <input type="date" class="form-control<?= invalid($errors, 'endDate') ?>"
             id="endDate" name="endDate" value="<?= e($values['endDate']) ?>"
             <?= isset($errors['endDate']) ? 'aria-invalid="true" aria-describedby="endDate-error"' : '' ?> required>
      <?= field_error($errors, 'endDate') ?>
    </div>

    <div class="col-12">
      <label for="image" class="form-label">
        Image
        <?php if ($editing): ?>
          <span class="text-body-secondary fw-normal">(leave empty to keep the current one)</span>
        <?php endif; ?>
      </label>

      <?php if ($editing && !empty($existing['image'])): ?>
        <div class="d-flex align-items-center gap-3 mb-2">
          <img src="../<?= e($existing['image']) ?>" alt="<?= e($existing['alt']) ?>"
               width="120" height="90" class="object-fit-cover rounded border">
          <span class="text-body-secondary small">Current image</span>
        </div>
      <?php endif; ?>

      <input type="file" class="form-control<?= invalid($errors, 'image') ?>"
             id="image" name="image" accept="image/jpeg,image/png,image/webp"
             aria-describedby="image-help<?= isset($errors['image']) ? ' image-error' : '' ?>"
             <?= isset($errors['image']) ? 'aria-invalid="true"' : '' ?>>
      <?= field_error($errors, 'image') ?>
      <div class="form-text" id="image-help">JPEG, PNG or WebP, up to 3MB. Landscape photos work best.</div>
    </div>

    <div class="col-12">
      <label for="alt" class="form-label">Image description</label>
      <input type="text" class="form-control<?= invalid($errors, 'alt') ?>"
             id="alt" name="alt" value="<?= e($values['alt']) ?>"
             aria-describedby="alt-help<?= isset($errors['alt']) ? ' alt-error' : '' ?>"
             <?= isset($errors['alt']) ? 'aria-invalid="true"' : '' ?> required>
      <?= field_error($errors, 'alt') ?>
      <div class="form-text" id="alt-help">
        This gets read out to anyone using a screen reader, so describe the photo instead of repeating the title.
      </div>
    </div>

    <div class="col-12">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" value="1"
               id="active" name="active" <?= $values['active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="active">
          Active: show this offer on the website
        </label>
      </div>

      <?php
            ?>
      <div class="form-check form-switch mt-2">
        <input class="form-check-input" type="checkbox" role="switch" value="1"
               id="bestseller" name="bestseller" <?= $values['bestseller'] ? 'checked' : '' ?>
               aria-describedby="bestseller-help">
        <label class="form-check-label" for="bestseller">
          Bestselling: feature this offer on the home page
        </label>
      </div>
      <div class="form-text" id="bestseller-help">
        The home page shows the three newest bestsellers. If an offer is not active it will not
        show up there, ticked or not.
      </div>
    </div>

    <div class="col-12 d-grid gap-2 d-sm-flex mt-4">
      <button type="submit" class="btn btn-primary"><?= $editing ? 'Save changes' : 'Add offer' ?></button>
      <a class="btn btn-outline-secondary" href="offers.php">Cancel</a>
    </div>

  </form>

</div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
