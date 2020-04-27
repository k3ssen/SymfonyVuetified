<template>
    <v-app>
        <div ref="appContent">
            <v-navigation-drawer v-model="drawer" app clipped>
                <slot name="menu"></slot>
            </v-navigation-drawer>

            <v-app-bar app color="primary" dark clipped-left>
                <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
                <v-toolbar-title>Application</v-toolbar-title>
            </v-app-bar>

            <v-content>
                <v-container>
                        <slot name="before-page" />
                        <external-component v-if="$store.pageUrl" :url="$store.pageUrl" :use-fetch="useFetch"></external-component>
                        <component v-else-if="$window.pageVue" :is="$window.pageVue"></component>
                        <slot v-else></slot>
                        <slot name="after-page" />
                </v-container>
            </v-content>
        </div>
    </v-app>
</template>

<script>
    import ExternalComponent from "./ExternalComponent";
    export default {
        components: {ExternalComponent},
        props: {
            useFetch: {
                type: Boolean,
                default: true
            },
        },
        data: () => ({
            drawer: null,
        }),
        mounted() {
            if (this.useFetch) {
                const vm = this;
                this.$refs.appContent.addEventListener('click', (event) => {
                    let el = event.target;
                    while (el && el.tagName !== 'A') {
                        el = el.parentNode;
                    }
                    if (el) {
                        vm.$store.pageUrl = el.href;
                        event.preventDefault();
                    }
                });

                window.addEventListener('popstate', (event) => {
                    vm.$store.pageUrl = event.state;
                });
            }
        },
        watch: {
            '$store.pageUrl': function() {
                if (this.useFetch) {
                    const href = this.$store.pageUrl;
                    window.history.pushState(href, href, href);
                }
            }
        }
    };
</script>
