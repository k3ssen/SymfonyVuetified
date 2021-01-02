<template>
    <div v-if="!alreadyRendered" v-bind="row_attributes">
        <template v-if="form.vars.compound && !componentType">
            <label v-if="form.vars.label" v-bind="form.vars.label_attr">
                {{ form.vars.label }}
            </label>
            <div v-for="(child, key) in form.children" :key="key" v-bind="child.vars.row_attr">
                <form-widget :form="child" />
            </div>
        </template>

        <component v-else-if="componentType" :is="componentType" v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)" :form="form" />
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import CheckboxGroupType from "./CheckboxGroupType.vue";
    import CheckboxType from "./CheckboxType.vue";
    import ChoiceType from "./ChoiceType.vue";
    import CollectionType from "./CollectionType.vue";
    import DateType from "./DateType.vue";
    import HiddenType from "./HiddenType.vue";
    import PasswordType from "./PasswordType.vue";
    import RadioGroupType from "./RadioGroupType.vue";
    import RadioType from "./RadioType.vue";
    import SwitchType from "./SwitchType.vue";
    import TextareaType from "./TextareaType.vue";
    import TextType from "./TextType.vue";
    import IForm from "./IForm";

    @Component({ components: {
        CheckboxGroupType,
        CheckboxType,
        ChoiceType,
        CollectionType,
        DateType,
        HiddenType,
        PasswordType,
        RadioGroupType,
        RadioType,
        SwitchType,
        TextareaType,
        TextType,
    }})
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

        isRendered(form: IForm) {
            return form.rendered;
        }
    }
</script>