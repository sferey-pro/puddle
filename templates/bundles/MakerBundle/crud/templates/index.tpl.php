<?= $helper->getHeadPrintCode($entity_class_name.' index'); ?>

{% block body_id '<?= $entity_twig_var_singular ?>_index' %}

{% block main %}
    <twig:Semantic:Title content="{{ '<?= $entity_twig_var_singular ?>.index' | trans }}" />

    <table class="table table-striped table-middle-aligned table-borderless table-hover">
        <thead>
            <tr>
<?php foreach ($entity_fields as $field): ?>
                <th scope="col"><?= ucfirst($field['fieldName']) ?></th>
<?php endforeach; ?>
                <th scope="col" class="text-end"><twig:ux:icon name="settings" /> {{ 'label.actions'|trans }}</th>
            </tr>
        </thead>
        <tbody>
        {% for <?= $entity_twig_var_singular ?> in <?= $entity_twig_var_plural ?> %}
            <tr id="<?= $entity_twig_var_singular ?>_{{ <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?> }}">
                {{ include('<?= $entity_twig_var_singular ?>/_item.html.twig', { <?= $entity_twig_var_singular ?>: <?= $entity_twig_var_singular ?> }) }}
            </tr>
        {% else %}
            <tr>
                <td colspan="<?= (count($entity_fields) + 1) ?>">{{ 'default.no_record_found'|trans }}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <twig:Button:CreateNew routeName='<?= $route_name ?>_new' />
{% endblock %}
