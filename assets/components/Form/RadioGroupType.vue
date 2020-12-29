<template>
        <v-radio-group
            v-bind="attributes"
            v-model="form.vars.data"
        >
            <div v-for="(child, key) in form.children" :key="key">
                <form-widget :form="child"></form-widget>
            </div>
        </v-radio-group>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class RadioGroupType extends Mixins(FormWidgetMixin) {
        created() {
            for (const child of this.form.children) {
                if (child.vars.data === true) {
                    this.form.vars.data = child.vars.value;
                }
            }
        }
    }
</script>