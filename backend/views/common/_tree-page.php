<?php
$totalCount = count($models);
?>
<div class="pages">
    <?php if($totalCount != 0) : ?>
    <ul class="pagination" >
        <li class="disabled"><span>共<strong><?= $totalCount ?></strong>条记录 </li>
    </ul>
    <?php endif; ?>
</div>
