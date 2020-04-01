<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h2>
            {{ form.vars.label }}
        </h2>
        <table  style="width: 100%">
            <tr v-for="(child, key) in form.children" :key="key">
                <td v-for="subChild in child.children" v-bind="subChild.vars.row_attr">
                    <FormType :form="subChild" :parentForm="subChild" />
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
    </div>
</template>

<script>
    import {formTypeMixin} from "./FormTypeMixin";

    export default {
        name: 'CollectionType',
        mixins: [formTypeMixin],
        methods: {
            addItem() {
                this.form.children.push(JSON.parse(JSON.stringify(this.form.vars.prototype)));
            },
            removeItem(key) {
                this.form.children.splice(key, 1);
            }
        },
    }
</script>