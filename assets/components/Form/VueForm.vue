<template>
    <v-form :method="method" v-bind="$attrs">
        <slot name="default">
            <form-widget :form="form"></form-widget>
        </slot>
        <form-widget :form="form"></form-widget><!-- render remainder -->
        <slot name="actions">
            <v-btn type="submit">
                {{ labelSubmit }}
            </v-btn>
        </slot>
    </v-form>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import IForm from "./IForm";

    @Component
    export default class VueForm extends Vue{
        @Prop()
        form!: IForm;
        @Prop({default: 'save'})
        labelSubmit!: string;
        @Prop({default: 'post'})
        method!: string;
    }
</script>