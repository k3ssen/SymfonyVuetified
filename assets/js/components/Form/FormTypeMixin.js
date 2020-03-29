const formTypeMixin = {
    data: () => ({
        alreadyRendered: false,
        customFormType: false,
    }),
    props: {
        form: {type: Object},
    },
    created() {
        this.alreadyRendered = this.form.rendered;
        this.form.rendered = true;
    },
    computed: {
        /**
         * Using hasAttributes is need to make sure the bindAttributes are only bind AFTER they are available.
         * Otherwise an empty set could be bound before being set. Despite being computed, it will not be updated
         * afterwards.
         */
        hasAttributes() {
            return Object.keys(this.bindAttributes).length > 0;
        },
        bindAttributes() {
            const attr = {}; //this.form.vars.attr;
            attr['label'] = this.form.vars.label;
            attr['hint'] = this.form.vars.help;
            if (!this.form.vars.multiple) {
                attr['name'] = this.form.vars.full_name;
            }
            attr['persistent-hint'] = !!this.form.vars.help;

            attr['error'] = !!this.form.vars.errors;
            attr['error-messages'] = this.form.vars.errors;

            if (this.isChoiceType) {
                attr['item-text'] = 'label';
                attr['item-value'] = 'value';
                attr['chips'] = this.form.vars.multiple;
                attr['multiple'] = this.form.vars.multiple;
                attr['items'] = this.form.vars.choices;
            }
            if (this.isPasswordType) {
                attr['type'] = 'password';
            }
            if (this.isCollectionType || this.customFormType) {
                attr['form'] = this.form;
            }
            return attr;
        },
        componentType() {
            for (const blockPrefix of this.form.vars.block_prefixes.reverse()) {
                if (this.$options.components[blockPrefix]) {
                    this.customFormType = true;
                    return blockPrefix;
                }
            }
            if (this.isChoiceType) {
                return 'v-select';
            }
            // if (this.isCollectionType) {
            //     return 'CollectionType';
            // }
            // if (this.isDateType) {
            //     return 'DateType';
            // }
            return 'v-text-field';
        },
        isHiddenType() {
            return this.form.vars.block_prefixes.includes('hidden');
        },
        isPasswordType() {
            return this.form.vars.block_prefixes.includes('password');
        },
        isDateType() {
            return this.form.vars.block_prefixes.includes('date');
        },
        isChoiceType() {
            return this.form.vars.block_prefixes.includes('choice');
        },
        isCollectionType() {
            return this.form.vars.block_prefixes.includes('collection');
        },
    },
};
export { formTypeMixin };