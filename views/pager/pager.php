<ul class="pager">
    <? if ($first_page_url !== FALSE) : ?>
        <li><a href="<?= $first_page_url ?>">First</a></li>
    <? endif; ?>

    <? if ($prev_page_url !== FALSE) : ?>
        <li><a href="<?= $prev_page_url ?>">Previous</a></li>
    <? endif; ?>

    <? foreach ($before_links as $page_num => $url) : ?>
        <li><a href="<?= $url ?>"><?= $page_num ?></a></li>
    <? endforeach; ?>

        <li><?= $current_page ?></li>

    <? foreach ($after_links as $page_num => $url) : ?>
        <li><a href="<?= $url ?>"><?= $page_num ?></a></li>
    <? endforeach; ?>

    <? if ($next_page_url !== FALSE) : ?>
    <li><a href="<?= $next_page_url ?>">Next</a></li>
    <? endif; ?>

    <? if ($last_page_url !== FALSE) : ?>
    <li><a href="<?= $last_page_url ?>">Last</a></li>
    <? endif; ?>
</ul>
<div class="pager">
    Items <?= $first_item_offset ?> - <?= $last_item_offset ?> of <?= $total_count ?> (<?= $page_count ?> pages)
</div>