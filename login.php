<?php

require_once(__DIR__ . '/includes/auth.php');

if (isset($_GET['signout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php?signedout=1');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && $signedIn = current_user()) {

    $staffFlag = $signedIn['role'] !== 'staff' && isset($_GET['staff']) ? '?staff=1' : '';

    header('Location: ' . home_for($signedIn) . $staffFlag);
    exit;
}

$values  = ['email' => ''];
$errors  = [];
$needsStaff  = isset($_GET['staff']);
$needsAccount = isset($_GET['account']);
$signedOut   = isset($_GET['signedout']);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    $values['email'] = trim((string) ($_POST['email'] ?? ''));
    $password        = (string) ($_POST['password'] ?? '');

    if ($values['email'] === '') {
        $errors['email'] = 'Enter the email address on your account.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter an email address in the format name@example.com.';
    }

    if ($password === '') {
        $errors['password'] = 'Enter your password.';
    }

    if (!$errors) {
        try {
            $user = authenticate($values['email'], $password);
        } catch (PDOException $exception) {
            $user = null;
            $errors['form'] = 'We cannot sign you in at the moment. Please try again shortly.';
        }

        if ($user) {
            sign_in($user);

            $welcome = $user['role'] === 'staff' ? '' : '?welcome=1';

            header('Location: ' . home_for($user) . $welcome);
            exit;
        }

        if (!isset($errors['form'])) {
            $errors['form'] = 'Email address or password not recognised. Check both and try again.';
        }
    }
}

$pageTitle       = 'Sign in';
$pageDescription = 'Sign in to your Book & Board account to see your contact details and the packages you have booked with us.';
$navHere         = 'login';

require_once(__DIR__ . '/includes/header.php');
?>

  <section class="bg-body-tertiary py-5">
    <div class="container">
      <div class="col-md-9 col-lg-6 col-xl-5 mx-auto">

        <?php

              ?>

        <div class="card">
          <div class="card-body p-4 p-md-5">

            <h1 class="h3 mb-1" id="login-heading">Sign in</h1>
            <p class="text-body-secondary">
              Your account holds your contact details and the packages you have booked with us.
            </p>
            <?php if ($needsStaff): ?>
              <div class="alert alert-warning d-flex gap-2" role="alert">
                <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                <span>That page is for staff. Sign in with a staff account to reach it.</span>
              </div>
            <?php elseif ($needsAccount): ?>
              <div class="alert alert-warning d-flex gap-2" role="alert">
                <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                <span>Sign in to see your account. Your details are not shown to anyone who is not signed in.</span>
              </div>
            <?php elseif ($signedOut): ?>
              <div class="alert alert-success d-flex gap-2" role="alert">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span>You have been signed out.</span>
              </div>
            <?php endif; ?>

            <?php if (isset($errors['form'])): ?>
              <div class="alert alert-danger d-flex gap-2" role="alert" tabindex="-1">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <span><?= e($errors['form']) ?></span>
              </div>
            <?php endif; ?>

            <form method="post" action="login.php" aria-labelledby="login-heading" novalidate>

              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control<?= invalid($errors, 'email') ?>"
                       id="email" name="email" autocomplete="email" value="<?= e($values['email']) ?>"
                       <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>
                       required autofocus>
                <?= field_error($errors, 'email') ?>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control<?= invalid($errors, 'password') ?>"
                       id="password" name="password" autocomplete="current-password"
                       <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>
                       required>
                <?= field_error($errors, 'password') ?>
              </div>

              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary">Sign in</button>
              </div>

              <p class="mb-0">
                No account yet? <a href="register.php">Create one</a>, or
                <a href="contact.php">ask your branch</a> to set it up for you.
              </p>

            </form>

          </div>
        </div>

        <div class="mt-4">
          <p class="text-body-secondary small mb-1">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            Demonstration accounts:
          </p>
          <ul class="text-body-secondary small mb-0">
            <li>Staff: <strong>staff@bookandboard.co.uk</strong> / <strong>Staff2026</strong></li>
            <li>Customer: <strong>demo@bookandboard.co.uk</strong> / <strong>Holiday2026</strong></li>
          </ul>
        </div>

      </div>
    </div>
  </section>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
