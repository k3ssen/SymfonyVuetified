import {Vue, Component, Prop} from "vue-property-decorator";
import IForm from "./IForm";

@Component
export default class FormWidgetMixin extends Vue {
    @Prop()
    form!: IForm;

    alreadyRendered: boolean = false;
    attributes: any = {};
    row_attributes: any = {};

    created() {
        this.attributes = this.$attrs;
        this.row_attributes = this.form.vars.row_attr;
        this.alreadyRendered = this.fullyRendered(this.form);
        this.form.rendered = true;
    }

    fullyRendered(form: IForm) {
        if (form.rendered) {
            return true;
        }
        // If a form has children, then consider the form to be rendered if all children are rendered.
        const children = Object.values(form.children as object);
        for (const child of children) {
            if (!this.fullyRendered(child as IForm)) {
                return false;
            }
        }
        return children.length > 0;
    }
}