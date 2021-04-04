<?= $helper->getHeadPrintCode($entity_class_name.' index'); ?>

{% block body %}
    <h1><?= $entity_class_name ?> index</h1>

    {# TODO: make sure the entity implements \JsonSerializable. For simple usecases you can use the following method:
        public function jsonSerialize()
        {
            return get_object_vars($this);
        }
    #}
    {{ vue_data('items', <?= $entity_twig_var_plural ?>) }}
    {{ vue_data('headers', [
<?php foreach ($entity_fields as $field): ?>
        { text: '<?= ucfirst($field['fieldName']) ?>', value: '<?= $field['fieldName'] ?>' },
<?php endforeach; ?>
        { text: '', value: 'actions', sortable: false },
    ]) }}
    {{ vue_data('search', '') }}
    <v-data-table :headers="headers" :items="items" :search="search">
        <template v-slot:top>
            <v-text-field v-model="search" placeholder="Search" prepend-inner-icon="mdi-magnify"></v-text-field>
        </template>
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
