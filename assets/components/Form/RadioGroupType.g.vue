<template>
    <v-radio-group v-bind="Object.assign(attributes, $attrs)" v-model="form.vars.data">
        <v-radio
            v-for="(choice, key) of choices" :key="key"
            :name="form.vars.full_name + '[]'"
            :label="choice.label"
            :value="choice.value"
            v-bind="choice.attr"
        >
            <!-- Pass on all slots -->
            <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
            <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
                <slot :name="slot" v-bind="scope"/>
            </template>
        </v-radio>
    </v-radio-group>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import IForm from "./IForm";

    @Component
    export default class RadioGroupType extends Mixins(FormWidgetMixin) {
        choices: any = [];
        created() {
            this.attributes['value'] = this.form.vars.value;
            if (this.form.vars.value) {
                this.form.vars.data = this.form.vars.value;
            }
            this.choices = Object.values(JSON.parse(JSON.stringify(this.form.vars.choices)));

            for (const child of (this.form.children as IForm[])) {
                child.rendered = true;
            }
        }
    }
</script>