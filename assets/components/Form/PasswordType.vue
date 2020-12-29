<template>
    <v-text-field type="password" v-model="form.vars.data" v-bind="attributes"/>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class PasswordType extends Mixins(FormWidgetMixin) {
    }
</script>