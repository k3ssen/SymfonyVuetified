<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" app clipped>
            <slot name="menu"></slot>
<!--            <component :is="vueMenu"></component>-->
        </v-navigation-drawer>

        <v-app-bar app color="primary" dark clipped-left>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>Application</v-toolbar-title>
        </v-app-bar>

        <v-content>
            <v-container>
                <slot name="before-page" />
                <component v-if="pageVue" :is="pageVue"></component>
                <slot v-else></slot>
                <slot name="after-page" />
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    export default {
        data: () => ({
            drawer: null,
            pageVue: null,
        }),
        created() {
            if (typeof window.pageVue !== 'undefined') {
                this.pageVue = pageVue;
            }
        },
        computed: {
            // vuePage() {
            //     return this.$store.vuePage;
            // }
        }
    };
</script>
