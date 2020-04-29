<template>
    <v-app>
        <div ref="appContent">
            <v-navigation-drawer v-model="drawer" app clipped>
                <slot name="menu"></slot>
            </v-navigation-drawer>

            <v-app-bar app color="primary" dark clipped-left>
                <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
                <v-toolbar-title>{{ title }}</v-toolbar-title>
            </v-app-bar>

            <v-content>
                <v-container>
                    <slot name="before-page" />
                    <fetch-component v-if="$store.fetchUrl" :url="$store.fetchUrl" :fetch-post="useFetch"></fetch-component>
                    <component v-else-if="$window.pageVue" :is="$window.pageVue"></component>
                    <slot v-else></slot>
                    <slot name="after-page" />
                </v-container>
            </v-content>
        </div>
    </v-app>
</template>

<script>
    import FetchComponent from "./FetchComponent";
    export default {
        components: {FetchComponent},
        props: {
            useFetch: {
                type: Boolean,
                default: true
            },
            title: {
                type: String,
                default: "Application"
            },
        },
        data: () => ({
            drawer: null,
        }),
        beforeCreate() {
            if (typeof this.$store.fetchUrl === 'undefined') {
                this.$set(this.$store, 'fetchUrl', null);
            }
        },
        mounted() {
            if (this.useFetch) {
                const vm = this;
                this.$refs.appContent.addEventListener('click', (event) => {
                    let el = event.target;
                    while (el && el.tagName !== 'A') {
                        el = el.parentNode;
                    }
                    if (el) {
                        vm.$store.fetchUrl = el.href;
                        event.preventDefault();
                    }
                });

                window.addEventListener('popstate', (event) => {
                    vm.$store.fetchUrl = event.state;
                });
            }
        },
        watch: {
            '$store.fetchUrl': function() {
                if (this.useFetch) {
                    const href = this.$store.fetchUrl;
                    window.history.pushState(href, href, href);
                }
            }
        }
    };
</script>
