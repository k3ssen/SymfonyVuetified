<template>
    <v-form :method="method" v-bind="$attrs">
        <!-- Make sure that any remaining errors (in non-rendered components) are displayed. -->
        <v-alert v-if="remainingErrors.length > 0" type="error">
            <div v-for="error in remainingErrors">
                <strong v-if="error.label">{{ error.label }}: </strong>{{ error.message }}
            </div>
        </v-alert>

        <slot name="default" v-bind="{ children: form.children, ...form.children }">
            <div v-for="(child, key) in form.children" :key="key">
                <slot :name="'subform_' + child.vars.name" v-bind="{ form: child }" >

                    <form-widget v-bind="child.vars.row_attr" :form="child"></form-widget>
                </slot>
            </div>
        </slot>

        <slot name="actions">
            <v-btn color="primary" type="submit">
                {{ labelSubmit }}
            </v-btn>
        </slot>
    </v-form>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import IForm from "./IForm";

    @Component
    export default class VueForm extends Vue {
        @Prop()
        form!: IForm;
        @Prop({default: 'save'})
        labelSubmit!: string;
        @Prop({default: 'post'})
        method!: string;

        remainingErrors: any = [];

        get scopedSlots() {
            // Vue 2.5 uses '$slots' as named slots and '$scopedSlots' for parameterized slots.
            // In Vue 2.6, all slots are merged into '$scopedSlots'.
            // In Vue 3 the '$scopedSlots' are renamed to '$slots'
            // In Vue 2.6 '$scopedSlots' are used; In Vue 3 these are renamed to '$slots'
            return this.$scopedSlots ?? this.$slots;
        }

        get namedSlots() {
            // Once (in Vue 3) $slots have replaced $scopedSlots, the namedSlots are no longer used.
            if (!!this.$scopedSlots) {
                return this.$slots;
            }
            return null;
        }

        public mounted() {
            this.setRemainingErrors(this.form);
        }

        public setRemainingErrors(form: any) {
            if (!form.rendered && form.vars.errors) {
                this.remainingErrors.push({
                    label: form !== this.form ? form.vars?.label || form.vars.name : null,
                    message: form.vars.errors,
                });
            }
            for (const child of Object.values(form.children)) {
                this.setRemainingErrors(child);
            }
        }
    }
</script>