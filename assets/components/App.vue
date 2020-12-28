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

            <v-main>
                <v-container>
                    <slot name="before-page" />
                    <slot></slot>
                    <slot name="after-page" />
                </v-container>
            </v-main>
        </div>
    </v-app>
</template>

<script lang="ts">
    import {Vue, Prop, Component} from 'vue-property-decorator';

    @Component({})
    export default class App extends Vue {
        @Prop({type: String, default: 'Application'})
        public title!: boolean;

        private drawer: boolean = true;
    };
</script>
