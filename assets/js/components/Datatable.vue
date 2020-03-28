<template>
    <div>
        <v-text-field
            v-model.lazy="options.search"
            append-icon="mdi-magnify"
            label="Search"
            single-line
            hide-details
        />
        <v-data-table
            v-if="datatable.useAjax"
            :headers="datatable.headers"
            :items="datatable.items"
            :items-per-page="datatable.itemsPerPage"
            :options.sync="options"
            :loading="loading"
            :server-items-length="total"
            :footer-props="{'items-per-page-options': [10, 25, 50, 100, 500]}"
        >
            <template v-slot:item.actions="{ item }">
                <v-btn v-for="(action, key) in item.actions" :key="key"
                       :href="action.href"
                       small
                       fab
                       depressed
                       :color="action.color"
                       class="ml-1"
                >
                    <v-icon>{{ action.icon }}</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <v-data-table
            v-else
            :headers="datatable.headers"
            :items="datatable.items"
            :items-per-page="datatable.itemsPerPage"
            :search="options.search"
            :footer-props="{'items-per-page-options': [10, 25, 50, 100, 500]}"
        >
            <template v-slot:item.actions="{ item }">
                <v-btn v-for="(action, key) in item.actions" :key="key"
                       :href="action.href"
                       small
                       fab
                       depressed
                       :color="action.color"
                       class="ml-1"
                >
                    <v-icon>{{ action.icon }}</v-icon>
                </v-btn>
            </template>
        </v-data-table>
    </div>
</template>

<script>
    module.exports = {
        data: () => ({
            loading: false,
            options: {
                search: "",
            }
        }),
        props: {
            datatable: {
                type: Object,
                default: {}
            },
        },
        watch: {
            'datatable.options': {
                handler () {
                    this.itemsPerPage = this.options.itemsPerPage;
                    this.getDataFromApi();
                }
            }
        },
        methods: {
            getDataFromApi() {
                this.loading = true;
                return new Promise((resolve, reject) => {
                    const vm = this;
                    fetch(this.datatable.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.datatable.options),
                    }).then((response) => {
                        response.json().then((data) => {
                            vm.datatable.items = data.items;
                            vm.datatable.total = data.total;
                            vm.loading = false;
                        });
                    });
                });
            },
        },
    }
</script>