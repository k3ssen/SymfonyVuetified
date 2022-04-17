# SymfonyVuetifiedBundle

## Symfony form as Vue component

Using form-functions in Twig or form_themes to create a Vuetify-form is difficult, especially when dealing with
edge-cases. This bundle introduces the `FormVue` class that processes Symfony's `FormView` to enable json_encoding.
This json is passed to the `sv_form` vue component to have the entire form rendered in Vue.

Example Usage:
```vue
{% block body %}
    {{ vue_data('form', form) }} {# json_encoding of the form is handled by the vueDataStorage #}
    <sv-form :form="form"></sv-form>
{% endblock %}
```

You can take full control and render parts individually. It takes some getting used to
it, because obviously Vue works differently from Twig's form-method, but it is quite powerful.

There are several ways you can use to render different parts of your form:

**1) Using form-widget and full object paths**

```vue
<sv-form :form="form">
    <sv-form-widget :form="form.children.name"></sv-form-widget>
    <sv-form-widget :form="form.children.email"></sv-form-widget>
</sv-form>
```
**2) Using the `children` parameter provided by the default slot:**
```vue
<sv-form :form="form" v-slot="{ children }">
    <sv-form-widget v-for="(child, key) of children" :key="key" :form="child"></sv-form-widget>
</sv-form>
```
(this approach can be mixed with the first approach)

**3) Using the child form names of default slot:**
```vue
<sv-form :form="form" v-slot="{ name, email }">
    <sv-form-widget :form="name"></sv-form-widget>
    <sv-form-widget :form="email"></sv-form-widget>
</sv-form>
```
(this approach can be mixed with the previous approaches)

**4) Using subform slots for the children:**
```vue
<sv-form :form="form">
    <template v-slot:subform_name="{ subform }">
        <sv-form-widget :form="subform"></sv-form-widget>
    </template>
    <!-- email is still rendered by the sv-form -->
</sv-form>
```
The `subform_` prefix is being used here to make sure slots won't overlap (e.g. when a field is called 'default').

Subform slots are defined within the default slot, so this approach cannot be combined with the previous ones.
Doing so would result in these subform slots being ignored by vue.

Using subform slots can be useful when you want to make changes for specific fields but don't need to change
the rendering (or order) of other fields.


**5) Using specific component types:**
```vue
<sv-form :form="form" v-slot="{ name, text }">
    <sv-text :form="name"></sv-text>
    <sv-textarea :form="text"></sv-textarea>
</sv-form>
```
Here the sv-text and sv-texteara components are used no matter what type that is provided by
the serverside form definition.

This last option can be useful when you want to use slots that are defined by Vuetify, since the
form-widget cannot cascade all slots (this would conflict with the default-slot).

## Custom form-type-components

To create a custom form-type, you should have a look at the `Resources/assets/components/Form` directory in this bundle.
Most of the logic you need is put in the `FormTypeMixin`, so you probably want to extend that to have some stuff taken
care of.

For example, if you want to create a wysiwyg text-editor using quill, you could create a EditorType:
```vue
<template>
    <div class="text-editor-type">
        <!-- use a hidden field to use when submitting the form -->
        <input type="hidden" :name="form.vars.full_name" :value="form.vars.data" />
        <quill-editor
            ref="myTextEditor"
            v-model="form.vars.data"
        />
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import quillEditor from 'vue-quill-editor';
    
    @Component
    export default class EditorType extends Mixins(FormWidgetMixin) {
        created() {
            // do stuff like applying settings to the editor...
        }
    }
</script>
```

Now you could use this component directly by using something like `<editor-type :form="form"></editor-type>`.


The block_prefixes are used to determine what component should be used for each individual field.
By setting a `block_prefix` in your FormType, you can specify a different component that you want to use for your
field. So within your form type class, you can use something like this:
```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder->add('text', null, [
        'label' => 'Text',
        'block_prefix' => 'EditorType',
    ]);
}
```
