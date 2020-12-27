<template>
    <v-select v-model="form.vars.data" v-bind="attributes"/>
</template>
<script>
    import {formWidgetMixin} from "./FormWidgetMixin";

    export default {
        mixins: [formWidgetMixin],
        created() {
            this.form.vars.data = this.form.vars.data === true ? this.form.vars.value : null;
            this.attributes['value'] = this.form.vars.value;
            this.setChoiceTypeAttributes();
        },
        methods: {
            setChoiceTypeAttributes() {
                let attr = {};
                if (this.form.vars.multiple) {
                    let dataObjects = [];
                    if (this.form.vars.data) {
                        dataObjects = Object.values(JSON.parse(JSON.stringify(this.form.vars.data)));
                    } else if (this.form.vars.value) {
                        dataObjects = Object.values(JSON.parse(JSON.stringify(this.form.vars.value)));
                    }
                    this.form.vars.data = dataObjects;
                    attr['multiple'] = true;
                    attr['deletable-chips'] = true;
                    attr['chips'] = true;
                }
                attr['item-text'] = 'label';
                attr['item-value'] = 'value';
                attr['required'] = this.form.vars.required;
                attr['return-object'] = false;
                attr['items'] = Object.values(JSON.parse(JSON.stringify(this.form.vars.choices)));

                Object.assign(this.attributes, attr);
            }
        }
    };
</script>