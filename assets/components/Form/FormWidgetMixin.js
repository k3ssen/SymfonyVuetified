const formWidgetMixin = {
    data: () => ({
        alreadyRendered: false,
        attributes: {},
    }),
    props: {
        form: {type: Object},
    },
    created() {
        this.attributes = this.$attrs;
        this.alreadyRendered = this.form.rendered;
        this.form.rendered = true;
    },
};
export { formWidgetMixin };