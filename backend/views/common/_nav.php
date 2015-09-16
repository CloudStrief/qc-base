<?php
use yii\helpers\Url;
?>

<?php if (!empty($filterNavs) && !empty($filterNavs['actions'])): ?>
<div class="nav">
    <ul class="cc">
        <?php foreach ($filterNavs['actions'] as $label => $action): ?>
            <?php 
                $current = false;
                foreach ($filterNavs['filterAttributes'] as $attribute) {
                    $current = (isset($get[$attribute]) && $get[$attribute] == $action[$attribute]) ? true : false;
                }
            ?>
            <li <?= $current ? 'class="current"' : '' ?> >
                <a href="<?= Url::to($action) ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
