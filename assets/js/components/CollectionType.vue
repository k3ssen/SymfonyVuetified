<template>
    <div>
        <slot></slot>
        <div v-for="item in items" :key="item">
            <component :is="getComponent(item)" @remove="remove(item)"></component>
        </div>
        <v-btn v-if="allowAdd" @click="add">Add</v-btn>
    </div>
</template>

<script>
    export default {
        props: {
            allowAdd: {
                type: Boolean,
                default: true
            },
            prototypeName: {
                type: String,
                default: '__name__'
            },
            prototypeComponent: {
                type: String,
                default: 0
            },
            initialCount: {
                type: Number,
                default: 0
            },
        },
        data: () => ({
            count: 0,
            items: [],
            createdComponents: {},
        }),
        computed: {
            modelName() {
                const vModelMatches = this.prototypeComponent.match(/v-model="([^ "]*)"/);
                return vModelMatches[1].split('.'+this.prototypeName)[0];
            },
            modelData() {
                const modelNames = this.modelName.split('.');
                let data = this.$store.pageData;
                for (const modelName of modelNames) {
                    data = data[modelName];
                }
                return data;
            }
        },
        methods: {
            updateValue: function () {
                this.$emit('input', this.value)
            },
            getComponent(item) {
                if (item in this.createdComponents) {
                    return this.createdComponents[item];
                }
                return { template: `<v-alert type="error">Something went wrong in CollectionType.vue: no component found for item ${item} </v-alert>`}
            },
            add() {
                const item = this.initialCount + this.count;
                this.count++;
                const modelData = this.modelData;
                const copyData = JSON.parse(JSON.stringify(modelData[this.prototypeName]));

                // // use this trickery to make sure the newly added data stays reactive (source: https://vuejs.org/v2/guide/list.html#Caveats)
                this.$set(modelData, item, copyData);

                const subFormComponentString = this.prototypeComponent
                // eg: replace .__name__.  with [1]
                    .replace(new RegExp("\\."+this.prototypeName+"\\.", "g"), '['+item+'].')
                    // eg: replace prototypeName for all remaining occurrences. Eg: __name__ to 1
                    .replace(new RegExp(this.prototypeName, "g"), item)
                ;

                const vuePageComponent = this.$store.vuePage;

                this.createdComponents[item] = (new Function(
                    'mixin',
                    'return ' + subFormComponentString
                ))({
                    // Share computed and methods from pageComponent as mixin.
                    // Though this can lead to invalid results (like calling data that only exists in the pageComponent)
                    // it can be useful for form-related stuff.
                    computed: vuePageComponent.computed,
                    methods: vuePageComponent.methods
                });
                //
                this.items.push(item);
            },
            remove(item) {
                const index = this.items.indexOf(item);
                if (index !== -1){
                    this.items.splice(index, 1);
                    delete this.modelData[item];
                    delete this.createdComponents[item];
                }
            },
        },
    }
</script>