import {Messages} from './import';
import {UsersApi} from './import';
import {LabelTrees} from './import';
import {Typeahead} from './import';

/**
 * View model for the form to create a new assistance request
 */
export default new Vue({
    components: {
        labelTrees: LabelTrees,
        typeahead: Typeahead,
    },
    data: {
        typeaheadTemplate: '<span v-text="item.name"></span><br><small v-text="item.affiliation"></small>',
        users: [],
        selectedUser: null,
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
            return this.flatLabels.filter(function (label) {
                return label.selected;
            });
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
        labelTrees() {
            return biigle.$require('ananas.labelTrees');
        },
    },
    methods: {
        close() {
            window.close();
        },
        loadUsers() {
            return UsersApi.query().then(this.usersLoaded, Messages.handleErrorResponse);
        },
        usersLoaded: function (response) {
            // Assemble full username that can be used for searching in the typeahead.
            response.data.forEach(function (user) {
                user.name = user.firstname + ' ' + user.lastname;
            });
            Vue.set(this, 'users', response.data);
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
                    Vue.set(label, 'selected', true);
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
    }
});
