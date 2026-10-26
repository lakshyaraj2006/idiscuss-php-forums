<?php
if (isset($_SESSION['alert'])):
    $alert = $_SESSION['alert'];

    $type = in_array($alert['type'], ALERT_TYPES, true)
        ? $alert['type']
        : ALERT_INFO;

    unset($_SESSION['alert']);
?>

<div class="alert alert-<?= htmlspecialchars($type) ?> alert-dismissible fade show mb-0 rounded-0" role="alert">
    <strong><?= ALERT_LABELS[$type] ?>!</strong> <?= htmlspecialchars($alert['message']) ?>

    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php endif; ?>