<template>
    <div v-if="!alreadyRendered">
        <div v-if="form.vars.compound">
            <div v-for="(child, key) in form.children" :key="key" :parentForm="form" :rootForm="rootForm ? rootForm : form">
                <AppForm :form="child" />
            </div>
        </div>
        <input v-else-if="isHiddenType"
            type="hidden"
            v-model="form.vars.data"
            v-bind="bindAttributes"
        />
        <component v-else-if="hasAttributes"
            :form="form"
            :parent="parentForm"
            :rootForm="rootForm ? rootForm : form"
            :is="componentType"
            v-model="form.vars.data"
            v-bind="bindAttributes"
        />

        <!-- In case of a multi-select, ccreate a hidden field for each selected value -->
        <template v-if="form.vars.multiple">
            <input type="hidden" v-for="value in form.vars.data" :name="form.vars.full_name" :value="value" />
        </template>
    </div>
</template>

<script>
    module.exports = {
        name: 'AppForm',
        data: () => ({
            alreadyRendered: false,
        }),
        props: {
            form: {type: Object, default: null},
            parentForm: {type: Object, default: null},
            rootForm: {type: Object, default: null},
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
                return attr;
            },
            componentType() {
                for (const blockPrefix of this.form.vars.block_prefixes.reverse()) {
                    console.log(blockPrefix);
                    if (this.$options.components[blockPrefix]) {
                        return blockPrefix;
                    }
                }
                if (this.isChoiceType) {
                    return 'v-select';
                }
                return 'v-text-field';
            },
            isHiddenType() {
                return this.form.vars.block_prefixes.includes('hidden');
            },
            isTextType() {
                return this.form.vars.block_prefixes.includes('text');
            },
            isPasswordType() {
                return this.form.vars.block_prefixes.includes('password');
            },
            isChoiceType() {
                return this.form.vars.block_prefixes.includes('choice');
            },
        },
    }
</script>