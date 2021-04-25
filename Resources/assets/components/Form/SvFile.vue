<template>
    <v-file-input v-model="form.vars.data" v-bind="Object.assign(attributes, $attrs)">
        <!-- Pass on all slots -->
        <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
        <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
            <slot :name="slot" v-bind="scope"/>
        </template>
    </v-file-input>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from '@k3ssen/symfony-vuetified/components/Form/FormWidgetMixin';

    @Component
    export default class SvFile extends Mixins(FormWidgetMixin) {

    }
</script>