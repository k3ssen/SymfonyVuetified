<template>
    <v-radio v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)">
        <!-- Pass on all slots -->
        <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
        <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
            <slot :name="slot" v-bind="scope"/>
        </template>
    </v-radio>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class SvRadio extends Mixins(FormWidgetMixin) {
        created() {
            this.attributes['value'] = this.form.vars.value;
            this.form.vars.data = this.form.vars.data === true ? this.form.vars.value : null;
        }
    }
</script>