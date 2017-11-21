/**
 * A mixin for the annotation assistance respond and show view models
 *
 * @type {Object}
 */
biigle.$component('ananas.mixins.ananasContainer', {
    mixins: [biigle.$require('core.mixins.loader')],
    components: {
        sidebar: biigle.$require('annotations.components.sidebar'),
        sidebarTab: biigle.$require('core.components.sidebarTab'),
        annotationCanvas: biigle.$require('annotations.components.annotationCanvas'),
    },
    data: {
        image: null,
        annotations: [],
    },
    computed: {
        annotation: function () {
            return biigle.$require('ananas.annotation');
        },
    },
    methods: {
        setImageAndAnnotation: function (image) {
            this.image = image;
            this.annotations.push(this.annotation);
        },
        focusAnnotation: function () {
            this.$refs.canvas.focusAnnotation(this.annotation, true);
        },
        handleLoadingError: function (message) {
            biigle.$require('messages.store').danger(message);
        },
    },
    created: function () {
        this.startLoading();
        biigle.$require('annotations.stores.images')
            .fetchAndDrawImage(this.annotation.image_id)
            .then(this.setImageAndAnnotation)
            .then(this.focusAnnotation)
            .catch(this.handleLoadingError)
            .finally(this.finishLoading);
    },
});
