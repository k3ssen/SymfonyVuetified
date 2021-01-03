<template>
    <div>
        <input type="hidden" :name="form.vars.full_name" :value="form.vars.data" />
        <v-slider v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)"></v-slider>
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class RangeType extends Mixins(FormWidgetMixin) {

    }
</script>