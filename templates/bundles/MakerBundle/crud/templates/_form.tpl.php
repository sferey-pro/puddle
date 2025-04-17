{{ form_start(form) }}
    {{ form_widget(form) }}

    <button type="submit" class="btn btn-primary">
        <twig:ux:icon name="tabler:save-changes"/> {{ button_label|default('label.create_<?= $entity_twig_var_singular ?>'|trans) }}
    </button>

    <twig:Button:ActionItem
        withBackToList='<?= $route_name ?>_index'
    />
{{ form_end(form) }}
