<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <v-sheet elevation="2" class="pa-5">
        <h2>
            {{ form.vars.label }}
        </h2>
        <table>
            <tr v-for="(child, key) in form.children" :key="key">
                <td v-for="subChild in child.children" v-bind="subChild.vars.row_attr">
                    <form-widget :form="subChild" :parentForm="subChild"></form-widget>
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

<script>
    import {formWidgetMixin} from "./FormWidgetMixin";

    export default {
        mixins: [formWidgetMixin],
        computed: {
            prototypeName() {
                return this.form.vars.prototype.vars.name;
            },
            prototype() {
                return JSON.stringify(this.form.vars.prototype);
            },
            childCount() {
                return this.form.children.length;
            }
        },
        methods: {
            addItem() {
                let prototypeString = this.prototype.replace(new RegExp(this.prototypeName, 'g'), this.childCount);
                this.form.children.push(JSON.parse(prototypeString));
            },
            removeItem(key) {
                this.form.children.splice(key, 1);
            }
        },
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