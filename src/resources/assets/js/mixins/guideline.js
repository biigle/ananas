import {AnnotationGuideline} from '../import.js';
import {AnnotationGuidelineSelector} from '../import.js';

/**
 * Annotation guideline selection for the labels of the annotation of an assistance
 * request.
 */
export default {
    components: {
        annotationGuidelineSelector: AnnotationGuidelineSelector,
    },
    data() {
        return {
            annotationGuidelines: [],
            mustUseAnnotationGuideline: false,
            annotationGuideline: null,
            volumeId: null,
        };
    },
    computed: {
        // Only enforced guidelines are validated by the API.
        annotationGuidelineId() {
            return this.annotationGuideline?.isEnforced ? this.annotationGuideline.id : null;
        },
    },
    methods: {
        isLabelAllowedByGuideline(labelId, shape) {
            if (!this.annotationGuideline) {
                return true;
            }

            return this.annotationGuideline.allowsLabel(labelId)
                && this.annotationGuideline.allowsShape(shape, labelId);
        },
        handleSelectedAnnotationGuideline(guideline) {
            this.annotationGuideline = guideline;
        },
    },
    created() {
        const shapes = biigle.$require('ananas.availableShapes');
        this.annotationGuidelines = biigle.$require('ananas.annotationGuidelines')
            .map(g => new AnnotationGuideline(g, shapes));
        this.mustUseAnnotationGuideline = biigle.$require('ananas.mustUseAnnotationGuideline');
        this.volumeId = biigle.$require('ananas.volumeId');
    },
};
