<?= $helper->getHeadPrintCode('New '.$entity_class_name) ?>

{% block body_id '<?= $entity_twig_var_singular ?>_new' %}

{% block main %}
    <twig:Semantic:Title content="{{ '<?= $entity_twig_var_singular ?>.new' | trans }}" />

    {{ include('<?= $templates_path ?>/_form.html.twig') }}
{% endblock %}
