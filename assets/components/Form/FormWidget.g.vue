<template>
    <div v-if="!alreadyRendered" v-bind="row_attributes">
        <template v-if="form.vars.compound && !componentType">
            <div class="form-widget" :class="{ 'use-flex': row }">
                <v-input v-bind="Object.assign(attributes, $attrs)">
                    <slot name="default" v-bind="{ children: form.children, ...form.children }">
                        <slot v-for="(child, key) in form.children" :name="'subform_' + child.vars.name" v-bind="{ subform: child }" >
                            <form-widget
                                :key="key"
                                 :class="{ col: row }"
                                 v-bind="child.vars.row_attr"
                                 :form="child"
                            ></form-widget>
                        </slot>
                    </slot>
                </v-input>
            </div>
        </template>
        <component v-else-if="componentType" :is="componentType" v-bind="$attrs" :form="form">
            <!-- Pass on all slots -->
            <slot v-if="namedSlots" v-for="slot in Object.keys(namedSlots)" :name="slot" :slot="slot"/>
            <template v-for="slot in Object.keys(scopedSlots)" :slot="slot" slot-scope="scope">
                <slot :name="slot" v-bind="scope"/>
            </template>
        </component>
    </div>
</template>

<script lang="ts">
    import {Component, Mixins, Prop} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";

    @Component
    export default class FormWidget extends Mixins(FormWidgetMixin) {
        @Prop({ type: Boolean, default: false })
        row!: boolean;

        componentType: string|null = null;

        created() {
            this.setComponentType();
            this.attributes['form'] = this.form;
        }

        setComponentType() {
            for (const blockPrefix of this.form.vars.block_prefixes.reverse()) {
                if ((this.$options as any).components[blockPrefix as any]) {
                    this.componentType = blockPrefix;
                    return;
                }
            }
            // Fallback to TextType if the form is not compound.
            if (!this.form.vars.compound) {
                this.componentType = 'TextType';
            }
        }
    }
</script>

<style lang="scss">
    .form-widget {
        &:not(.use-flex) > .v-input > .v-input__control > .v-input__slot {
            display: block;
        }
        > .v-input > .v-input__control > .v-input__slot {
            margin-bottom: 0;
        }
    }
    .v-messages__message {
        margin-bottom: 10px;
    }
</style>