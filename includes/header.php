<?php

$navHere = $navHere ?? '';
$user    = current_user();

$navItems = [
    'home'     => ['label' => 'Home',     'href' => 'index.php'],
    'offers'   => ['label' => 'Offers',   'href' => 'offers.php'],
    'flights'  => ['label' => 'Flights',  'href' => 'flights.php'],
    'hotels'   => ['label' => 'Hotels',   'href' => 'hotels.php'],
    'services' => ['label' => 'Services', 'href' => 'services.php'],
    'branches' => ['label' => 'Branches', 'href' => 'branches.php'],
    'contact'  => ['label' => 'Contact',  'href' => 'contact.php'],
];
?>
<!doctype html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book &amp; Board<?= $pageTitle === '' ? '' : ' - ' . e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="d-flex flex-column min-vh-100">

<a href="#main" class="visually-hidden-focusable d-inline-block p-3 bg-dark text-white">Skip to content</a>

<header class="sticky-top bg-body border-bottom">
  <nav class="navbar navbar-expand-xl" aria-label="Primary">
    <div class="container">

      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
        <img src="assets/img/logo.svg" alt="" width="34" height="27">
        <span class="fw-semibold">Book &amp; Board</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
              aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-xl-auto mb-2 mb-xl-0">
          <?php foreach ($navItems as $key => $item): ?>
            <li class="nav-item">
              <a class="nav-link<?= $navHere === $key ? ' active' : '' ?>"
                 <?= $navHere === $key ? 'aria-current="page"' : '' ?>
                 href="<?= e($item['href']) ?>"><?= e($item['label']) ?></a>
            </li>
          <?php endforeach; ?>
        </ul>

        <hr class="d-xl-none my-2">

        <div class="d-grid gap-2 d-xl-flex">
          <?php if ($user): ?>
            <span class="text-body-secondary small me-xl-2 align-self-xl-center"><i class="bi bi-person-circle me-1" aria-hidden="true"></i><span class="visually-hidden">Signed in as </span><?= e($user['name']) ?></span>
            <?php if ($user['role'] === 'staff'): ?>
              <a class="btn btn-primary" href="staff/offers.php">Manage offers</a>
            <?php elseif ($navHere !== 'account'): ?>
              <a class="btn btn-primary" href="account.php">My account</a>
            <?php endif; ?>
            <a class="btn btn-outline-secondary" href="login.php?signout=1">Sign out</a>
          <?php else: ?>
            <a class="btn btn-outline-secondary<?= $navHere === 'login' ? ' active' : '' ?>"
               <?= $navHere === 'login' ? 'aria-current="page"' : '' ?> href="login.php">Sign in</a>
            <a class="btn btn-primary<?= $navHere === 'register' ? ' active' : '' ?>"
               <?= $navHere === 'register' ? 'aria-current="page"' : '' ?> href="register.php">Register</a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </nav>
</header>

<main id="main" tabindex="-1" class="flex-grow-1 flex-shrink-0">
