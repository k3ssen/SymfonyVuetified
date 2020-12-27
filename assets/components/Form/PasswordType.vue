<template>
    <v-text-field type="password" v-model="form.vars.data" v-bind="attributes"/>
</template>
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";

    export default {
        mixins: [formWidgetMixin],
    };
</script>