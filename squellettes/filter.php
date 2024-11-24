<div class="filter">
    <?php $actualLink = preg_replace('/[?&]'.preg_quote($key, '/').'='.preg_quote($value, '/').'(&|$)/', '$1', $_SERVER['REQUEST_URI']); ?>
    <p><?= $value ?></p> <button class="btn btn-cross" onclick="window.location.href='<?= $actualLink ?>'">X</button>
</div>