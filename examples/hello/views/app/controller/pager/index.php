<?= $pager ?>
<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($items as $item) : ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['price'] ?></td>
        </tr>
        <? endforeach; ?>
    </tbody>
</table>
<?= $pager ?>