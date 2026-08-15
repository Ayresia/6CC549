<?php

require_once(__DIR__ . '/includes/auth.php');

function clock(string $time): string
{
    $value = date_create($time);

    return $value ? $value->format('H:i') : '';
}

function flight_stops(int $stops): string
{
    if ($stops === 0) {
        return 'Direct';
    }

    return $stops . ' stop' . ($stops === 1 ? '' : 's');
}

function flight_duration(int $minutes): string
{
    $hours = intdiv($minutes, 60);
    $rest  = $minutes % 60;

    if ($hours === 0) {
        return $rest . 'm';
    }

    return $rest === 0 ? $hours . 'h' : $hours . 'h ' . $rest . 'm';
}

$today = date('Y-m-d');

$destinations = flights_destinations();
$origins      = flights_origins();

const DURATION_CHOICES = [
    180  => 'Up to 3 hours',
    360  => 'Up to 6 hours',
    540  => 'Up to 9 hours',
    720  => 'Up to 12 hours',
    1080 => 'Up to 18 hours',
];

const STOP_CHOICES = [
    0 => 'Direct',
    1 => '1 stop',
    2 => '2 or more stops',
];

$values = [
    'destination' => trim((string) ($_GET['destination'] ?? '')),
    'origin'      => trim((string) ($_GET['origin'] ?? '')),
    'dateFrom'    => trim((string) ($_GET['dateFrom'] ?? '')),
    'dateTo'      => trim((string) ($_GET['dateTo'] ?? '')),
    'maxPrice'    => trim((string) ($_GET['maxPrice'] ?? '')),
    'maxDuration' => trim((string) ($_GET['maxDuration'] ?? '')),

    'stops'       => array_map('intval', (array) ($_GET['stops'] ?? [])),
];

$errors  = [];
$results = [];

if ($values['destination'] !== '' && !in_array($values['destination'], $destinations, true)) {
    $errors['destination'] = 'Select a destination from the list. We do not fly to that one.';
}

if ($values['origin'] !== '' && !in_array($values['origin'], $origins, true)) {
    $errors['origin'] = 'Select a departure airport from the list, or leave it as any airport.';
}

if ($values['dateFrom'] !== '' && !is_date($values['dateFrom'])) {
    $errors['dateFrom'] = 'Enter the departure date as a real date, for example 12/10/2026.';
}

if ($values['dateTo'] !== '') {
    if (!is_date($values['dateTo'])) {
        $errors['dateTo'] = 'Enter the latest departure date as a real date, for example 16/10/2026.';
    } elseif (!isset($errors['dateFrom']) && $values['dateFrom'] !== '' && $values['dateTo'] < $values['dateFrom']) {
        $errors['dateTo'] = 'The latest departure date must be on or after the departure date.';
    }
}

if ($values['maxPrice'] !== '' && (!is_numeric($values['maxPrice']) || (float) $values['maxPrice'] < 0)) {
    $errors['maxPrice'] = 'Enter a maximum price as a number, without the pound sign.';
}

if (!isset(DURATION_CHOICES[(int) $values['maxDuration']])) {
    $values['maxDuration'] = '';
}

$values['stops'] = array_values(array_filter(
    $values['stops'],
    static fn(int $stop): bool => isset(STOP_CHOICES[$stop])
));

if (!$errors) {
    $results = flights_search([
        'destination' => $values['destination'],
        'origin'      => $values['origin'],
        'dateFrom'    => $values['dateFrom'],
        'dateTo'      => $values['dateTo'],
        'maxPrice'    => $values['maxPrice'] === '' ? 0 : (float) $values['maxPrice'],
        'maxDuration' => $values['maxDuration'] === '' ? 0 : (int) $values['maxDuration'],
        'stops'       => $values['stops'],
    ]);
}

$filter = $values['destination'] !== ''
    || $values['origin'] !== ''
    || $values['dateFrom'] !== ''
    || $values['dateTo'] !== ''
    || $values['maxPrice'] !== ''
    || $values['maxDuration'] !== ''
    || $values['stops'] !== [];

