<?= $helper->getHeadPrintCode('Edit '.$entity_class_name) ?>

{% block body %}
    <h1>Edit <?= $entity_class_name ?></h1>

    {{ include('<?= $route_name ?>/_form.html.twig', {'button_label': 'Update'}) }}

    <v-btn href="{{ path('<?= $route_name ?>_index') }}">back to list</v-btn>

    {{ include('<?= $route_name ?>/_delete_form.html.twig') }}
{% endblock %}
