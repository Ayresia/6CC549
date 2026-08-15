<?php

require_once(__DIR__ . '/../includes/auth.php');

$staff = require_staff();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    $id     = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');
    $offer  = offers_find($id);

    if (!$offer) {
        flash_set('danger', 'That offer no longer exists.');
    } elseif ($action === 'delete') {
        offers_delete($id);
        flash_set('success', '“' . $offer['title'] . '” has been deleted.');
    } elseif ($action === 'deactivate') {
        offers_set_active($id, false);
        flash_set('success', '“' . $offer['title'] . '” is now inactive and no longer shows on the website.');
    } elseif ($action === 'activate') {
        offers_set_active($id, true);
        flash_set('success', '“' . $offer['title'] . '” is now active and live on the website.');
    }

    header('Location: offers.php');
    exit;
}

$offers    = offers_all();
$confirmId = (int) ($_GET['confirm'] ?? 0);
$confirm   = $confirmId ? offers_find($confirmId) : null;

$pageTitle = 'Manage Offers';
$navHere   = 'offers';
require_once(__DIR__ . '/../includes/staff-header.php');
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div>
    <h1 class="h2 mb-1">Manage Offers</h1>
    <p class="text-body-secondary mb-0">
      <?= count($offers) ?> offer<?= count($offers) === 1 ? '' : 's' ?> in total.
      Anything you make active shows up on the website straight away.
    </p>
  </div>
  <a class="btn btn-primary" href="offer-form.php">Add new offer</a>
</div>

<?php if ($confirm): ?>
  <div class="alert alert-danger" role="alert" tabindex="-1">
    <h2 class="alert-heading h6">Delete “<?= e($confirm['title']) ?>”?</h2>
    <p>This deletes the offer for good. If you only want it off the website, deactivate it instead and the record stays here.</p>
    <form method="post" action="offers.php" class="d-grid gap-2 d-sm-flex">
      <input type="hidden" name="id" value="<?= (int) $confirm['id'] ?>">
      <button class="btn btn-danger" type="submit" name="action" value="delete">Yes, delete it</button>
      <button class="btn btn-outline-secondary" type="submit" name="action" value="deactivate">Deactivate instead</button>
      <a class="btn btn-outline-secondary" href="offers.php">Cancel</a>
    </form>
  </div>
<?php endif; ?>

<?php if (!$offers): ?>

  <div class="card">
    <div class="card-body p-4 p-md-5 text-center">
      <p class="mb-3">You have not added any offers yet.</p>
      <a class="btn btn-primary" href="offer-form.php">Add the first one</a>
    </div>
  </div>

<?php else: ?>

  <?php

  function offer_actions(array $offer, bool $stacked = false): void { ?>
    <div class="<?= $stacked ? 'd-grid gap-2' : 'd-flex flex-wrap gap-2' ?>">
      <a class="btn btn-sm btn-outline-secondary" href="offer-form.php?id=<?= (int) $offer['id'] ?>">
        Edit<span class="visually-hidden"> <?= e($offer['title']) ?></span>
      </a>

      <form method="post" action="offers.php" class="<?= $stacked ? 'd-grid' : 'd-inline' ?>">
        <input type="hidden" name="id" value="<?= (int) $offer['id'] ?>">
        <?php if (!empty($offer['active'])): ?>
          <button class="btn btn-sm btn-outline-secondary" type="submit" name="action" value="deactivate">
            Deactivate<span class="visually-hidden"> <?= e($offer['title']) ?></span>
          </button>
        <?php else: ?>
          <button class="btn btn-sm btn-outline-secondary" type="submit" name="action" value="activate">
            Activate<span class="visually-hidden"> <?= e($offer['title']) ?></span>
          </button>
        <?php endif; ?>
      </form>

      <a class="btn btn-sm btn-outline-danger" href="offers.php?confirm=<?= (int) $offer['id'] ?>">
        Delete<span class="visually-hidden"> <?= e($offer['title']) ?></span>
      </a>
    </div>
  <?php } ?>

  <?php ?>
  <div class="d-md-none">
    <?php foreach ($offers as $offer): ?>
      <div class="card mb-3">
        <div class="card-body">

          <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <h2 class="h6 mb-0">
              <?= e($offer['title']) ?>
              <?php if (!empty($offer['bestseller'])): ?>
                <i class="bi bi-star-fill text-primary small" title="Bestselling: featured on the home page" aria-hidden="true"></i>
                <span class="visually-hidden">Bestselling</span>
              <?php endif; ?>
            </h2>
            <?php if (!empty($offer['active'])): ?>
              <span class="badge rounded-pill text-bg-success flex-shrink-0">Active</span>
            <?php else: ?>
              <span class="badge rounded-pill bg-body-tertiary text-body border flex-shrink-0">Inactive</span>
            <?php endif; ?>
          </div>

          <p class="text-body-secondary small mb-2">
            <?= e($offer['destination']) ?><br>
            <?= e(date_range($offer['startDate'], $offer['endDate'])) ?>
          </p>

          <p class="fs-5 fw-bold mb-3"><?= e(money($offer['price'])) ?></p>

          <?php offer_actions($offer, true); ?>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="table-responsive d-none d-md-block">
    <table class="table table-hover align-middle">
      <caption class="visually-hidden">All Book &amp; Board offers, with their status and the actions available</caption>
      <thead>
        <tr>
          <th scope="col">Offer</th>
          <th scope="col">Destination</th>
          <th scope="col">Price</th>
          <th scope="col">Status</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($offers as $offer): ?>
          <tr>
            <th scope="row" class="fw-semibold">
              <?= e($offer['title']) ?>
              <?php if (!empty($offer['bestseller'])): ?>
                <i class="bi bi-star-fill text-primary small" title="Bestselling: featured on the home page" aria-hidden="true"></i>
                <span class="visually-hidden">Bestselling</span>
              <?php endif; ?>
              <span class="d-block fw-normal text-body-secondary small">
                <?= e(date_range($offer['startDate'], $offer['endDate'])) ?>
              </span>
            </th>
            <td><?= e($offer['destination']) ?></td>
            <td><?= e(money($offer['price'])) ?></td>
            <td>
              <?php if (!empty($offer['active'])): ?>
                <span class="badge rounded-pill text-bg-success">Active</span>
              <?php else: ?>
                <span class="badge rounded-pill bg-body-tertiary text-body border">Inactive</span>
              <?php endif; ?>
            </td>
            <td><?php offer_actions($offer); ?></td>
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
