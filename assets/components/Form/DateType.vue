<template>
        <v-menu
            v-model="fromDateMenu"
            offset-y
            max-width="290px"
            min-width="290px"
        >
            <template v-slot:activator="{ on }">
                <v-text-field
                    :value="form.vars.data"
                    append-outer-icon="mdi-calendar"
                    v-bind="attributes"
                    v-on="on"
                    autocomplete="off"
                />
            </template>
            <v-date-picker
                :locale="locale"
                v-model="form.vars.data"
                no-title
                @input="fromDateMenu = false"
            ></v-date-picker>
        </v-menu>
</template>
<script>
    import {formTypeMixin} from "./FormTypeMixin";

    export default {
        mixins: [formTypeMixin],
        data: () => ({
            locale: 'nl', // get locale from config-value
            fromDateMenu: false,
        }),
    };
</script>