<template>
    <v-list-item :href="href" v-on="$listeners">
        <v-list-item-action>
            <v-icon>{{ icon }}</v-icon>
        </v-list-item-action>

        <v-list-item-content>
            <v-list-item-title>
                <slot></slot>
            </v-list-item-title>
        </v-list-item-content>
    </v-list-item>
</template>

<script lang="ts">
    import {Vue, Prop, Component} from 'vue-property-decorator';

    @Component
    export default class SvMenuItem extends Vue {
        @Prop({default: null})
        public href!: string;

        @Prop({default: ''})
        public icon!: string;
    }
</script>