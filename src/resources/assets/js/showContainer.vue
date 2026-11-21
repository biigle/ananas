<script>
import AnanasContainer from './mixins/ananasContainer.vue';
import GuidelineMixin from './mixins/guideline.js';
import {handleErrorResponse} from './import.js';
import {AnnotationsApi} from './import.js';

/**
 * View model for the annotation assistance show view
 */
export default {
    mixins: [AnanasContainer, GuidelineMixin],
    data() {
        return {
            existingLabels: [],
            userId: null,
            suggestedLabelId: null,
        };
    },
    computed: {
        attachedSuggestedLabel() {
            let labels = this.existingLabels;
            for (let i = labels.length - 1; i >= 0; i--) {
                if (labels[i].label_id === this.suggestedLabelId && labels[i].user_id === this.userId) {
                    return true;
                }
            }

            return false;
        },
        // Guidelines that are not enforced have no effect on attaching the label.
        hasEnforcedAnnotationGuidelines() {
            return this.annotationGuidelines.some(g => g.isEnforced);
        },
        // The colleague could choose any label for the response.
        suggestedLabelAllowed() {
            return this.isLabelAllowedByGuideline(this.suggestedLabelId, this.annotation.shape);
        },
        attachTitle() {
            if (!this.suggestedLabelAllowed) {
                return 'The annotation guideline does not allow this label for the annotation';
            }

            return 'Attach the chosen label to the annotation';
        },
    },
    methods: {
        attach() {
            if (!this.suggestedLabelAllowed) {
                return;
            }

            let payload = {
                label_id: this.suggestedLabelId,
                confidence: 1.0,
            };

            if (this.annotationGuidelineId) {
                payload.guideline_id = this.annotationGuidelineId;
            }

            this.startLoading();
            AnnotationsApi.attachLabel({id: this.annotation.id}, payload)
                .then(this.handleAttachSuccess, handleErrorResponse)
                .finally(this.finishLoading);
        },
        handleAttachSuccess() {
            this.existingLabels.push({label_id: this.suggestedLabelId, user_id: this.userId});
        },
    },
    created() {
        this.existingLabels = biigle.$require('ananas.labels');
        this.userId = biigle.$require('ananas.userId');
        this.suggestedLabelId = biigle.$require('ananas.suggestedLabelId');
    },
};
</script>
