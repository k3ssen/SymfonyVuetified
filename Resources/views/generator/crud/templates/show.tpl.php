<?= $helper->getHeadPrintCode($entity_class_name) ?>

{% block body %}
    <h1><?= $entity_class_name ?></h1>

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

    <v-btn href="{{ path('<?= $route_name ?>_index') }}">back to list</v-btn>

    <v-btn color="warning" href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">edit</v-btn>

    {{ include('<?= $route_name ?>/_delete_form.html.twig') }}
{% endblock %}
