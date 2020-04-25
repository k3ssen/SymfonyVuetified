<template>
    <div>
        <label v-if="form.vars.label" v-bind="form.vars.label_attr">
            {{ form.vars.label }}
        </label>
        <div v-for="(child, key) in form.children" :key="key" v-bind="child.vars.row_attr" style="margin-bottom: -10px;">
            <form-type :form="child" />
        </div>
    </div>
</template>
<script>
    import {formTypeMixin} from "./FormTypeMixin";
    import FormType from "./FormType";
    export default {
        components: {FormType},
        mixins: [formTypeMixin],
    };
</script>