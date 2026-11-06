<script>
import GuidelineMixin from './mixins/guideline.js';
import {handleErrorResponse} from './import.js';
import {UsersApi} from './import.js';
import {LabelTrees} from './import.js';
import {Typeahead} from './import.js';

/**
 * View model for the form to create a new assistance request
 */
export default {
    mixins: [GuidelineMixin],
    components: {
        labelTrees: LabelTrees,
        typeahead: Typeahead,
    },
    data() {
        return {
            users: [],
            selectedUser: null,
            labelTrees: [],
            annotationShape: null,
        };
    },
    computed: {
        flatLabels() {
            let labels = [];
            this.labelTrees.forEach(function (tree) {
                Array.prototype.push.apply(labels, tree.labels);
            });

            return labels;
        },
        selectedLabels() {
            return this.flatLabels.filter((label) => label.selected);
        },
        hasTooManySelectedLabels() {
            return this.selectedLabels.length > 5;
        },
        receiverId() {
            return this.selectedUser ? this.selectedUser.id : '';
        },
        receiverName() {
            return this.selectedUser ? this.selectedUser.name : '';
        },
        // Labels that don't allow the shape of the annotation are not disabled in the
        // label trees, so they are reported separately.
        labelsInGuideline() {
            return this.annotationGuideline?.labelIds ?? [];
        },
        enforceGuideline() {
            return !!this.annotationGuideline?.isEnforced;
        },
        // Labels can still be selected if they don't allow the shape of the
        // annotation or if they were selected before the guideline was changed.
        disallowedSelectedLabels() {
            return this.selectedLabels.filter(label => !this.isLabelAllowedByGuideline(label.id, this.annotationShape));
        },
        hasDisallowedSelectedLabels() {
            return this.disallowedSelectedLabels.length > 0;
        },
        disallowedSelectedLabelNames() {
            return this.disallowedSelectedLabels.map(label => label.name).join(', ');
        },
        // The guideline is only validated if labels are suggested.
        missingRequiredGuideline() {
            return this.mustUseAnnotationGuideline
                && !this.annotationGuidelineId
                && this.selectedLabels.length > 0;
        },
    },
    methods: {
        close() {
            window.close();
        },
        loadUsers() {
            return UsersApi.query().then(this.usersLoaded, handleErrorResponse);
        },
        usersLoaded: function (response) {
            // Assemble full username that can be used for searching in the typeahead.
            response.data.forEach(function (user) {
                user.name = user.firstname + ' ' + user.lastname;
            });
            this.users = response.data;
        },
        selectUser: function (user) {
            this.selectedUser = user;
        },
        clearSelectedUser() {
            this.selectedUser = null;
        },
    },
    created() {
        // Select the previously selected labels if there was a validation error.
        let oldLabels = biigle.$require('ananas.oldLabels');
        if (Array.isArray(oldLabels)) {
            let idMap = {};
            oldLabels.forEach(function (id) {
                idMap[id] = null;
            });
            this.flatLabels.forEach(function (label) {
                if (idMap.hasOwnProperty(label.id)) {
                    label.selected = true;
                }
            });
        }

        let promise = this.loadUsers();
        let oldReceiverId = biigle.$require('ananas.oldReceiverId');

        if (oldReceiverId) {
            promise.then(() => {
                for (let i = this.users.length - 1; i >= 0; i--) {
                    if (this.users[i].id == oldReceiverId) {
                        this.selectedUser = this.users[i];
                        return;
                    }
                }
            });
        }

        this.labelTrees = biigle.$require('ananas.labelTrees');
        this.annotationShape = biigle.$require('ananas.annotationShape');
    }
};
</script>
