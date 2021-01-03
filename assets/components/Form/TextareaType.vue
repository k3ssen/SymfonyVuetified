<template>
    <v-textarea v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)"></v-textarea>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class TextareaType extends Mixins(FormWidgetMixin) {

    }
</script>