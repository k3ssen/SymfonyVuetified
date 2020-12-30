<template>
    <div v-if="!alreadyRendered" v-bind="row_attributes">
        <template v-if="form.vars.compound && !componentType">
            <v-alert v-if="form.vars.errors" type="error">
                {{ form.vars.errors }}
            </v-alert>
            <label v-if="form.vars.label" v-bind="form.vars.label_attr">
                {{ form.vars.label }}
            </label>
            <div v-for="(child, key) in form.children" :key="key" v-bind="child.vars.row_attr">
                <form-widget :form="child" />
            </div>
        </template>

        <component v-else-if="componentType" :is="componentType" v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)" :form="form" />

        <!-- In case of a multi-select, create a hidden field for each selected value -->
        <template v-if="form.vars.multiple">
            <input type="hidden" v-for="value in form.vars.data" :name="form.vars.full_name" :value="value" />
        </template>
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class FormWidget extends Mixins(FormWidgetMixin) {
        componentType: string|null = null;

        created() {
            this.setComponentType();
            this.setAttributes();
        }

        setComponentType() {
            for (const blockPrefix of this.form.vars.block_prefixes.reverse()) {
                if ((this.$options as any).components[blockPrefix as any]) {
                    this.componentType = blockPrefix;
                    return;
                }
            }
            // Fallback to TextType if the form is not compound.
            if (!this.form.vars.compound) {
                this.componentType = 'TextType';
            }
        }

        setAttributes() {
            let attr: any = {};
            attr['form'] = this.form;
            attr['label'] = this.form.vars.label ?? this.form.vars.name;
            attr['hint'] = this.form.vars.help;
            attr['error-messages'] = this.form.vars.errors;
            attr['error'] = !!this.form.vars.errors;
            attr['persistent-hint'] = !!this.form.vars.help;

            if (!this.form.vars.multiple) {
                attr['name'] = this.form.vars.full_name;
            }
            if (!Array.isArray(this.form.vars.attr)) {
                attr = Object.assign(attr, this.form.vars.attr);
            }
            this.attributes = attr;
        }
    }
</script>