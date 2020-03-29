<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div>
        <h2>
            {{ form.vars.label }}
        </h2>
        <v-expansion-panels>
            <v-expansion-panel v-for="(child, key) in form.children" :key="key">
                <v-expansion-panel-header>
                    # {{ key + 1 }}

                    <template v-slot:actions>
                        <v-btn v-if="form.vars.allow_delete" @click.stop="removeItem(key)">
                            {{ form.vars.btn_delete_txt }}
                        </v-btn>
                    </template>
                </v-expansion-panel-header>
                <v-expansion-panel-content>
                    <FormType :form="child" :parentForm="form" :rootForm="rootForm ? rootForm : form" />
                </v-expansion-panel-content>
            </v-expansion-panel>

        </v-expansion-panels>

        <v-btn v-if="form.vars.allow_add" @click="addItem" class="mt-5" >
            <v-icon color="success">mdi-plus</v-icon>
            {{ form.vars.btn_add_txt }}
        </v-btn>
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