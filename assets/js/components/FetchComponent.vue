<template>
    <div ref="component">
        <v-alert v-if="error" type="error">
            An error occurred.
        </v-alert>
        <component v-if="component" :is="component"></component>
        <div ref="content" style="display: none"></div>
    </div>
</template>

<script>
    export default {
        data: () => ({
            component: null,
            error: false,
        }),
        props: {
            url: {
                type: String,
            },
            fetchPost: {
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
            if (this.fetchPost) {
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
                await this.fetch(formElement.action, {
                    headers: {'fetch': 'post'},
                    method: "POST",
                    body: new FormData(formElement),
                });
            },
            async load() {
                await this.fetch(this.url, { headers: {'fetch': 'get'} });
            },
            async fetch(fetchUrl, fetchOptions) {
                this.error = false;
                this.$emit('loading');
                // reset the object and store to prevent these from conflicting with new data.
                window[this.objectName] = {};
                window[this.storeName] = {};

                const response = await fetch(fetchUrl, fetchOptions);
                if (response.redirected) {
                    const url = response.url;
                    window.history.pushState(url, url, url);
                }
                if (!response.ok) {
                    this.error = true;
                    this.$emit('error', response);
                } else {
                    this.processResponseContent(await response.text());
                }
                this.$emit('loaded');
            },
            processResponseContent(responseText) {
                // Load the responseText into the content's innerHtml, so it can be analyzed.
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
            },
            loadStoreData() {
                // Update the $store if a store variable has been used.
                let store = window[this.storeName];
                if (typeof store !== 'undefined') {
                    // Add properties to the store using Vue.set to make sure these properties are reactive.
                    for (const property in store) {
                        if (store.hasOwnProperty(property)) {
                            this.$set(this.$store, property, store[property]);
                        }
                    }
                }
            },
            loadComponent() {
                const vueObject = window[this.objectName];
                // If no vueObject available, throw an exception to point out this component is not used correctly.
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