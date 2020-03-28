<template>
    <div v-if="!alreadyRendered">
        <div v-if="form.vars.compound">
            <div v-for="(child, key) in form.children" :key="key">
                <AppForm :form="child" />
            </div>
        </div>
        <input v-else-if="isHiddenType"
            type="hidden"
            v-model="form.vars.data"
            v-bind="bindAttributes"
        />
        <component v-else
            :is="componentType"
            v-model="form.vars.data"
            v-bind="bindAttributes"
        />
    </div>
</template>

<script>
    module.exports = {
        data: () => ({
            alreadyRendered: false,
        }),
        props: {
            form: {
                type: Object,
                default: {}
            },
        },
        created() {
            this.alreadyRendered = this.form.rendered;
            this.form.rendered = true;
        },
        computed: {
            componentType() {
                if (this.isHiddenType) {
                    return 'input';
                }
                if (this.isChoiceType) {
                    return 'v-select';
                }
                return 'v-text-field';
            },
            bindAttributes() {
                const attr = this.form.vars.attr;
                attr['label'] = this.form.vars.label;
                attr['hint'] = this.form.vars.help;
                attr['persistent-hint'] = !!this.form.vars.help;

                if (this.isChoiceType) {
                    attr['label'] = this.form.vars.label;
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
        }
    }
</script>