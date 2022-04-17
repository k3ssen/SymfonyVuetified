# SymfonyVuetifiedBundle

## Twig functions

### vue_add([key: string], [value])
This twig-function lets you add serverside data into the the vue-data. Any data you put in it will be json-serialized.
Note that this requires that objects must be JsonSerializable if you want to make them available to vue.

Example:
```twig
{{ vue_add('product', product) }}
```
Is the same as
```
<script>
    vue = {
        data: () => ({
            product: {{ product | json_encode | raw }}
        })
    }
</script>
```
You cannot use script-tags inside the element that is targetted by vue, but you *can*  use `vue_add` pretty much
wherever you want.

### get_vue_data()
This twig method fetches the json object that contains all data added by `vue_data`.
It is used in [_vue_script.html.twig](../views/layout/_vue_script.html.twig) to load the data into the vue-object.

### vue_store([key: string], [value]) & get_vue_store()
These methods are practically the same as `vue_add` and `get_vue_data`. It's simply a different variable, so it can be
used in [vue-object-init.ts](../assets/vue-object-init.ts) to treat it differently:
Data added by `vue_store` and fetched through `get_vue_store` will be loaded as Vue observable into the `$store`
variable that can be accessed in any vue component.

### vue_prop([key: string], [value])
Same as `vue_add`, but it returns the key value, so you can make some code more concise:
```
{{ vue_add('product', product) }}
<some-component :product="product" ></some-component>
```
Can also be written as:
```
<some-component :product="{{ vue_prop('product', product) }}" ></some-component>
```

### |vue filter
As an alternative to `vue_prop` you can also use the `vue` filter:
```
<some-component :product="{{ product|vue('product') }}" ></some-component>
```
In case of objects, you can even omit the key:
```
<some-component :product="{{ product|vue }}" ></some-component>
```
Without providing the key, the key will be the same as the object-variable (in this case 'product').
This can only be used for object, because twig's context is used to find the variable name of the
matching object. In case of primitives (string, int) there's too much chance of variables with
same value.
