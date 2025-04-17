<?php foreach ($entity_fields as $field): ?>
<td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
<?php endforeach; ?>
<td class="text-end">
    <twig:Button:ActionItem
        withShow='<?= $route_name ?>_show'
        withEdit='<?= $route_name ?>_edit'
        withDelete='<?= $route_name ?>_delete'
        :itemId='<?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>'

        />
</td>
