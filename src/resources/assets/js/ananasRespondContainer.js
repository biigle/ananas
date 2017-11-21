/**
 * View model for the annotation assistance respond view
 */
biigle.$viewModel('ananas-respond-container', function (element) {
    var resource = biigle.$require('api.ananas');
    var token = biigle.$require('ananas.token');

    new Vue({
        el: element,
        mixins: [biigle.$require('ananas.mixins.ananasContainer')],
        data: {
            pickedLabel: null,
            responseText: '',
            closed: false,
            errors: [],
        },
        computed: {
            hasPickedLabel: function () {
                return this.pickedLabel !== null;
            },
            hasResponseText: function () {
                return !!this.responseText;
            },
            hasDisabledControls: function () {
                return this.closed || this.loading;
            },
            hasErrors: function () {
                return this.errors.length > 0;
            },
        },
        methods: {
            pickLabel: function (id) {
                if (this.hasDisabledControls) return;

                if (this.pickedLabel === id) {
                    this.pickedLabel = null;
                } else {
                    this.pickedLabel = id;
                }
            },
            submit: function () {
                var payload = {};
                if (this.hasPickedLabel) {
                    payload.response_label_id = this.pickedLabel;
                }
                if (this.hasResponseText) {
                    payload.response_text = this.responseText;
                }

                this.startLoading();

                resource.respond({token: token}, payload)
                    .finally(this.clearErrors)
                    .then(this.handleSuccess, this.handleError)
                    .finally(this.finishLoading);
            },
            handleSuccess: function () {
                this.closed = true;
            },
            clearErrors: function (response) {
                Vue.set(this, 'errors', []);

                return response;
            },
            handleError: function (response) {
                for (var key in response.body) {
                    if (!response.body.hasOwnProperty(key)) continue;
                    this.errors.push(response.body[key][0]);
                }
            },
        },
    });
});
