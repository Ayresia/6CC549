<?php

require_once(__DIR__ . '/includes/auth.php');

$offers = offers_bestselling(3);

$headOffice = branch_head_office();

$services = [
    [
        'icon'        => 'bi-map',
        'title'       => 'Travel Plans',
        'description' => 'We book your flights, trains and transfers around the dates you want. We hold the seats, keep an eye on the prices and deal with any changes for you.',
    ],
    [
        'icon'        => 'bi-building',
        'title'       => 'Travel and Hotel Packages',
        'description' => 'Your travel and a hotel we have stayed in ourselves, booked together so that one agent looks after the whole trip.',
    ],
    [
        'icon'        => 'bi-suitcase',
        'title'       => 'Complete Holiday Packages',
        'description' => 'Flights, hotel, car hire, transfers and travel insurance all in one price, protected from the day you pay your deposit.',
    ],
];

$pageTitle       = '';
$pageDescription = 'Book & Board is a UK travel agency arranging flights, hotels and complete holiday packages since 1975, from four high street branches and a head office in London.';
$navHere         = 'home';

require_once(__DIR__ . '/includes/header.php');
?>

  <section id="home" class="hero position-relative d-flex">
    <img src="assets/img/hero-paris.jpg" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
         alt="The Pont Alexandre III in Paris at dusk, its lamps lit along the Seine.">

    <div class="hero-overlay position-relative flex-grow-1 d-flex align-items-center">
      <div class="container text-center text-white">
        <div class="col-lg-8 col-xl-7 mx-auto py-4">
          <h1 class="display-4 fw-bold">Holidays booked by real people</h1>
          <p class="lead mb-4">
            We have been booking flights, hotels and complete holiday packages across the UK since 1975.
            Tell us where you want to go and one of our agents will sort out the rest.
          </p>
          <a class="btn btn-primary btn-lg" href="offers.php">View offers</a>
        </div>
      </div>
    </div>
  </section>

  <section id="search" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="row g-4 align-items-center">

        <div class="col-lg-7">
          <h2>Search flights and hotels yourself</h2>
          <p class="mb-4">
            You can search every flight and room we hold, then filter them by price, travel time and
            stops. Found something you like? Call your branch and an agent will put the trip
            together for you. We do not sell online.
          </p>
          <div class="d-grid gap-2 d-sm-flex">
            <a class="btn btn-primary" href="flights.php">
              <i class="bi bi-airplane me-1" aria-hidden="true"></i>Search flights
            </a>
            <a class="btn btn-outline-secondary" href="hotels.php">
              <i class="bi bi-building me-1" aria-hidden="true"></i>Search hotels
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="offers" class="py-5">
    <div class="container">
      <div class="row g-3 align-items-center mb-4">
        <div class="col-lg-8">
          <h2 class="mb-0">Bestselling Offers</h2>
        </div>
        <div class="col-lg-8 order-lg-2">
          <p class="text-body-secondary mb-0">
            Our three most popular offers this month. Prices are per person, based on two people sharing.
            Bring your membership card into a branch and we will take your discount off the price.
          </p>
        </div>
        <div class="col-lg-4 order-lg-1 text-lg-end">
          <a class="btn btn-primary" href="offers.php">See all current offers</a>
        </div>
      </div>

      <?php if (!$offers): ?>

        <p class="mb-0">
          Nothing is featured right now.
          <a href="offers.php">See everything we are holding</a>.
        </p>

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
                <h3 class="card-title h5 mb-1"><?= e($offer['title']) ?></h3>
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

  <section id="services" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="row g-3 align-items-center mb-4">
        <div class="col-lg-8">
          <h2 class="mb-0">Our Services</h2>
        </div>
        <div class="col-lg-8 order-lg-2">
          <p class="text-body-secondary mb-0">We can do as much or as little of the planning as you want.</p>
        </div>
        <div class="col-lg-4 order-lg-1 text-lg-end">
          <a class="btn btn-primary" href="services.php">More about our services</a>
        </div>
      </div>

      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($services as $service): ?>
          <div class="col">
            <div class="card h-100">
              <div class="card-body">
                <i class="bi <?= $service['icon'] ?> fs-2 text-primary d-block mb-2" aria-hidden="true"></i>
                <h3 class="card-title h5"><?= $service['title'] ?></h3>
                <p class="card-text"><?= $service['description'] ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="branches" class="py-5">
    <div class="container">
      <div class="row align-items-center g-4">

        <div class="col-lg-6">
          <img src="assets/img/branches-london.jpg" class="branch-img w-100 object-fit-cover rounded shadow-sm"
               alt="An aerial view of the River Thames and Tower Bridge in London at dusk." loading="lazy">
        </div>

        <div class="col-lg-6">
          <h2>Support from your local branch</h2>
          <p>We have branches in Manchester, Birmingham, Glasgow and Bristol, plus our head office in London, and they are open six days a week. Sit down with an agent, talk your trip through and leave with it booked.</p>
          <p class="mb-4">Already away? The branch that made your booking is on the phone seven days a week for as long as you are travelling.</p>
          <div class="d-grid d-sm-flex gap-2">
            <a class="btn btn-primary" href="branches.php">All branch details</a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="contact" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="row g-4 align-items-center">

        <div class="col-lg-7">
          <h2>Contact Us</h2>
          <p class="mb-4">Call or email us and an agent will get back to you within one working day.</p>
          <?php if ($headOffice): ?>
            <dl class="detail-list mb-4">
              <dt class="detail-label">Phone</dt>
              <dd><a href="tel:<?= e($headOffice['tel']) ?>"><?= e($headOffice['phone']) ?></a></dd>
              <dt class="detail-label">Email</dt>
              <dd><a href="mailto:<?= e($headOffice['email']) ?>"><?= e($headOffice['email']) ?></a></dd>
              <dt class="detail-label">Open</dt>
              <dd><?= e($headOffice['hours']) ?></dd>
            </dl>
          <?php endif; ?>
          <div class="d-grid d-sm-flex gap-2">
            <a class="btn btn-primary" href="contact.php">All contact details</a>
          </div>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
