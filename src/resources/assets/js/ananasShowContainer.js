/**
 * View model for the annotation assistance show view
 */
biigle.$viewModel('ananas-show-container', function (element) {
    var annotation = biigle.$require('ananas.annotation');
    var userId = biigle.$require('ananas.userId');
    var suggestedLabelId = biigle.$require('ananas.suggestedLabelId');
    var annotationsApi = biigle.$require('api.annotations');
    var messages = biigle.$require('messages.store');

    new Vue({
        el: element,
        mixins: [biigle.$require('ananas.mixins.ananasContainer')],
        data: {
            existingLabels: biigle.$require('ananas.labels'),
        },
        computed: {
            attachedSuggestedLabel: function () {
                var labels = this.existingLabels;
                for (var i = labels.length - 1; i >= 0; i--) {
                    if (labels[i].label_id === suggestedLabelId && labels[i].user_id === userId) {
                        return true;
                    }
                }

                return false;
            },
        },
        methods: {
            attach: function () {
                this.startLoading();
                annotationsApi.attachLabel({id: annotation.id}, {
                        label_id: suggestedLabelId,
                        confidence: 1.0,
                    })
                    .then(this.handleAttachSuccess, messages.handleErrorResponse)
                    .finally(this.finishLoading);
            },
            handleAttachSuccess: function () {
                this.existingLabels.push({label_id: suggestedLabelId, user_id: userId});
            },
        }
    });
});
