<template>
    <v-radio v-model="form.vars.data" v-bind="attributes"/>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component({})
    export default class RadioType extends Mixins(FormWidgetMixin) {
        created() {
            this.attributes['value'] = this.form.vars.value;
            this.form.vars.data = this.form.vars.data === true ? this.form.vars.value : null;
        }
    }
</script>