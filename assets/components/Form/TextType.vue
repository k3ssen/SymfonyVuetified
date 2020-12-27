<template>
    <v-text-field v-model="form.data" v-bind="attributes"/>
</template>
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";

    export default {
        mixins: [formWidgetMixin],
    };
</script>