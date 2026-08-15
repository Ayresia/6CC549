<?php

require_once(__DIR__ . '/includes/auth.php');

$user = require_customer();

$values = [
    'name'  => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? '',
];

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    $stored = user_find((int) $user['id']);

    if ($stored) {
        $values = ['name' => $stored['name'], 'email' => $stored['email'], 'phone' => $stored['phone']];
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    foreach (['name', 'email', 'phone'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if ($values['name'] === '') {
        $errors['name'] = 'Enter your full name.';
    } elseif (mb_strlen($values['name']) > 100) {
        $errors['name'] = 'Your name can be up to 100 characters.';
    }

    if ($values['email'] === '') {
        $errors['email'] = 'Enter your email address.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address, in the format name@example.com.';
    } elseif (mb_strlen($values['email']) > 190) {
        $errors['email'] = 'That email address is too long for us to store.';
    } else {
        try {

            if (email_taken($values['email'], (int) $user['id'])) {
                $errors['email'] = 'Another account already uses this email address. Use a different one.';
            }
        } catch (PDOException $exception) {
            $errors['form'] = 'We cannot save your details at the moment. Please try again shortly.';
        }
    }

    if ($values['phone'] !== '' && !preg_match('/^[0-9 ()+-]{7,20}$/', $values['phone'])) {
        $errors['phone'] = 'Enter a phone number using digits, spaces and the characters + ( ) - only, for example 07700 900123.';
    }

    if (!$errors) {
        try {
            $saved = user_update((int) $user['id'], $values['name'], $values['email'], $values['phone']);
        } catch (PDOException $exception) {
            $saved = false;
            $errors['form'] = 'We cannot save your details at the moment. Please try again shortly.';
        }

        if ($saved) {

            $_SESSION['user'] = user_find((int) $user['id']) ?? $user;

            header('Location: account.php?updated=1');
            exit;
        }

        if (!isset($errors['form'])) {
            $errors['email'] = 'Another account already uses this email address. Use a different one.';
        }
    }
}

$pageTitle       = 'Update your details';
$pageDescription = 'Correct the name, email address and telephone number Book & Board holds on your account.';
$navHere         = 'account';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-lg-9 col-xl-8">
        <h1>Update your details</h1>
        <p class="lead mb-0">
          Change what we hold here and it updates straight away. You do not need to ring your branch.
        </p>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="col-md-10 col-lg-7 col-xl-6">

        <div class="card">
          <div class="card-body p-4 p-md-5">
            <?php if ($errors): ?>
              <?php $errorSummaryFor = 'form'; ?>
              <?php require(__DIR__ . '/includes/error-summary.php'); ?>
            <?php endif; ?>

            <form method="post" action="account-details.php" novalidate>

              <p class="text-body-secondary small">
                <span aria-hidden="true">*</span> Required, unless a field says otherwise.
              </p>

              <div class="mb-3">
                <label for="name" class="form-label">
                  Full name <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input type="text" class="form-control<?= invalid($errors, 'name') ?>"
                       id="name" name="name" value="<?= e($values['name']) ?>" autocomplete="name"
                       <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?>
                       required autofocus>
                <?= field_error($errors, 'name') ?>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">
                  Email address <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input type="email" class="form-control<?= invalid($errors, 'email') ?>"
                       id="email" name="email" value="<?= e($values['email']) ?>" autocomplete="email"
                       aria-describedby="email-help<?= isset($errors['email']) ? ' email-error' : '' ?>"
                       <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?> required>
                <?= field_error($errors, 'email') ?>
                <div class="form-text" id="email-help">You sign in with this address, so changing it changes your sign in.</div>
              </div>

              <div class="mb-4">
                <label for="phone" class="form-label">
                  Phone number <span class="text-body-secondary fw-normal">(optional)</span>
                </label>
                <input type="tel" class="form-control<?= invalid($errors, 'phone') ?>"
                       id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"
                       aria-describedby="phone-help<?= isset($errors['phone']) ? ' phone-error' : '' ?>"
                       <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'phone') ?>
                <div class="form-text" id="phone-help">
                  Only so an agent can call you back about an enquiry. Clear it if you would rather we emailed.
                </div>
              </div>

              <div class="d-grid gap-2 d-sm-flex">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn btn-outline-secondary" href="account.php">Cancel</a>
              </div>

            </form>

          </div>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
