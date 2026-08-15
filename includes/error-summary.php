<?php

$errorSummaryFor = $errorSummaryFor ?? 'form';
?>
<div class="alert alert-danger" role="alert" tabindex="-1">
  <h2 class="alert-heading h6">
    <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
    There <?= count($errors) === 1 ? 'is 1 problem' : 'are ' . count($errors) . ' problems' ?>
    with this <?= e($errorSummaryFor) ?>
  </h2>
  <ul class="mb-0">
    <?php foreach ($errors as $field => $error): ?>
      <li>
        <?php if ($field === 'form'): ?>
          <?= e($error) ?>
        <?php else: ?>
          <a href="#<?= e($field) ?>" class="alert-link"><?= e($error) ?></a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
