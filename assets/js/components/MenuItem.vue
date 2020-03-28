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

<script>
    export default {
        props: {
            href: {
                type: String,
                default: ""
            },
            icon: {
                type: String,
                default: ""
            },
        },
    }
</script>