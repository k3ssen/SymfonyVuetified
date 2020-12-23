// Only import & load vue if the 'vue' var is set and is an object, so that other pages have smalled js files to load.
if (typeof vue === 'object') {
    (async () => {
        const VueImport = await import('vue');
        const Vue = VueImport.default;
        new Vue(Object.assign({
            el: '#app',
            delimiters: ['@{', '}'], // Twig already uses '{{' and '}}' delimiters, so here we specify an alternative.
        }, vue));
    })();
}
