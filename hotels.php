<?php

require_once(__DIR__ . '/includes/auth.php');

function hotel_nights(string $checkIn, string $checkOut): int
{
    $in  = date_create($checkIn);
    $out = date_create($checkOut);

    if (!$in || !$out || $out <= $in) {
        return 0;
    }

    return (int) $in->diff($out)->days;
}

function hotel_rating(float $rating): string
{
    return rtrim(rtrim(number_format($rating, 1), '0'), '.') . ' out of 5';
}

$today = date('Y-m-d');

$destinations = hotels_destinations();

$values = [
    'destination' => trim((string) ($_GET['destination'] ?? '')),
    'checkIn'     => trim((string) ($_GET['checkIn'] ?? '')),
    'checkOut'    => trim((string) ($_GET['checkOut'] ?? '')),
    'maxPrice'    => trim((string) ($_GET['maxPrice'] ?? '')),
];

$errors  = [];
$results = [];

if ($values['destination'] !== '' && !in_array($values['destination'], $destinations, true)) {
    $errors['destination'] = 'Select a destination from the list. We do not have rooms in that one.';
}

if ($values['checkIn'] !== '' && !is_date($values['checkIn'])) {
    $errors['checkIn'] = 'Enter the check in date as a real date, for example 12/10/2026.';
} elseif ($values['checkIn'] === '' && $values['checkOut'] !== '') {
    $errors['checkIn'] = 'Select a check in date as well, so we know the whole stay.';
}

if ($values['checkOut'] !== '' && !is_date($values['checkOut'])) {
    $errors['checkOut'] = 'Enter the check out date as a real date, for example 16/10/2026.';
} elseif ($values['checkOut'] === '' && $values['checkIn'] !== '') {
    $errors['checkOut'] = 'Select a check out date as well, so we know the whole stay.';
} elseif (!isset($errors['checkIn']) && $values['checkIn'] !== '' && $values['checkOut'] !== ''
    && $values['checkOut'] <= $values['checkIn']) {
    $errors['checkOut'] = 'Check out must be after check in. Choose a later date, or a stay of at least one night.';
}

if ($values['maxPrice'] !== '' && (!is_numeric($values['maxPrice']) || (float) $values['maxPrice'] < 0)) {
    $errors['maxPrice'] = 'Enter a maximum price as a number, without the pound sign.';
}

if (!$errors) {
    $results = hotels_search([
        'destination' => $values['destination'],
        'checkIn'     => $values['checkIn'],
        'checkOut'    => $values['checkOut'],
        'maxPrice'    => $values['maxPrice'] === '' ? 0 : (float) $values['maxPrice'],
    ]);
}

$filter = $values['destination'] !== ''
    || $values['checkIn'] !== ''
    || $values['checkOut'] !== ''
    || $values['maxPrice'] !== '';

$nights = hotel_nights($values['checkIn'], $values['checkOut']);

$stayDates = date_range($values['checkIn'], $values['checkOut']);

