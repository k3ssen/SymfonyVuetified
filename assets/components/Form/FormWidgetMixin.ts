import {Vue, Component, Prop} from "vue-property-decorator";
import IForm from "./IForm";

@Component
export default class FormWidgetMixin extends Vue {
    @Prop()
    form!: IForm;

    alreadyRendered: boolean = false;
    attributes: any = {};

    created() {
        this.attributes = this.$attrs;
        this.alreadyRendered = this.form.rendered;
        this.form.rendered = true;
    }
}