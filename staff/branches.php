<?php

require_once(__DIR__ . '/../includes/auth.php');

$staff = require_staff();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    $id     = (int) ($_POST['id'] ?? 0);
    $branch = branches_find($id);

    if (!$branch) {
        flash_set('danger', 'That branch is not there any more.');
    } elseif ((string) ($_POST['action'] ?? '') === 'delete') {
        branches_delete($id);
        flash_set('success', '“' . $branch['name'] . '” has been deleted.');
    }

    header('Location: branches.php');
    exit;
}

$branches  = branches_all();
$confirmId = (int) ($_GET['confirm'] ?? 0);
$confirm   = $confirmId ? branches_find($confirmId) : null;

$offices  = count(array_filter($branches, fn($b) => $b['isHeadOffice']));
$counters = count($branches) - $offices;

$pageTitle = 'Manage Branches';
$navHere   = 'branches';
require_once(__DIR__ . '/../includes/staff-header.php');
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div>
    <h1 class="h2 mb-1">Manage Branches</h1>
    <p class="text-body-secondary mb-0">
      <?= $counters ?> branch<?= $counters === 1 ? '' : 'es' ?><?php
        if ($offices): ?> and <?= $offices ?> head office<?= $offices === 1 ? '' : 's' ?><?php endif; ?>.
      Anything you change here shows up on the website straight away.
    </p>
  </div>
  <a class="btn btn-primary" href="branch-form.php">Add new branch</a>
</div>

<?php if ($offices !== 1): ?>
  <div class="alert alert-warning d-flex gap-2" role="alert">
    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
    <span>
      <?php if ($offices === 0): ?>
        No branch is marked as the head office, so the head office section is hidden on the website.
        Edit a branch and tick “head office” to bring it back.
      <?php else: ?>
        More than one branch is marked as the head office. The website shows the first.
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<?php if ($confirm): ?>
  <div class="alert alert-danger" role="alert" tabindex="-1">
    <h2 class="alert-heading h6">Delete “<?= e($confirm['name']) ?>”?</h2>
    <p>This deletes the branch for good, along with its address, phone number and opening hours.</p>
    <form method="post" action="branches.php" class="d-grid gap-2 d-sm-flex">
      <input type="hidden" name="id" value="<?= (int) $confirm['id'] ?>">
      <button class="btn btn-danger" type="submit" name="action" value="delete">Yes, delete it</button>
      <a class="btn btn-outline-secondary" href="branches.php">Cancel</a>
    </form>
  </div>
<?php endif; ?>

<?php if (!$branches): ?>

  <div class="card">
    <div class="card-body p-4 p-md-5 text-center">
      <p class="mb-3">You have not added any branches yet.</p>
      <a class="btn btn-primary" href="branch-form.php">Add the first one</a>
    </div>
  </div>

<?php else: ?>

  <?php

  function branch_actions(array $branch, bool $stacked = false): void { ?>
    <div class="<?= $stacked ? 'd-grid gap-2' : 'd-flex flex-wrap gap-2' ?>">
      <a class="btn btn-sm btn-outline-secondary" href="branch-form.php?id=<?= (int) $branch['id'] ?>">
        Edit<span class="visually-hidden"> <?= e($branch['name']) ?></span>
      </a>
      <a class="btn btn-sm btn-outline-danger" href="branches.php?confirm=<?= (int) $branch['id'] ?>">
        Delete<span class="visually-hidden"> <?= e($branch['name']) ?></span>
      </a>
    </div>
  <?php } ?>

  <?php ?>
  <div class="d-md-none">
    <?php foreach ($branches as $branch): ?>
      <div class="card mb-3">
        <div class="card-body">

          <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <h2 class="h6 mb-0"><?= e($branch['name']) ?></h2>
            <?php if ($branch['isHeadOffice']): ?>
              <span class="badge rounded-pill text-bg-success flex-shrink-0">Head office</span>
            <?php endif; ?>
          </div>

          <p class="text-body-secondary small mb-3">
            <?php if ($branch['street'] || $branch['area']): ?>
              <?= e(trim($branch['street'] . ' ' . $branch['area'])) ?><br>
            <?php endif; ?>
            <?= e($branch['phone']) ?>
          </p>

          <?php branch_actions($branch, true); ?>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="table-responsive d-none d-md-block">
    <table class="table table-hover align-middle">
      <caption class="visually-hidden">All Book &amp; Board branches, with their contact details and the actions available</caption>
      <thead>
        <tr>
          <th scope="col">Branch</th>
          <th scope="col">Address</th>
          <th scope="col">Phone</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($branches as $branch): ?>
          <tr>
            <th scope="row" class="fw-semibold">
              <?= e($branch['name']) ?>
              <?php if ($branch['isHeadOffice']): ?>
                <span class="badge rounded-pill text-bg-success ms-1">Head office</span>
              <?php endif; ?>
              <?php if ($branch['location']): ?>
                <span class="d-block fw-normal text-body-secondary small"><?= e($branch['location']) ?></span>
              <?php endif; ?>
            </th>
            <td><?= e(trim($branch['street'] . ', ' . $branch['area'], ', ')) ?></td>
            <td><?= e($branch['phone']) ?></td>
            <td><?php branch_actions($branch); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
