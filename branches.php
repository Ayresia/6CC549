<?php

require_once(__DIR__ . '/includes/auth.php');

$branches   = branches_public();
$headOffice = branch_head_office();

$pageTitle       = 'Our Branches';
$pageDescription = 'Book & Board\'s four UK branches in Manchester, Birmingham, Glasgow and Bristol, with addresses, telephone numbers, email and opening hours, plus the London head office.';
$navHere         = 'branches';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Our Branches</h1>
        <p class="lead mb-0">
          We have four branches across the UK and they are open six days a week. Sit down with an
          agent, talk your trip through and leave with it booked. Once you are away, the branch that
          made your booking is on the phone for as long as you are travelling.
        </p>
      </div>
    </div>
  </section>

  <?php if ($headOffice): ?>
  <section class="pt-5">
    <div class="container">
      <div class="row align-items-center g-4">

        <div class="col-lg-6">
          <img src="assets/img/branches-london.jpg" class="branch-img w-100 object-fit-cover rounded shadow-sm"
               alt="An aerial view of the River Thames and Tower Bridge in London at dusk." loading="lazy">
        </div>

        <div class="col-lg-6">
          <h2>Head office, <?= e($headOffice['name']) ?></h2>
          <p class="text-body-secondary"><?= e($headOffice['location']) ?></p>
          <p>
            Our head office does not take bookings over the counter. It is where the offers you see on
            this site are negotiated, and where to write about anything a branch cannot settle.
          </p>

          <dl class="detail-list mb-4">
            <dt class="detail-label">Address</dt>
            <dd>
              <address class="mb-0">
                <?= e($headOffice['street']) ?><br>
                <?= e($headOffice['area']) ?>
              </address>
            </dd>
            <dt class="detail-label">Phone</dt>
            <dd><a href="tel:<?= e($headOffice['tel']) ?>"><?= e($headOffice['phone']) ?></a></dd>
            <dt class="detail-label">Email</dt>
            <dd><a href="mailto:<?= e($headOffice['email']) ?>"><?= e($headOffice['email']) ?></a></dd>
            <dt class="detail-label">Open</dt>
            <dd><?= e($headOffice['hours']) ?></dd>
          </dl>

          <a class="btn btn-primary" href="contact.php"
             aria-label="Contact head office: <?= e($headOffice['name']) ?>">Contact head office</a>
        </div>

      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="py-5">
    <div class="container">
      <?php if (!$branches): ?>

        <p class="mb-0">We cannot show the branch details right now. Please try again in a minute.</p>

      <?php else: ?>

      <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($branches as $branch): ?>
          <div class="col">
            <div class="card h-100">
              <div class="card-body d-flex flex-column p-4">

                <h2 class="h4 mb-1"><?= e($branch['name']) ?></h2>

                <?php if ($branch['location']): ?>
                  <p class="text-body-secondary mb-3"><?= e($branch['location']) ?></p>
                <?php endif; ?>

                <dl class="detail-list mb-4">
                  <?php if ($branch['street'] || $branch['area']): ?>
                    <dt class="detail-label">Address</dt>
                    <dd>
                      <address class="mb-0">
                        <?= e($branch['street']) ?><br>
                        <?= e($branch['area']) ?>
                      </address>
                    </dd>
                  <?php endif; ?>

                  <?php if ($branch['phone']): ?>
                    <dt class="detail-label">Phone</dt>
                    <dd><a href="tel:<?= e($branch['tel']) ?>"><?= e($branch['phone']) ?></a></dd>
                  <?php endif; ?>

                  <?php if ($branch['email']): ?>
                    <dt class="detail-label">Email</dt>
                    <dd><a href="mailto:<?= e($branch['email']) ?>"><?= e($branch['email']) ?></a></dd>
                  <?php endif; ?>

                  <?php if ($branch['hours']): ?>
                    <dt class="detail-label">Open</dt>
                    <dd><?= e($branch['hours']) ?></dd>
                  <?php endif; ?>
                </dl>

                <a class="btn btn-primary mt-auto align-self-start" href="contact.php"
                   aria-label="Contact this branch: <?= e($branch['name']) ?>">Contact this branch</a>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php endif; ?>
    </div>
  </section>


<?php require_once(__DIR__ . '/includes/footer.php'); ?>
