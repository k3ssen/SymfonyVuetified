<template>
    <v-combobox v-if="attributes.allow_add"
                v-model="form.vars.data"
                v-bind="attributes"
                :return-object="false">
        <template v-slot:selection="{ item }">
            <v-chip>
                {{ getItemByValue(item).label }}
            </v-chip>
        </template>
    </v-combobox>
    <v-autocomplete v-else v-model="form.vars.data" v-bind="attributes"></v-autocomplete>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class ChoiceType extends Mixins(FormWidgetMixin) {
        created() {
            this.attributes['value'] = this.form.vars.value;
            if (this.form.vars.value) {
                this.form.vars.data = this.form.vars.value;
            }
            this.setChoiceTypeAttributes();
        }
        getItemByValue(value: any) {
            return this.attributes['items'].find((item: any) => {
                return item.value === value;
            }) || {
                value,
                label: value,
            };
        }
        setChoiceTypeAttributes() {
            let attr: any = {};
            if (this.form.vars.multiple) {
                let dataObjects: any = [];
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
</script>