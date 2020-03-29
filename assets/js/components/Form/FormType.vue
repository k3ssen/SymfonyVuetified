<template>
    <div v-if="!alreadyRendered">
        <template v-if="form.vars.compound && !isCollectionType">
            <div v-for="(child, key) in form.children" :key="key">
                <FormType :form="child" />
            </div>
        </template>
        <input v-else-if="isHiddenType"
            type="hidden"
            v-model="form.vars.data"
            v-bind="bindAttributes"
        />
        <component v-else-if="hasAttributes"
            :is="componentType"
            v-model="form.vars.data"
            :value="form.vars.data"
            v-bind="bindAttributes"
        />

        <!-- In case of a multi-select, create a hidden field for each selected value -->
        <template v-if="form.vars.multiple">
            <input type="hidden" v-for="value in form.vars.data" :name="form.vars.full_name" :value="value" />
        </template>
    </div>
</template>

<script>
    import { formTypeMixin } from "./FormTypeMixin";

    export default {
        name: 'FormType',
        mixins: [formTypeMixin],
    }
</script>