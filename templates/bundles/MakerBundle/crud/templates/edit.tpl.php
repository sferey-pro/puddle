<?= $helper->getHeadPrintCode('Edit '.$entity_class_name) ?>

{% block body_id '<?= $entity_twig_var_singular ?>_edit' %}

{% block main %}
    <twig:Semantic:Title content="{{ '<?= $entity_twig_var_singular ?>.edit' | trans }}" />

    {{ include('<?= $templates_path ?>/_form.html.twig', { 'button_label': 'label.update_<?= $entity_twig_var_singular ?>'|trans }) }}
{% endblock %}
