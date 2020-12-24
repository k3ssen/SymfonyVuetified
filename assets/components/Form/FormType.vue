<template>
    <div v-if="!alreadyRendered">
        <template v-if="form.vars.compound && !componentType">
            <label v-if="form.vars.label" v-bind="form.vars.label_attr">
                {{ form.vars.label }}
            </label>
            <div v-for="(child, key) in form.children" :key="key" v-bind="child.vars.row_attr">
                <form-type :form="child" />
            </div>
        </template>

        <component v-else-if="componentType" :is="componentType" v-model="form.vars.data" v-bind="attributes" />

        <!-- In case of a multi-select, create a hidden field for each selected value -->
        <template v-if="form.vars.multiple">
            <input type="hidden" v-for="value in form.vars.data" :name="form.vars.full_name" :value="value" />
        </template>
    </div>
</template>

<script>
    import { formTypeMixin } from "./FormTypeMixin";

    export default {
        name: 'FormType',
        mixins: [formTypeMixin],
        data: () => ({
            componentType: null,
        }),
        created() {
            this.setComponentType();
            this.setAttributes();
        },
        methods: {
            setComponentType() {
                for (const blockPrefix of this.form.vars.block_prefixes.reverse()) {
                    if (this.$options.components[blockPrefix]) {
                        this.componentType = blockPrefix;
                        return;
                    }
                }
                // Fallback to TextType if the form is not compound.
                if (!this.form.vars.compound) {
                    this.componentType = 'TextType';
                }
            },
            setAttributes() {
                let attr = {};
                attr['form'] = this.form;
                attr['label'] = this.form.vars.label;
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
            },
        }
    }
</script>