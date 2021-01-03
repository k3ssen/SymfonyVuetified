<template>
    <v-text-field v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)"></v-text-field>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class TextType extends Mixins(FormWidgetMixin) {

    }
</script>