/**
 * View model for the annotation assistance show view
 */

import AnanasContainer from './mixins/ananasContainer';
import {Messages} from './import';
import {AnnotationsApi} from './import';

export default new Vue({
    mixins: [AnanasContainer],
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
        existingLabels() {
            return biigle.$require('ananas.labels');
        },
        annotation() {
            return biigle.$require('ananas.annotation');
        },
        userId() {
            return biigle.$require('ananas.userId');
        },
        suggestedLabelId() {
            return biigle.$require('ananas.suggestedLabelId');
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
    }
});
