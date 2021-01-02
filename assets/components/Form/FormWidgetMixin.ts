import {Vue, Component, Prop} from "vue-property-decorator";
import IForm from "./IForm";

@Component
export default class FormWidgetMixin extends Vue {
    @Prop()
    form!: IForm;
    @Prop({type: Boolean, default: false})
    forceRender!: boolean;

    alreadyRendered: boolean = false;
    attributes: any = {};
    row_attributes: any = {};

    created() {
        if (this.forceRender) {
            this.resetRendered(this.form);
        }
        this.attributes = this.$attrs;
        this.row_attributes = this.form.vars.row_attr;
        this.alreadyRendered = this.checkRendered(this.form);
        if (!this.alreadyRendered) {
            this.form.rendered = true;
        }
    }

    resetRendered(form: IForm) {
        form.rendered = false;
        for (const child of Object.values(form.children as object)) {
            this.resetRendered(child as IForm);
        }
    }

    checkRendered(form: IForm) {
        if (form.rendered) {
            return true;
        }
        // If a form has children, then consider the form to be rendered if all children are rendered.
        const children = Object.values(form.children as object);
        for (const child of children) {
            if (!this.checkRendered(child as IForm)) {
                return false;
            }
        }
        return children.length > 0;
    }
}