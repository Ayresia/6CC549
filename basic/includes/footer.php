<?php

?>
</main>

<footer class="bg-dark text-light py-5" data-bs-theme="dark">
  <div class="container">
    <div class="row g-4">

      <div class="col-lg-4">
        <p class="d-flex align-items-center gap-2 fs-5 fw-semibold mb-2">
          <img src="assets/img/logo.svg" alt="" width="30" height="24">
          Book &amp; Board
        </p>
        <p class="text-body-secondary mb-0">A UK travel agency booking flights, hotels and complete holiday packages from four high street branches and a head office in London, since 1975.</p>
      </div>

      <div class="col-6 col-lg-4">
        <h2 class="h6 text-uppercase mb-3">Explore</h2>
        <ul class="list-unstyled mb-0">
          <?php

                $lastNav = array_key_last($navItems); ?>
          <?php foreach ($navItems as $key => $item): ?>
            <li<?= $key === $lastNav ? '' : ' class="mb-2"' ?>><a class="link-light" href="<?= e($item['href']) ?>"><?= e($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="col-6 col-lg-4">
        <h2 class="h6 text-uppercase mb-3">Staff</h2>
        <ul class="list-unstyled mb-0">
          <?php if ($user): ?>
            <li class="mb-2"><a class="link-light" href="staff/offers.php">Manage offers</a></li>
            <li><a class="link-light" href="login.php?signout=1">Sign out</a></li>
          <?php else: ?>
            <li><a class="link-light" href="login.php">Staff sign in</a></li>
          <?php endif; ?>
        </ul>
      </div>

    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
