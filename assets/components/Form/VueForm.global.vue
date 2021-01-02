<template>
    <v-form :method="method" v-bind="$attrs">

        <v-alert v-if="remainingErrors.length > 0" type="error">
            <div v-for="error in remainingErrors">
                <strong>{{ error.label }}:</strong> {{ error.message }}
            </div>
        </v-alert>

        <slot name="default">
            <form-widget :form="form"></form-widget>
        </slot>
        <form-widget :form="form"></form-widget><!-- render remainder -->

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
    import FormWidget from "./FormWidget.global.vue";

    @Component({
        components: { FormWidget },
    })
    export default class VueForm extends Vue{
        @Prop()
        form!: IForm;
        @Prop({default: 'save'})
        labelSubmit!: string;
        @Prop({default: 'post'})
        method!: string;

        remainingErrors: any = [];

        public mounted() {
            this.setRemainingErrors(this.form);
        }

        public setRemainingErrors(form: any) {
            if (!form.rendered && form.vars.errors) {
                this.remainingErrors.push({
                    label: form.vars?.label || form.vars.name,
                    message: form.vars.errors,
                });
            }
            for (const child of Object.values(form.children)) {
                this.setRemainingErrors(child);
            }
        }
    }
</script>