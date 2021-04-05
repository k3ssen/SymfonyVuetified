<template>
    <div v-if="attributes.name">
        <input type="hidden" v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)" />
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class SvHidden extends Mixins(FormWidgetMixin) {
    }
</script>