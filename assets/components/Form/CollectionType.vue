<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-sheet elevation="2" class="form-collection-type pa-5 mb-2">
        <div class="form-widget" :class="{ 'use-flex': row }">
            <v-input v-bind="Object.assign(attributes, $attrs)">
                <table>
                    <tr v-for="(child, key) in form.children" :key="key">
                        <template v-if="child.children.length">
                            <td v-for="subChild in child.children" v-bind="subChild.vars.row_attr">
                                <form-widget :form="subChild"></form-widget>
                            </td>
                        </template>
                        <td v-else>
                            <form-widget :form="child"></form-widget>
                        </td>
                        <td>
                            <v-btn v-if="form.vars.allow_delete" @click.stop="removeItem(key)">
                                {{ form.vars.btn_delete_txt }}
                            </v-btn>
                        </td>
                    </tr>
                    <tr v-if="form.vars.allow_add">
                        <v-btn  @click="addItem" class="mt-5 ml-2" >
                            <v-icon color="success">mdi-plus</v-icon>
                            {{ form.vars.btn_add_txt }}
                        </v-btn>
                    </tr>
                </table>
            </v-input>
        </div>
    </v-sheet>
</template>

<script lang="ts">
import {Component, Mixins, Prop} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import IForm from "./IForm";

    @Component
    export default class CollectionType extends Mixins(FormWidgetMixin) {
        @Prop({ type: Boolean, default: false })
        row!: boolean;

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
        }
        removeItem(key: any) {
            (this.form.children as IForm[]).splice(key, 1);
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
        .form-widget {
            &:not(.use-flex) > .v-input > .v-input__control > .v-input__slot {
                display: block;
            }
            > .v-input > .v-input__control > .v-input__slot {
                margin-bottom: 20px;
                > label {
                    font-weight: bold;
                    font-size: 125%;
                }
            }
        }
    }
</style>
