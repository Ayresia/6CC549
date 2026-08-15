<?php

require_once(__DIR__ . '/includes/auth.php');

if ($user = current_user()) {
    header('Location: ' . home_for($user));
    exit;
}

$values = ['name' => '', 'email' => '', 'phone' => ''];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    foreach (['name', 'email', 'phone'] as $field) {
        $values[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    $password = (string) ($_POST['password'] ?? '');
    $confirm  = (string) ($_POST['confirm'] ?? '');

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
            if (email_taken($values['email'])) {
                $errors['email'] = 'An account already exists with this email address. Sign in instead, or use another address.';
            }
        } catch (PDOException $exception) {
            $errors['form'] = 'We cannot set an account up at the moment. Please try again shortly.';
        }
    }

    if ($values['phone'] !== '' && !preg_match('/^[0-9 ()+-]{7,20}$/', $values['phone'])) {
        $errors['phone'] = 'Enter a phone number using digits, spaces and the characters + ( ) - only, for example 07700 900123.';
    }

    if ($password === '') {
        $errors['password'] = 'Choose a password.';
    } elseif (mb_strlen($password) < PASSWORD_MIN) {
        $errors['password'] = 'Your password must be at least ' . PASSWORD_MIN . ' characters long.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Your password must include at least one letter and one number.';
    }

    if ($confirm === '') {
        $errors['confirm'] = 'Type your password a second time to confirm it.';
    } elseif ($password !== '' && $confirm !== $password) {
        $errors['confirm'] = 'The two passwords do not match. Type the same password in both boxes.';
    }

    if (!$errors) {
        try {
            $created = register_customer($values['name'], $values['email'], $password, $values['phone']);
        } catch (PDOException $exception) {
            $created = null;
            $errors['form'] = 'We cannot set an account up at the moment. Please try again shortly.';
        }

        if ($created) {

            sign_in($created);

            header('Location: account.php?welcome=1');
            exit;
        }

        if (!isset($errors['form'])) {
            $errors['email'] = 'An account already exists with this email address. Sign in instead, or use another address.';
        }
    }
}

$pageTitle       = 'Create an account';
$pageDescription = 'Create a Book & Board account to keep your contact details and the packages you have booked with us in one place.';
$navHere         = 'register';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-md-10 col-lg-7 col-xl-6 mx-auto">

        <div class="card">
          <div class="card-body p-4 p-md-5">

            <h1 class="h3 mb-1" id="register-heading">Create an account</h1>
            <p class="text-body-secondary">
              An account holds your contact details and the packages you have booked with us, all in one
              place. It only takes a minute, and you can still search flights and hotels without one.
            </p>
            <?php if ($errors): ?>
              <?php $errorSummaryFor = 'form'; ?>
              <?php require(__DIR__ . '/includes/error-summary.php'); ?>
            <?php endif; ?>

            <form method="post" action="register.php" aria-labelledby="register-heading" novalidate>

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
                <div class="form-text" id="email-help">You will sign in with this address.</div>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">
                  Phone number <span class="text-body-secondary fw-normal">(optional)</span>
                </label>
                <input type="tel" class="form-control<?= invalid($errors, 'phone') ?>"
                       id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"
                       aria-describedby="phone-help<?= isset($errors['phone']) ? ' phone-error' : '' ?>"
                       <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
                <?= field_error($errors, 'phone') ?>
                <div class="form-text" id="phone-help">
                  Only so an agent can call you back about an enquiry. Leave it empty if you would rather we emailed.
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">
                  Password <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input type="password" class="form-control<?= invalid($errors, 'password') ?>"
                       id="password" name="password" autocomplete="new-password"
                       aria-describedby="password-help<?= isset($errors['password']) ? ' password-error' : '' ?>"
                       <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?> required>
                <?= field_error($errors, 'password') ?>
                <div class="form-text" id="password-help">
                  At least <?= PASSWORD_MIN ?> characters, including one letter and one number.
                </div>
              </div>

              <div class="mb-4">
                <label for="confirm" class="form-label">
                  Confirm password <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <input type="password" class="form-control<?= invalid($errors, 'confirm') ?>"
                       id="confirm" name="confirm" autocomplete="new-password"
                       <?= isset($errors['confirm']) ? 'aria-invalid="true" aria-describedby="confirm-error"' : '' ?> required>
                <?= field_error($errors, 'confirm') ?>
              </div>

              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary">Create account</button>
              </div>

              <p class="mb-0">
                Already have an account? <a href="login.php">Sign in</a>.
              </p>

            </form>

          </div>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