$pageTitle       = 'Flight Search';
$pageDescription = 'Search Book & Board flights by destination and date, and filter the results by price, travel time and number of stops.';
$navHere         = 'flights';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Flight Search</h1>
        <p class="lead mb-0">
          Every fare we hold with the airlines. Search by where you are going and when. Found
          something? Call the branch and an agent will hold the seats for you. We do not sell online.
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

      <form method="get" action="flights.php" class="card mb-5" novalidate>
        <div class="card-body p-4">

          <fieldset class="mb-0">
            <legend class="h5 mb-3">Find a flight</legend>

            <div class="row g-3">

              <div class="col-md-6 col-lg-3">
                <label for="origin" class="form-label">Leaving from</label>
                <select class="form-select<?= invalid($errors, 'origin') ?>"
                        id="origin" name="origin"
                        <?= isset($errors['origin']) ? 'aria-invalid="true" aria-describedby="origin-error"' : '' ?>>
                  <option value="">Any UK airport</option>
                  <?php foreach ($origins as $origin): ?>
                    <option value="<?= e($origin) ?>" <?= $values['origin'] === $origin ? 'selected' : '' ?>><?= e($origin) ?></option>
                  <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'origin') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="destination" class="form-label">Going to</label>
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
                <label for="dateFrom" class="form-label">Departure date</label>
                <input type="date" class="form-control<?= invalid($errors, 'dateFrom') ?>"
                       id="dateFrom" name="dateFrom" value="<?= e($values['dateFrom']) ?>"
                       min="<?= e($today) ?>"
                       <?= isset($errors['dateFrom']) ? 'aria-describedby="dateFrom-error"' : '' ?>
                       <?= isset($errors['dateFrom']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'dateFrom') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="dateTo" class="form-label">Latest departure</label>
                <input type="date" class="form-control<?= invalid($errors, 'dateTo') ?>"
                       id="dateTo" name="dateTo" value="<?= e($values['dateTo']) ?>"
                       min="<?= e($today) ?>"
                       <?= isset($errors['dateTo']) ? 'aria-describedby="dateTo-error"' : '' ?>
                       <?= isset($errors['dateTo']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'dateTo') ?>
              </div>

              <div class="col-md-6 col-lg-3">
                <label for="maxPrice" class="form-label">Maximum price</label>
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

              <div class="col-md-6 col-lg-3">
                <label for="maxDuration" class="form-label">Travel time</label>
                <select class="form-select" id="maxDuration" name="maxDuration">
                  <option value="">Any length</option>
                  <?php foreach (DURATION_CHOICES as $minutes => $label): ?>
                    <option value="<?= $minutes ?>" <?= (string) $minutes === $values['maxDuration'] ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-lg-6">
                <fieldset class="mb-0">
                  <legend class="form-label fs-6 mb-2">Stops</legend>
                  <?php foreach (STOP_CHOICES as $count => $label): ?>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" value="<?= $count ?>"
                             id="stops-<?= $count ?>" name="stops[]"
                             <?= in_array($count, $values['stops'], true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="stops-<?= $count ?>"><?= e($label) ?></label>
                    </div>
                  <?php endforeach; ?>
                </fieldset>
              </div>

            </div>
          </fieldset>

          <div class="d-grid d-sm-block mt-4">
            <button type="submit" class="btn btn-primary">Search flights</button>
          </div>

        </div>
      </form>

      <?php if (!$results): ?>

        <h2 class="h5">No flights found</h2>
        <p class="mb-0">
          <?= $filter
              ? 'Nothing we hold matches all of that. Try changing one of your filters above.'
              : 'We have no flights loaded at the moment. Please check back soon.' ?>
        </p>

      <?php else: ?>

        <h2 class="visually-hidden">
          <?= count($results) ?> flight<?= count($results) === 1 ? '' : 's' ?><?php
          ?><?= $values['destination'] !== '' ? ' to ' . e($values['destination']) : '' ?>
        </h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
          <?php foreach ($results as $flight): ?>
            <?php $departs = date_long($flight['departureDate']); ?>
            <div class="col">
              <article class="card h-100">
                <div class="card-body d-flex flex-column">

                  <p class="offer-type mb-2"><?= e($flight['airline']) ?></p>

                  <h3 class="card-title h5 mb-1">
                    <?= e($flight['origin']) ?>
                    <span aria-hidden="true">→</span><span class="visually-hidden">to</span>
                    <?= e($flight['destination']) ?>
                  </h3>
                  <p class="text-body-secondary mb-3"><?= e(flight_stops($flight['stops'])) ?></p>

                  <dl class="detail-list mb-3">
                    <dt class="detail-label">Departs</dt>
                    <dd><?= e(clock($flight['departureTime'])) ?></dd>

                    <dt class="detail-label">Arrives</dt>
                    <dd><?= e(clock($flight['arrivalTime'])) ?></dd>

                    <dt class="detail-label">Duration</dt>
                    <dd><?= e(flight_duration($flight['durationMinutes'])) ?></dd>
                  </dl>

                  <div class="fare mt-auto">
                    <?php
                          ?>
                    <p class="fare-line mb-1">
                      <span class="fare-label">Date</span>
                      <span class="fare-value"><?= e($departs) ?></span>
                    </p>
                    <p class="fare-line mb-3">
                      <span class="fare-label">From</span>
                      <span class="fare-price">
                        <span class="fare-amount"><?= e(money($flight['price'])) ?></span>
                        <span class="fare-unit">one way</span>
                      </span>
                    </p>

                    <a class="btn btn-primary w-100" href="contact.php"
                       aria-label="Enquire about this flight: <?= e($flight['airline']) ?>, <?= e($flight['origin']) ?> to <?= e($flight['destination']) ?>, <?= e($departs) ?>">Enquire about this flight</a>
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
