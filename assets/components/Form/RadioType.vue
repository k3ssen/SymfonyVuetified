<template>
    <v-radio v-model="form.vars.data" v-bind="attributes"/>
</template>
<script>
    import {formTypeMixin} from "./FormTypeMixin";

    export default {
        mixins: [formTypeMixin],
        created() {
            this.attributes['value'] = this.form.vars.value;
            this.form.vars.data = this.form.vars.data === true ? this.form.vars.value : null
        },
    };
</script>