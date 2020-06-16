/**
 * View model for the annotation assistance show view
 */

import AnanasContainer from './mixins/ananasContainer';
import {Messages} from './import';
import {AnnotationsApi} from './import';

export default new Vue({
    mixins: [AnanasContainer],
    data: {
        existingLabels: [],
        userId: null,
        suggestedLabelId: null,
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
                .then(this.handleAttachSuccess, Messages.handleErrorResponse)
                .finally(this.finishLoading);
        },
        handleAttachSuccess() {
            this.existingLabels.push({label_id: this.suggestedLabelId, user_id: this.userId});
        },
    },
    mounted() {
        this.existingLabels = biigle.$require('ananas.labels');
        this.userId = biigle.$require('ananas.userId');
        this.suggestedLabelId = biigle.$require('ananas.suggestedLabelId');
    },
});
