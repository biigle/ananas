/**
 * View model for the annotation assistance show view
 */
biigle.$viewModel('ananas-show-container', function (element) {
    new Vue({
        el: element,
        mixins: [biigle.$require('ananas.mixins.ananasContainer')],
    });
});
