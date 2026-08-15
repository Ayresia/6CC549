<?php

require_once(__DIR__ . '/includes/auth.php');

$user = current_user();

$services = [
    [
        'icon'        => 'bi-map',
        'title'       => 'Travel Plans',
        'description' => 'We sort out the journey itself. Tell us your dates and where you need to be, and an agent puts the travel together: flights, trains, ferries and the transfers in between, on fares we have agreed directly with the airlines.',
        'covers'      => [
            'Flights, rail and ferry crossings booked around your dates',
            'Airport and station transfers',
            'Seats held while you decide, and changes handled by your agent',
        ],
    ],
    [
        'icon'        => 'bi-building',
        'title'       => 'Travel and Hotel Packages',
        'description' => 'Your travel and somewhere to stay, booked together on one itinerary at one price. We agree room rates with hotels in the places we know best, and one agent looks after the whole trip instead of passing you around.',
        'covers'      => [
            'Return travel and hotel on a single booking',
            'Hotels our agents have stayed in themselves',
            'Board arrangements to suit, from room only to half board',
        ],
    ],
    [
        'icon'        => 'bi-suitcase',
        'title'       => 'Complete Holiday Packages',
        'description' => 'Everything in one price, so there is nothing left to sort out once you land. Flights, hotel, car hire and travel insurance are booked together, with transfers and any trips added to the same itinerary.',
        'covers'      => [
            'Flights and hotel',
            'Car hire and airport transfers',
            'Travel insurance for everyone travelling',
        ],
    ],
];

$pageTitle       = 'Our Services';
$pageDescription = 'The three services Book & Board offers: travel plans, travel and hotel packages, and complete holiday packages covering flights, hotels, car hire and insurance.';
$navHere         = 'services';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Our Services</h1>
        <p class="lead mb-0">
          There are three ways to book with us. We can do as much or as little of the planning as you
          want, and either way an agent in your branch arranges it and stays with your booking until
          you get home.
        </p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($services as $service): ?>
          <div class="col">
            <div class="card h-100">
              <div class="card-body d-flex flex-column p-4">

                <span class="service-icon d-inline-flex align-items-center justify-content-center
                             rounded-circle bg-body-tertiary text-primary mb-3">
                  <i class="bi <?= $service['icon'] ?> fs-2" aria-hidden="true"></i>
                </span>

                <h2 class="h4"><?= $service['title'] ?></h2>
                <p class="card-text"><?= $service['description'] ?></p>

                <ul class="list-unstyled mb-4">
                  <?php foreach ($service['covers'] as $item): ?>
                    <li class="d-flex gap-2 mb-2">
                      <i class="bi bi-check2 text-primary" aria-hidden="true"></i>
                      <span><?= $item ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>

                <a class="btn btn-primary mt-auto" href="contact.php"
                   aria-label="Contact us about <?= $service['title'] ?>">Contact us</a>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
