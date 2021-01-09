<template>
    <div>
        <component v-if="component" :is="component"></component>
        <div ref="scriptContainer" style="display: none"></div>
    </div>
</template>


<script lang="ts">
import {Vue, Prop, Component, Watch} from 'vue-property-decorator';

@Component
export default class FetchComponent extends Vue {
    @Prop()
    public url!: string;
    @Prop({default: 'vue', type: String})
    public vueObjectName!: any;
    @Prop({default: 'vueData', type: String})
    public dataObjectName!: any;
    @Prop({default: 'vueStoreData', type: String})
    public storeDataObjectName!: any;

    private component: any = null;

    async mounted() {
        await this.load();
    }

    @Watch('url')
    async urlChanged() {
        (this.$refs['scriptContainer'] as HTMLElement).innerText = ''; // Cleanup script and style container
        await this.load();
    }

    async load() {
        // reset the vueObjects to make sure we won't use an earlier object defined elsewhere.
        window[this.vueObjectName] = {} as any;
        window[this.dataObjectName] = {} as any;
        window[this.storeDataObjectName] = {} as any;
        const response: any = await fetch(this.url, {headers: {'fetch': 'get'}});
        if (!response.ok) {
            console.error('Error occurred while fetching data from ' + this.url)
        } else {
            this.processResponseText(await response.text());
        }
    }
    processResponseText(responseText: string) {
        const contentElement: HTMLElement = document.createElement('div');
        contentElement.innerHTML = responseText; // Add responseText as innerHTML, so its content can be queried.
        // script and style cannot be used inside a vue template, so handle them here
        contentElement.querySelectorAll('script, style').forEach(element => {
            const newElement = document.createElement(element.tagName);
            newElement.textContent = element.textContent;
            (this.$refs['scriptContainer'] as HTMLElement).append(newElement); // script/style is executed once appended to the DOM.
            element!.parentNode!.removeChild(element); // remove script/style from contentElement
        });
        const vueObject: any = window[this.vueObjectName];
        this.loadVueData(vueObject);
        this.loadVueStoreData();
        // Wrap the innerHTML inside a div to make sure there's only one root element.
        vueObject.template = '<div>' + contentElement.innerHTML + '</div>';
        this.component = vueObject;
    }

    loadVueData(vueObject: any) {
        const vueData: any = window[this.dataObjectName];
        if (typeof vueData !== 'undefined') {
            const vueObjectData = vueObject.data ?? {};
            vueObject.data = () => (Object.assign(
                vueData,
                typeof vueObjectData === 'function' ? vueObjectData() : vueObjectData
            ));
        }
    }

    loadVueStoreData() {
        // Update the $store if a store variable has been used.
        let store: any = window[this.storeDataObjectName];
        if (typeof store !== 'undefined') {
            // Add properties to the store using Vue.set to make sure these properties are reactive.
            for (const property in store) {
                if (store.hasOwnProperty(property)) {
                    this.$set((this as any).$store, property, store[property]);
                }
            }
        }
    }
}
</script>