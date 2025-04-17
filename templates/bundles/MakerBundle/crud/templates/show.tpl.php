<?= $helper->getHeadPrintCode($entity_class_name) ?>

{% block body_id '<?= $entity_twig_var_singular ?>_show' %}

{% block main %}
    <twig:Semantic:Title content="{{ '<?= $entity_twig_var_singular ?>.show' | trans }}" />

    <table class="table">
        <tbody>
<?php foreach ($entity_fields as $field): ?>
            <tr>
                <th><?= ucfirst($field['fieldName']) ?></th>
                <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>

    <twig:Button:ActionItem
        withBackToList='<?= $route_name ?>_index'
        withEdit='<?= $route_name ?>_edit'
        withDelete='<?= $route_name ?>_delete'
        :itemId='<?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>'

        />
{% endblock %}
