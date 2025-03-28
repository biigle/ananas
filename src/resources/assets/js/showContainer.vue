<script>
import AnanasContainer from './mixins/ananasContainer.vue';
import {handleErrorResponse} from './import.js';
import {AnnotationsApi} from './import.js';

/**
 * View model for the annotation assistance show view
 */
export default {
    mixins: [AnanasContainer],
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
    },
    methods: {
        attach() {
            this.startLoading();
            AnnotationsApi.attachLabel({id: this.annotation.id}, {
                    label_id: this.suggestedLabelId,
                    confidence: 1.0,
                })
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
