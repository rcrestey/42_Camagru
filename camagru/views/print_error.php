<?php if($_GET['0'] === 'error' && isset($_GET['1'])): unset($_GET['0']); unset($_GET['view'])?>

<div class="alert-error">
    <p>Sorry but, it's not a correct answer:</p>
    <ul>
        <?php foreach($_GET as $error): ?>
            <li><?= $error; ?></li>
        <?php endforeach;?>
    </ul>
</div>
<?php endif; ?>