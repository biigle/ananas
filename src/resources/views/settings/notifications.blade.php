<h4>Annotation assistance request notifications</h4>
<p class="text-muted">
    Notifications when you receive a new annotation assistance request.
</p>
<form id="ananas-notification-settings">
    <div class="form-group">
        <label class="radio-inline">
            <input type="radio" v-model="settings" value="email"> <strong>Email</strong>
        </label>
        <label class="radio-inline">
            <input type="radio" v-model="settings" value="web"> <strong>Web</strong>
        </label>
        <span v-cloak>
            <loader v-if="loading" :active="true"></loader>
            <span v-else>
                <i v-if="saved" class="fa fa-check text-success"></i>
                <i v-if="error" class="fa fa-times text-danger"></i>
            </span>
        </span>
    </div>
</form>

@push('scripts')
<script type="text/javascript">
    biigle.$mount('ananas-notification-settings', {
        mixins: [biigle.$require('core.mixins.loader')],
        data: {
            settings: '{!! $user->getSettings('ananas_notifications', config('ananas.notifications.default_settings')) !!}',
            saved: false,
            error: false,
        },
        methods: {
            handleSuccess: function () {
                this.saved = true;
                this.error = false;
            },
            handleError: function (response) {
                this.saved = false;
                this.error = true;
                biigle.$require('messages').handleErrorResponse(response);
            },
        },
        watch: {
            settings: function (settings) {
                this.startLoading();
                this.$http.post('api/v1/users/my/settings/ananas', {
                        ananas_notifications: this.settings,
                    })
                    .then(this.handleSuccess, this.handleError)
                    .finally(this.finishLoading);
            },
        },
    });
</script>
@endpush
