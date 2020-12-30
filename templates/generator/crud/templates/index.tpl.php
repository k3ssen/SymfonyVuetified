<?= $helper->getHeadPrintCode($entity_class_name.' index'); ?>

{% block body %}
    <h1><?= $entity_class_name ?> index</h1>

    {{ vue_data('items', <?= $entity_twig_var_plural ?>) }}
    {{ vue_data('headers', [
<?php foreach ($entity_fields as $field): ?>
        { text: '<?= ucfirst($field['fieldName']) ?>', value: '<?= ucfirst($field['fieldName']) ?>' },
<?php endforeach; ?>
        { text: '', value: 'actions', sortable: false },
    ]) }}
    <v-data-table :headers="headers" :items="items">
        <template v-slot:item.actions="{ item }">
            <v-btn icon :href="'{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': '_id_'}) }}'.replace('_id_', item.<?= $entity_identifier ?>)">
                <v-icon>mdi-eye</v-icon>
            </v-btn>
            <v-btn icon :href="'{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': '_id_'}) }}'.replace('_id_', item.<?= $entity_identifier ?>)">
                <v-icon>mdi-pencil</v-icon>
            </v-btn>
        </template>
    </v-data-table>
    <v-btn color="success" href="{{ path('<?= $route_name ?>_new') }}">Create new</v-btn>
{% endblock %}