$pageTitle       = 'Hotel Search';
$pageDescription = 'Search Book & Board hotels by destination and your check in and check out dates, and filter the results by price per night.';
$navHere         = 'hotels';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Hotel Search</h1>
        <p class="lead mb-0">
          Every room we hold a rate on, in the places our agents know. Search by where you are going
          and the nights you need, then call the branch. We do not take bookings online.
        </p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">

      <?php if ($errors): ?>
        <?php $errorSummaryFor = 'search'; ?>
        <?php require(__DIR__ . '/includes/error-summary.php'); ?>
      <?php endif; ?>

      <form method="get" action="hotels.php" class="card mb-5" novalidate>
        <div class="card-body p-4">

          <fieldset class="mb-0">
            <legend class="h5 mb-3">Find a room</legend>

            <div class="row g-3">

              <div class="col-md-6 col-lg-3">
                <label for="destination" class="form-label">Destination</label>
                <select class="form-select<?= invalid($errors, 'destination') ?>"
                        id="destination" name="destination"
                        <?= isset($errors['destination']) ? 'aria-invalid="true" aria-describedby="destination-error"' : '' ?>>
                  <option value="">Any destination</option>
                  <?php foreach ($destinations as $destination): ?>
                    <option value="<?= e($destination) ?>" <?= $values['destination'] === $destination ? 'selected' : '' ?>><?= e($destination) ?></option>
                  <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'destination') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="checkIn" class="form-label">Check in</label>
                <input type="date" class="form-control<?= invalid($errors, 'checkIn') ?>"
                       id="checkIn" name="checkIn" value="<?= e($values['checkIn']) ?>"
                       min="<?= e($today) ?>"
                       <?= isset($errors['checkIn']) ? 'aria-describedby="checkIn-error"' : '' ?>
                       <?= isset($errors['checkIn']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'checkIn') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="checkOut" class="form-label">Check out</label>
                <input type="date" class="form-control<?= invalid($errors, 'checkOut') ?>"
                       id="checkOut" name="checkOut" value="<?= e($values['checkOut']) ?>"
                       min="<?= e($today) ?>"
                       <?= isset($errors['checkOut']) ? 'aria-describedby="checkOut-error"' : '' ?>
                       <?= isset($errors['checkOut']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'checkOut') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="maxPrice" class="form-label">Maximum price per night</label>
                <div class="input-group<?= isset($errors['maxPrice']) ? ' has-validation' : '' ?>">
                  <span class="input-group-text" aria-hidden="true">£</span>
                  <input type="number" class="form-control<?= invalid($errors, 'maxPrice') ?>"
                         id="maxPrice" name="maxPrice" value="<?= e($values['maxPrice']) ?>"
                         min="0" step="10" inputmode="numeric"
                         <?= isset($errors['maxPrice']) ? 'aria-describedby="maxPrice-error"' : '' ?>
                         <?= isset($errors['maxPrice']) ? 'aria-invalid="true"' : '' ?>>
                  <?= field_error($errors, 'maxPrice') ?>
                </div>
              </div>

            </div>
          </fieldset>

          <div class="d-grid d-sm-block mt-4">
            <button type="submit" class="btn btn-primary">Search hotels</button>
          </div>

        </div>
      </form>

      <?php if (!$results): ?>

        <h2 class="h5">No hotels found</h2>
        <p class="mb-0">
          <?= $filter
              ? 'Nothing we hold matches all of that. Try changing one of your filters above.'
              : 'We have no hotels loaded at the moment. Please check back soon.' ?>
        </p>

      <?php else: ?>

        <h2 class="visually-hidden">
          <?= count($results) ?> hotel<?= count($results) === 1 ? '' : 's' ?><?php
          ?><?= $values['destination'] !== '' ? ' in ' . e($values['destination']) : '' ?>
        </h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
          <?php foreach ($results as $hotel): ?>
            <div class="col">
              <article class="card h-100">

                <?php

                      ?>
                <?php if ($hotel['image'] !== ''): ?>
                  <div class="ratio ratio-4x3 card-img-top overflow-hidden">
                    <img src="<?= e($hotel['image']) ?>" class="object-fit-cover"
                         alt="<?= e($hotel['alt']) ?>" loading="lazy">
                  </div>
                <?php endif; ?>

                <div class="card-body d-flex flex-column">

                  <p class="offer-type mb-2">
                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                    <?= e(hotel_rating($hotel['rating'])) ?>
                  </p>

                  <h3 class="card-title h5 mb-1"><?= e($hotel['name']) ?></h3>
                  <p class="text-body-secondary mb-2"><?= e($hotel['destination']) ?></p>
                  <p class="card-text"><?= e($hotel['description']) ?></p>

                  <div class="fare mt-auto">
                    <p class="fare-line mb-1">
                      <span class="fare-label">Nights</span>
                      <span class="fare-value"><?= e($stayDates) ?></span>
                    </p>
                    <p class="fare-line mb-1">
                      <span class="fare-label">From</span>
                      <span class="fare-price">
                        <span class="fare-amount"><?= e(money($hotel['pricePerNight'])) ?></span>
                        <span class="fare-unit">per night</span>
                      </span>
                    </p>
                    <p class="fare-line mb-3">
                      <span class="fare-label">Stay</span>
                      <span class="fare-value"><?= e(money($hotel['pricePerNight'] * $nights)) ?> for <?= $nights ?> night<?= $nights === 1 ? '' : 's' ?></span>
                    </p>

                    <a class="btn btn-primary w-100" href="contact.php"
                       aria-label="Enquire about this hotel: <?= e($hotel['name']) ?>, <?= e($hotel['destination']) ?>, <?= e($stayDates) ?>">Enquire about this hotel</a>
                  </div>

                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>

    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
