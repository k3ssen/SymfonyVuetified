<template>
    <v-text-field type="password" v-model="form.vars.data" v-bind="attributes"/>
</template>
<script>
    import {formTypeMixin} from "./FormTypeMixin";

    export default {
        mixins: [formTypeMixin],
    };
</script>