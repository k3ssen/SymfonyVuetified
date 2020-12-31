<template>
    <v-radio-group v-bind="Object.assign(attributes, $attrs)">
        <div v-for="(child, key) in form.children" :key="key">
            <form-widget :form="child"></form-widget>
        </div>
    </v-radio-group>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import FormWidget from "./FormWidget.global";

    @Component({
        components: { FormWidget }
    })
    export default class CheckboxGroupType extends Mixins(FormWidgetMixin) {

    }
</script>