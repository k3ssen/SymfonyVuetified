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

    get scopedSlots() {
        // Vue 2.5 uses '$slots' as named slots and '$scopedSlots' for parameterized slots.
        // In Vue 2.6, all slots are merged into '$scopedSlots'.
        // In Vue 3 the '$scopedSlots' are renamed to '$slots'
        // In Vue 2.6 '$scopedSlots' are used; In Vue 3 these are renamed to '$slots'
        return this.$scopedSlots ?? this.$slots;
    }

    get namedSlots() {
        // Once (in Vue 3) $slots have replaced $scopedSlots, the namedSlots are no longer used.
        if (!!this.$scopedSlots) {
            return this.$slots;
        }
        return null;
    }

    created() {
        if (this.forceRender) {
            this.resetRendered(this.form);
        }
        this.row_attributes = this.form.vars.row_attr;
        this.alreadyRendered = this.checkRendered(this.form);
        if (!this.alreadyRendered) {
            this.form.rendered = true;
        }

        this.setAttributes();
    }

    setAttributes() {
        let attr: any = {};
        attr['label'] = this.form.vars.label ?? this.form.vars.name;
        attr['label'] = attr['label'] === false ? null : attr['label'];
        attr['hint'] = this.form.vars.help;
        attr['error-messages'] = this.form.vars.errors;
        attr['error'] = !!this.form.vars.errors;
        attr['persistent-hint'] = !!this.form.vars.help;

        if (this.form.vars.disabled) {
            attr['disabled'] = true;
        }
        if (this.form.vars.required) {
            attr['required'] = true;
        }
        if (!this.form.vars.multiple) {
            attr['name'] = this.form.vars.full_name;
        }
        if (!Array.isArray(this.form.vars.attr)) {
            attr = Object.assign(attr, this.form.vars.attr);
        }
        this.attributes = Object.assign(this.attributes, attr);
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