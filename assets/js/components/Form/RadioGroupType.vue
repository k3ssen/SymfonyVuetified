<template>
        <v-radio-group
            v-bind="attributes"
            v-model="form.vars.data"
        >
            <div v-for="(child, key) in form.children" :key="key">
                <FormType :form="child" />
            </div>
        </v-radio-group>
</template>
<script>
    import {formTypeMixin} from "./FormTypeMixin";
    export default {
        mixins: [formTypeMixin],
        created() {
            for (const child of this.form.children) {
                if (child.vars.data === true) {
                    this.form.vars.data = child.vars.value;
                }
            }
        },
    };
</script>