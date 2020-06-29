import {AnnotationCanvasComponent} from '../import';
import {ImagesStore} from '../import';
import {LoaderMixin} from '../import';
import {Messages} from '../import';
import {SidebarComponent} from '../import';
import {SidebarTabComponent} from '../import';

/**
 * A mixin for the annotation assistance respond and show view models
 *
 * @type {Object}
 */
export default {
    mixins: [LoaderMixin],
    components: {
        sidebar: SidebarComponent,
        sidebarTab: SidebarTabComponent,
        annotationCanvas: AnnotationCanvasComponent,
    },
    data: {
        image: null,
        annotations: [],
        annotation: null,
    },
    methods: {
        setImageAndAnnotation(image) {
            this.image = image;
            this.annotations.push(this.annotation);
        },
        focusAnnotation() {
            this.$refs.canvas.focusAnnotation(this.annotation, true);
        },
        handleLoadingError(message) {
            Messages.danger(message);
        },
    },
    created() {
        this.annotation = biigle.$require('ananas.annotation');
    },
    mounted() {
        this.startLoading();
        ImagesStore.fetchAndDrawImage(this.annotation.image_id)
            .then(this.setImageAndAnnotation)
            .then(this.focusAnnotation)
            .catch(this.handleLoadingError)
            .finally(this.finishLoading);
    },
};
