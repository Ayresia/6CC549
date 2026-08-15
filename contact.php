<?php

require_once(__DIR__ . '/includes/auth.php');

$branches = branches_public();
$general  = branch_head_office();

$pageTitle       = 'Contact Us';
$pageDescription = 'Contact Book & Board by phone or email, or go straight to your local branch in Manchester, Birmingham, Glasgow or Bristol.';
$navHere         = 'contact';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-8">
        <h1>Contact Us</h1>
        <p class="lead mb-0">
          Call us or email us and an agent will get back to you within one working day. If you booked
          in a branch, that branch is usually the quickest way to get through.
        </p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">

      <div class="row gx-4 gy-5">

        <div class="col-lg-6">
          <h2 class="h4">Book &amp; Board</h2>
          <?php if (!$general): ?>
            <p class="mb-0">We cannot show our contact details right now. Please try again in a minute.</p>
          <?php else: ?>
          <p class="text-body-secondary">One number and one address for anything else.</p>
          <dl class="detail-list mb-4">
            <dt class="detail-label">Phone</dt>
            <dd><a href="tel:<?= e($general['tel']) ?>"><?= e($general['phone']) ?></a></dd>
            <dt class="detail-label">Email</dt>
            <dd><a href="mailto:<?= e($general['email']) ?>"><?= e($general['email']) ?></a></dd>
            <dt class="detail-label">Open</dt>
            <dd><?= e($general['hours']) ?></dd>
            <dt class="detail-label">Head office</dt>
            <dd>
              <address class="mb-0">
                <?= e($general['street']) ?><br>
                <?= e($general['area']) ?>
              </address>
            </dd>
          </dl>

          <div class="d-grid d-sm-flex gap-2">
            <a class="btn btn-primary" href="mailto:<?= e($general['email']) ?>">Email us</a>
            <a class="btn btn-outline-secondary" href="tel:<?= e($general['tel']) ?>">Call <?= e($general['phone']) ?></a>
          </div>
          <?php endif; ?>
        </div>

        <div class="col-lg-6">
          <h2 class="h4">Your local branch</h2>
          <p class="text-body-secondary">Open six days a week, straight through to the counter.</p>
          <?php if (!$branches): ?>
            <p class="mb-0">We cannot show the branch details right now.</p>
          <?php else: ?>
          <ul class="list-unstyled mb-4">
            <?php foreach ($branches as $branch): ?>
              <li class="mb-3">
                <span class="fw-semibold d-block"><?= e($branch['name']) ?></span>
                <a href="tel:<?= e($branch['tel']) ?>"><?= e($branch['phone']) ?></a>
                <span class="vr mx-1" aria-hidden="true"></span>
                <a href="mailto:<?= e($branch['email']) ?>">Email<span class="visually-hidden"> the <?= e($branch['name']) ?> branch</span></a>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <a class="btn btn-outline-secondary" href="branches.php">Branch addresses and opening hours</a>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
