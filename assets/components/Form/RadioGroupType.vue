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
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";
    export default {
        mixins: [formWidgetMixin],
        created() {
            for (const child of this.form.children) {
                if (child.vars.data === true) {
                    this.form.vars.data = child.vars.value;
                }
            }
        },
    };
</script>