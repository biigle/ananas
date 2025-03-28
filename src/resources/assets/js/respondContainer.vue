<script>
import AnanasApi from './api/ananas.js';
import AnanasContainer from './mixins/ananasContainer.vue';

/**
 * View model for the annotation assistance respond view
 */
export default {
    mixins: [AnanasContainer],
    data() {
        return {
            pickedLabel: null,
            responseText: '',
            closed: false,
            errors: [],
            showMinimap: true,
            token: '',
        };
    },
    computed: {
        hasPickedLabel() {
            return this.pickedLabel !== null;
        },
        hasResponseText() {
            return !!this.responseText;
        },
        hasDisabledControls() {
            return this.closed || this.loading;
        },
        hasErrors() {
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
        submit() {
            let payload = {};
            if (this.hasPickedLabel) {
                payload.response_label_id = this.pickedLabel;
            }
            if (this.hasResponseText) {
                payload.response_text = this.responseText;
            }

            this.startLoading();

            AnanasApi.respond({token: this.token}, payload)
                .finally(this.clearErrors)
                .then(this.handleSuccess, this.handleError)
                .finally(this.finishLoading);
        },
        handleSuccess() {
            this.closed = true;
        },
        clearErrors: function (response) {
            this.errors = [];

            return response;
        },
        handleError: function (response) {
            for (let key in response.body) {
                if (!response.body.hasOwnProperty(key)) continue;
                this.errors.push(response.body[key][0]);
            }
        },
        checkForMobile() {
            if (window.innerWidth < 1000) {
                this.showMinimap = false;
            } else {
                this.showMinimap = true;
            }
        },
    },
    created() {
        this.checkForMobile();
        window.addEventListener('resize', this.checkForMobile);

        this.token = biigle.$require('ananas.token');
    },
};
</script>
