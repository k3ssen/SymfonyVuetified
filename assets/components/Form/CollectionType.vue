<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-sheet elevation="2" class="pa-5 mb-3">
        <h2>
            {{ form.vars.label }}
        </h2>
        <table>
            <tr v-for="(child, key) in form.children" :key="key">
                <template v-if="child.children.length">
                    <td v-for="subChild in child.children" v-bind="subChild.vars.row_attr">
                        <form-widget :form="subChild" :parentForm="subChild"></form-widget>
                    </td>
                </template>
                <td v-else>
                    <form-widget :form="child" :parentForm="child"></form-widget>
                </td>
                <td>
                    <v-btn v-if="form.vars.allow_delete" @click.stop="removeItem(key)">
                        {{ form.vars.btn_delete_txt }}
                    </v-btn>
                </td>
            </tr>
            <tr v-if="form.vars.allow_add">
                <v-btn  @click="addItem" class="mt-5" >
                    <v-icon color="success">mdi-plus</v-icon>
                    {{ form.vars.btn_add_txt }}
                </v-btn>
            </tr>
        </table>
    </v-sheet>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import IForm from "./IForm";

    @Component
    export default class CollectionType extends Mixins(FormWidgetMixin) {
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

<style scoped>
    table {
        width: 100%;
        margin: 0 -10px;
    }
    td {
        padding: 0 10px;
    }
</style>