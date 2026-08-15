<?php

require_once(__DIR__ . '/includes/auth.php');

$user   = current_user();
$offers = offers_active();

$pageTitle       = 'Current Offers';
$pageDescription = 'Every current Book & Board offer: travel plans, travel and hotel packages and complete holiday packages, with prices, dates and what is included.';
$navHere         = 'offers';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Current Offers</h1>
        <p class="lead mb-0">
          Everything we are holding this month, across all three of our services. Prices are per person,
          based on two people sharing. Bring your membership card into a branch and we will take your
          discount off the price.
        </p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <?php if (!$offers): ?>

        <p class="mb-0">We have no offers on at the moment. Please check back soon.</p>

      <?php else: ?>

      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($offers as $offer): ?>
          <div class="col">
            <article class="card h-100">
              <div class="ratio ratio-4x3 card-img-top overflow-hidden">
                <img src="<?= e($offer['image']) ?>" class="object-fit-cover"
                     alt="<?= e($offer['alt']) ?>" loading="lazy">
              </div>
              <div class="card-body d-flex flex-column">
                <p class="offer-type mb-2"><?= e($offer['type']) ?></p>
                <h2 class="card-title h5 mb-1"><?= e($offer['title']) ?></h2>
                <p class="text-body-secondary mb-2"><?= e($offer['destination']) ?></p>
                <p class="card-text"><?= e($offer['description']) ?></p>

                <div class="fare mt-auto">
                  <p class="fare-line mb-1">
                    <span class="fare-label">Departs</span>
                    <span class="fare-value"><?= e(date_range($offer['startDate'], $offer['endDate'])) ?></span>
                  </p>
                  <p class="fare-line mb-3">
                    <span class="fare-label">From</span>
                    <span class="fare-price">
                      <span class="fare-amount"><?= e(money($offer['price'])) ?></span>
                      <span class="fare-unit">per person</span>
                    </span>
                  </p>

                  <a class="btn btn-primary w-100" href="contact.php"
                     aria-label="Enquire about this offer: <?= e($offer['title']) ?>, <?= e($offer['destination']) ?>">Enquire about this offer</a>
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
