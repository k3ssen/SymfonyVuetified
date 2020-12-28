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

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import FormWidget from "./FormWidget";

    @Component({
        components: { FormWidget }
    })
    export default class CheckboxGroupType extends Mixins(FormWidgetMixin) {

    }
</script>