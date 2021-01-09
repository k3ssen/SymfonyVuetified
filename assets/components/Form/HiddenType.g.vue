<template>
    <input type="hidden" v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)" />
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class HiddenType extends Mixins(FormWidgetMixin) {
    }
</script>