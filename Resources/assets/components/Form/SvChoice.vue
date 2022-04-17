<template>
    <div>
        <v-combobox
                v-if="attributes.allow_add"
                v-model="form.vars.data"
                v-bind="Object.assign(attributes, $attrs)"
            >
            <!-- Pass on all slots (note that the selection slot will be overwritten below ) -->
            <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
            <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
                <slot :name="slot" v-bind="scope"/>
            </template>

            <template v-slot:selection="{ item, parent, disabled, select }">
                <v-chip v-if="attributes.multiple" :disabled="disabled" close @click:close="parent.selectItem(item)">
                    {{ getItemByValue(item).label }}
                </v-chip>
                <span v-else>
                    {{ getItemByValue(item).label }}
                </span>
            </template>
        </v-combobox>
        <v-autocomplete v-else v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)">
            <!-- Pass on all slots -->
            <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
            <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
                <slot :name="slot" v-bind="scope"/>
            </template>
        </v-autocomplete>

        <!-- In case of a multi-select, create a hidden field for each selected value -->
        <template v-if="form.vars.multiple">
            <input type="hidden" v-for="value in form.vars.data" :name="form.vars.full_name" :value="value" />
        </template>
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin";

    @Component
    export default class SvChoice extends Mixins(FormWidgetMixin) {
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
            attr['clearable'] = this.attributes?.clearable ?? !this.form.vars.required;

            Object.assign(this.attributes, attr);
        }
    }
</script>