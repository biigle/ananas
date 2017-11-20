/**
 * View model for the form to create a new assistance request
 */
biigle.$viewModel('create-ananas-form', function (element) {
    new Vue({
        el: element,
        components: {
            labelTrees: biigle.$require('labelTrees.components.labelTrees'),
        },
        data: {
            labelTrees: biigle.$require('ananas.labelTrees'),
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
        },
        methods: {
            close: function () {
                window.close();
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
        }
    });
});
