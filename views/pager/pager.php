<ul class="pager">
    <? if ($first_page_num !== FALSE) : ?>
        <li><a href="<?= $first_page_num ?>">First</a></li>
    <? endif; ?>

    <? if ($prev_page_num !== FALSE) : ?>
        <li><a href="<?= $prev_page_num ?>">Previous</a></li>
    <? endif; ?>

    <? foreach ($before_links as $page_num => $url) : ?>
        <li><a href="<?= $url ?>"><?= $page_num ?></a></li>
    <? endforeach; ?>

        <li><?= $current_page ?></li>

    <? foreach ($after_links as $page_num => $url) : ?>
        <li><a href="<?= $url ?>"><?= $page_num ?></a></li>
    <? endforeach; ?>

    <? if ($next_page_num !== FALSE) : ?>
    <li><a href="<?= $next_page_num ?>">Next</a></li>
    <? endif; ?>

    <? if ($last_page_num !== FALSE) : ?>
    <li><a href="<?= $last_page_num ?>">Last</a></li>
    <? endif; ?>
</ul>
<div class="pager">
    Items <?= $first_item_offset ?> - <?= $last_item_offset ?> of <?= $total_count ?> (<?= $page_count ?> pages)
</div>