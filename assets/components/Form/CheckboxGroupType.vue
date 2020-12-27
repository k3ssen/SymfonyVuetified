<template>
    <div>
        <label v-if="form.vars.label" v-bind="form.vars.label_attr">
            {{ form.vars.label }}
        </label>
        <div v-for="(child, key) in form.children" :key="key" v-bind="child.vars.row_attr" style="margin-bottom: -10px;">
            <form-widget :form="child" />
        </div>
    </div>
</template>
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";
    import FormWidget from "./FormWidget";

    export default {
        components: {FormWidget},
        mixins: [formWidgetMixin],
    };
</script>