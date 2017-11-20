/**
 * View model for the ananas container
 */
biigle.$viewModel('ananas-container', function (element) {
    var imagesStore = biigle.$require('annotations.stores.images');
    var annotation = biigle.$require('ananas.annotation');
    annotation.shape = annotation.shape.name;

    new Vue({
        el: element,
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
        methods: {
            setImageAndAnnotation: function (image) {
                this.image = image;
                this.annotations.push(annotation);
            },
            focusAnnotation: function () {
                this.$refs.canvas.focusAnnotation(annotation, true);
            },
            handleLoadingError: function (message) {
                messages.danger(message);
            },
        },
        created: function () {
            this.startLoading();
            imagesStore.fetchAndDrawImage(annotation.image_id)
                .catch(this.handleLoadingError)
                .then(this.setImageAndAnnotation)
                .then(this.focusAnnotation)
                .finally(this.finishLoading);
        },
    });
});
