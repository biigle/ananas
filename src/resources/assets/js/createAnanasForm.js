/**
 * View model for the form to create a new assistance request
 */
biigle.$viewModel('create-ananas-form', function (element) {
    var usersApi = biigle.$require('api.users');
    var messages = biigle.$require('messages.store');

    new Vue({
        el: element,
        components: {
            labelTrees: biigle.$require('labelTrees.components.labelTrees'),
            typeahead: biigle.$require('core.components.typeahead'),
        },
        data: {
            labelTrees: biigle.$require('ananas.labelTrees'),
            typeaheadTemplate: '<span v-text="item.name"></span><br><small v-text="item.affiliation"></small>',
            users: [],
            selectedUser: null,
        },
        computed: {
            flatLabels: function () {
                var labels = [];
                this.labelTrees.forEach(function (tree) {
                    Array.prototype.push.apply(labels, tree.labels);
                });

                return labels;
            },
            selectedLabels: function () {
                return this.flatLabels.filter(function (label) {
                    return label.selected;
                });
            },
            hasTooManySelectedLabels: function () {
                return this.selectedLabels.length > 5;
            },
            receiverId: function () {
                return this.selectedUser ? this.selectedUser.id : '';
            },
            receiverName: function () {
                return this.selectedUser ? this.selectedUser.name : '';
            },
        },
        methods: {
            close: function () {
                window.close();
            },
            loadUsers: function () {
                return usersApi.query().then(this.usersLoaded, messages.handleErrorResponse);
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
            clearSelectedUser: function () {
                this.selectedUser = null;
            },
        },
        created: function () {
            // Select the previously selected labels if there was a validation error.
            var oldLabels = biigle.$require('ananas.oldLabels');
            if (Array.isArray(oldLabels)) {
                var idMap = {};
                oldLabels.forEach(function (id) {
                    idMap[id] = null;
                });
                this.flatLabels.forEach(function (label) {
                    if (idMap.hasOwnProperty(label.id)) {
                        Vue.set(label, 'selected', true);
                    }
                });
            }

            var promise = this.loadUsers();
            var oldReceiverId = biigle.$require('ananas.oldReceiverId');

            if (oldReceiverId) {
                promise.bind(this).then(function () {
                    for (var i = this.users.length - 1; i >= 0; i--) {
                        if (this.users[i].id == oldReceiverId) {
                            this.selectedUser = this.users[i];
                            return;
                        }
                    }
                });
            }
        }
    });
});
