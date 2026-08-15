<?php

?>
<!doctype html>
<html lang="en-GB">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Book &amp; Board Staff<?= $pageTitle === '' ? '' : ' - ' . e($pageTitle) ?></title>
  <link rel="icon" href="../assets/img/logo.svg" type="image/svg+xml">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/theme.css">
</head>
<body class="d-flex flex-column min-vh-100">

<a href="#main" class="visually-hidden-focusable d-inline-block p-3 bg-dark text-white">Skip to content</a>

<header class="sticky-top bg-body border-bottom">
  <nav class="navbar navbar-expand-lg" aria-label="Staff">
    <div class="container">

      <a class="navbar-brand d-flex align-items-center gap-2" href="offers.php">
        <img src="../assets/img/logo.svg" alt="" width="34" height="27">
        <span class="fw-semibold">Book &amp; Board</span>
        <span class="badge rounded-pill bg-body-tertiary text-body border fw-normal">Staff</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#staffNav"
              aria-controls="staffNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="staffNav">
        <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link<?= $navHere === 'offers' ? ' active' : '' ?>"
               <?= $navHere === 'offers' ? 'aria-current="page"' : '' ?> href="offers.php">Manage Offers</a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= $navHere === 'branches' ? ' active' : '' ?>"
               <?= $navHere === 'branches' ? 'aria-current="page"' : '' ?> href="branches.php">Manage Branches</a>
          </li>
        </ul>

        <hr class="d-lg-none my-2">

        <div class="d-grid gap-2 d-lg-flex">
          <span class="text-body-secondary small me-lg-2 align-self-lg-center"><i class="bi bi-person-circle me-1" aria-hidden="true"></i><span class="visually-hidden">Signed in as </span><?= e($staff['name']) ?></span>
          <a class="btn btn-primary" href="../index.php">View website</a>
          <a class="btn btn-outline-secondary" href="../login.php?signout=1">Sign out</a>
        </div>
      </div>

    </div>
  </nav>
</header>

<main id="main" tabindex="-1" class="flex-grow-1 flex-shrink-0 py-5">
  <div class="container">

    <?php $flash = flash_take(); ?>
    <?php if ($flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?> d-flex gap-2" role="alert" tabindex="-1">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"
           aria-hidden="true"></i>
        <span><?= e($flash['message']) ?></span>
      </div>
    <?php endif; ?>
