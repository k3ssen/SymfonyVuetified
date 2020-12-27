<template>
    <v-checkbox v-model="form.vars.data" v-bind="attributes"/>
</template>
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";

    export default {
        mixins: [formWidgetMixin],
        created() {
            this.attributes['value'] = this.form.vars.value;
            this.form.vars.data = this.form.vars.data === true ? this.form.vars.value : null
        },
    };
</script>