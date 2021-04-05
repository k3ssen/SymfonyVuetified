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
                    v-bind="Object.assign(attributes, $attrs)"
                    v-on="on"
                    autocomplete="off"
                />
            </template>
            <v-date-picker
                :locale="locale"
                v-model="form.vars.data"
                no-title
                @input="fromDateMenu = false"
            >
                <!-- Pass on all slots -->
                <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
                <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
                    <slot :name="slot" v-bind="scope"/>
                </template>
            </v-date-picker>
        </v-menu>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class SvDate extends Mixins(FormWidgetMixin) {
        locale: string = 'en';
        fromDateMenu: boolean = false;
    }
</script>