<template>
    <div class="form-widget">
        <v-input v-bind="Object.assign(attributes, $attrs)">
            <v-card outlined class="mb-1">
                <v-tabs :vertical="verticalTabs" v-model="tab" background-color="grey lighten-5">
                    <slot name="tabs">
                        <v-tab v-for="(child, key) in form.children" :key="key" :class="{ 'error--text': hasError(child) }">
                            <slot name="tab" v-bind="{ child: child }">
                                <v-icon v-if="hasError(child)" color="error" class="mr-2">mdi-alert</v-icon>
                                <slot name="tab-text" v-bind="{ child: child, text: child.vars.name, key: key }">
                                    {{ key+1 }}
                                </slot>
                            </slot>
                        </v-tab>
                    </slot>
                    <span v-if="form.vars.allow_add" class="v-tab" @click="addItem">
                        <v-icon color="success">mdi-plus</v-icon>
                        <slot name="btn-add-text" v-bind="{ text: form.vars.btn_add_txt }">
                            {{ form.vars.btn_add_txt }}
                        </slot>
                    </span>
                    <v-tabs-items v-model="tab">
                        <v-tab-item v-for="(child, key) in form.children" :key="key" class="pa-4">
                            <slot name="default" v-bind="{ children: child.children, ...child.children}">
                                <sv-form-widget :form="child"></sv-form-widget>
                            </slot>
                            <template>
                                <v-btn v-if="form.vars.allow_delete" @click.stop="removeItem(key)">
                                    <slot name="btn-delete-text" v-bind="{ text: form.vars.btn_delete_txt }">
                                        {{ form.vars.btn_delete_txt }}
                                    </slot>
                                </v-btn>
                            </template>
                        </v-tab-item>
                        <slot name="no-items" v-bind="{ noItemsText }">
                            <v-alert v-if="!form.children.length" class="ma-5" type="info" outlined>
                                <slot name="no-items-text" v-bind="{ noItemsText }">
                                    {{ noItemsText }}
                                </slot>
                            </v-alert>
                        </slot>
                    </v-tabs-items>
                </v-tabs>
            </v-card>
        </v-input>
    </div>
</template>

<script lang="ts">
import {Component, Mixins, Prop} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin";
    import IForm from "./IForm";

    @Component
    export default class SvCollection extends Mixins(FormWidgetMixin) {
        tab: any = 0;
        @Prop({type: String, default: 'There are one or more errors in this collection.'})
        collectionErrorMessage!: string;
        @Prop({type: String, default: 'There are no items in this collection.'})
        noItemsText!: string;
        @Prop({type: Boolean, default: false})
        verticalTabs!: boolean;

        created() {
            if (!this.form.vars.errors && this.hasError(this.form)) {
                this.attributes['error-messages'] = this.collectionErrorMessage;
            }
        }

        get prototypeName() {
            return this.form.vars.prototype.vars.name;
        }
        get prototype() {
            return JSON.stringify(this.form.vars.prototype);
        }
        get childCount() {
            return (this.form.children as IForm[]).length;
        }
        addItem() {
            let prototypeString = this.prototype.replace(new RegExp(this.prototypeName, 'g'), this.childCount.toString());
            (this.form.children as IForm[]).push(JSON.parse(prototypeString));
            this.tab = this.childCount - 1;
        }
        removeItem(key: any) {
            (this.form.children as IForm[]).splice(key, 1);
        }

        hasError(form: any) {
            if (form.vars.errors) {
                return true;
            }
            for (const child of Object.values(form.children)) {
                if (this.hasError(child)) {
                    return true;
                }
            }
            return false;
        }
    }
</script>

<style lang="scss">
    .form-collection-type {
        table {
            width: 100%;
            margin: 0 -10px;
            td {
                padding: 0 10px;
            }
        }
        &form-widget {
            //&:not(.use-flex) > .v-input > .v-input__control > .v-input__slot {
            //    display: block;
            //}
        //    > .v-input > .v-input__control > .v-input__slot {
        //        margin-bottom: 20px;
        //        > label {
        //            font-weight: bold;
        //            font-size: 125%;
        //        }
        //    }
        }
    }
</style>
