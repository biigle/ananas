<script>
import {AnnotationCanvasComponent} from '../import.js';
import {ImagesStore} from '../import.js';
import {LoaderMixin} from '../import.js';
import {handleErrorResponse} from '../import.js';
import {SidebarComponent} from '../import.js';
import {SidebarTabComponent} from '../import.js';

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
    data() {
        return {
            image: null,
            annotations: [],
            annotation: null,
        };
    },
    methods: {
        setImageAndAnnotation(image) {
            this.image = image;
            this.annotations.push(this.annotation);
        },
        focusAnnotation() {
            this.$refs.canvas.focusAnnotation(this.annotation, true);
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
            .catch(handleErrorResponse)
            .finally(this.finishLoading);
    },
};
</script>
