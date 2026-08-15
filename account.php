<?php

require_once(__DIR__ . '/includes/auth.php');

$user = require_customer();

$details = user_find((int) $user['id']) ?: $user + ['created_at' => ''];

$packages = packages_for_customer((int) $user['id']);

$justIn      = isset($_GET['welcome']);
$staffDenied = isset($_GET['staff']);
$justUpdated = isset($_GET['updated']);

function package_status_class(string $status): string
{
    switch (strtolower($status)) {
        case 'upcoming':
            return 'text-bg-success';
        case 'cancelled':
            return 'text-bg-danger';
        default:
            return 'bg-body-tertiary text-body border';
    }
}

$pageTitle       = 'My account';
$pageDescription = 'Your Book & Board account: the contact details we hold for you and the packages you have booked with us.';
$navHere         = 'account';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-9 col-xl-8">
        <h1>My account</h1>
        <p class="lead mb-0">Signed in as <?= e($details['email']) ?>.</p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="col-lg-9 col-xl-8">

        <?php if ($justIn): ?>
          <div class="alert alert-success d-flex gap-2" role="alert" tabindex="-1">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>Welcome, <?= e($details['name']) ?>. Your account is ready and you are signed in.</span>
          </div>
        <?php endif; ?>

        <?php if ($justUpdated): ?>
          <div class="alert alert-success d-flex gap-2" role="alert" tabindex="-1">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>Your details have been updated.</span>
          </div>
        <?php endif; ?>

        <?php if ($staffDenied): ?>
          <div class="alert alert-warning d-flex gap-2" role="alert" tabindex="-1">
            <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
            <span>
              That page is for Book &amp; Board staff, who manage what the website shows.
              Your account does not have access to it.
            </span>
          </div>
        <?php endif; ?>

        <div class="card mb-4">
          <div class="card-body p-4">
            <h2 class="h5 mb-1">Your details</h2>
            <p class="text-body-secondary mb-4">
              This is everything we store about you. Correct it yourself below, or
              <a href="contact.php">tell your branch</a> and an agent will do it for you.
            </p>

            <dl class="detail-list mb-0">
              <dt class="detail-label">Name</dt>
              <dd><?= e($details['name']) ?></dd>

              <dt class="detail-label">Email</dt>
              <dd><a href="mailto:<?= e($details['email']) ?>"><?= e($details['email']) ?></a></dd>

              <?php ?>
              <?php if ($details['phone'] !== ''): ?>
                <dt class="detail-label">Phone</dt>
                <dd><?= e($details['phone']) ?></dd>
              <?php endif; ?>

              <?php if ($details['created_at'] !== ''): ?>
                <?php
                      ?>
                <dt class="detail-label">Joined</dt>
                <dd><?= e(date_long($details['created_at'])) ?></dd>
              <?php endif; ?>
            </dl>

            <a class="btn btn-primary mt-4" href="account-details.php">Update your details</a>
          </div>
        </div>

        <h2 class="h5 mb-1">Previous packages</h2>
        <p class="text-body-secondary mb-3">
          Everything you have booked with us, no matter which branch arranged it.
        </p>

        <?php if (!$packages): ?>

          <div class="card mb-4">
            <div class="card-body p-4">
              <p class="mb-3">You have not booked anything with us yet.</p>
              <div class="d-grid gap-2 d-sm-flex">
                <a class="btn btn-primary" href="offers.php">Browse current offers</a>
                <a class="btn btn-outline-secondary" href="flights.php">Search flights</a>
              </div>
            </div>
          </div>

        <?php else: ?>

          <?php

                ?>
          <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
            <?php foreach ($packages as $package): ?>
              <div class="col">
                <article class="card h-100">
                  <div class="card-body d-flex flex-column">

                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                      <p class="offer-type mb-0"><?= e($package['packageType']) ?></p>
                      <span class="badge rounded-pill flex-shrink-0 <?= e(package_status_class($package['status'])) ?>">
                        <?= e($package['status']) ?>
                      </span>
                    </div>

                    <h3 class="card-title h6 mb-1"><?= e($package['title']) ?></h3>
                    <p class="text-body-secondary mb-0"><?= e($package['destination']) ?></p>

                    <div class="fare mt-auto">
                      <p class="fare-line mb-0">
                        <span class="fare-label"><?= strtolower($package['status']) === 'upcoming' ? 'Travelling' : 'Travelled' ?></span>
                        <span class="fare-value"><?= e(date_range($package['startDate'], $package['endDate'])) ?></span>
                      </p>
                    </div>

                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

        <div class="d-grid gap-2 d-sm-flex">
          <a class="btn btn-primary" href="offers.php">Browse current offers</a>
          <a class="btn btn-outline-secondary" href="login.php?signout=1">Sign out</a>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
