<template>
    <div  ref="component">
        <component v-if="component" :is="component"></component>
        <div ref="content" style="display: none"></div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            component: null,
        }),
        props: {
            url: {
                type: String,
            },
            useFetch: {
                type: Boolean,
                default: true
            },
            objectName: {
                type: String,
                default: "vue"
            },
            storeName: {
                type: String,
                default: "store"
            },
        },
        async created() {
            await this.load();
            if (this.useFetch) {
                this.$refs.component.addEventListener('submit', (event) => {
                    const el = event.target;
                    if (el.tagName.toLowerCase() === 'form') {
                        event.preventDefault();
                        this.submitForm(el);
                    }
                });
            }
        },
        watch: {
            async url() {
                await this.load();
            }
        },
        methods: {
            async submitForm(formElement) {
                this.$emit('loading');
                // reset the object and store to prevent these from having merged with new data.
                window[this.objectName] = {};
                window[this.storeName] = {};

                const response = await fetch(formElement.action, {
                    // Do NOT add headers, since this will affect the body, causing formData to be sent differently.
                    headers: {'fetch': 'post'},
                    method: "POST",
                    body: new FormData(formElement),
                });
                if (response.redirected) {
                    const url = response.url;
                    window.history.pushState(url, url, url);
                }

                this.processResponseContent(await response.text());
                this.$emit('loaded');
            },
            async load() {
                this.$emit('loading');
                // reset the object and store to prevent these from having merged with new data.
                window[this.objectName] = {};
                window[this.storeName] = {};

                const response = await fetch(this.url, { headers: {'fetch': 'get'} });
                if (response.redirected) {
                    const url = response.url;
                    window.history.pushState(url, url, url);
                }
                this.processResponseContent(await response.text());
            },
            processResponseContent(responseText) {
                // Load the fetched content into the DOM, so it can be analyzed.
                this.$refs.content.innerHTML = responseText;
                // Scripts in innerHTML won't be executed, so replace them with new script-elements.
                for (const script of this.$refs.content.getElementsByTagName('script')) {
                    const newScript = document.createElement('script');
                    newScript.text = script.innerHTML;
                    script.replaceWith(newScript);
                }
                this.loadStoreData();
                this.loadComponent();
                // Cleanup innerHTML as we no longer need it.
                this.$refs.content.innerHTML = '';
                this.$emit('loaded');
            },
            loadStoreData() {
                // Update the $store if a store variable has been used.
                let store = window[this.storeName];
                if (typeof store !== 'undefined') {
                    // Add properties to the store using Vue.set to make sure these properties are reactive.
                    for (const property in store) {
                        if (store.hasOwnProperty(property)) {
                            Vue.set(this.$store, property, store[property]);
                        }
                    }
                }
            },
            loadComponent() {
                const vueObject = window[this.objectName];
                // If no vue object is available, throw an exception to point out this component is not used correctly.
                if (typeof vueObject === 'undefined') {
                    throw new Error('Expected variable "'+this.objectName+'" was not found in ' + this.url);
                } else {
                    // If a template tag is used, then add this to the object.
                    const template = this.$refs.content.getElementsByTagName('template')[0];
                    if (template) {
                        vueObject.template = template;
                    }
                    this.component = vueObject;
                }
            }
        }
    }
</script>