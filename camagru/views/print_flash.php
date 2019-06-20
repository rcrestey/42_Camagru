<?php
if (session_status() == PHP_SESSION_NONE)
    session_start(); 
?>
<?php if (isset($_SESSION['flash'])): ?>
    <?php foreach($_SESSION['flash'] as $type => $message): ?>
    <div class = "alert-<?= $type; ?>">
        <?= $message; ?>
    </div>
    <?php endforeach; ?>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>