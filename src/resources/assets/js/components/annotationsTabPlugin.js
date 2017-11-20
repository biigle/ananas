/**
 * The plugin component showing a button to create a new annotation assistance request
 * in the annotations tab.
 *
 * @type {Object}
 */
biigle.$require('annotations.components.annotationsTabPlugins').assistanceRequest = {
    props: {
        annotations: {
            type: Array,
            required: true,
        },
        url: {
            type: String,
            required: true,
        },
    },
    computed: {
        selectedAnnotations: function () {
            return this.annotations.filter(function (a) {
                return a.selected;
            });
        },
        isDisabled: function () {
            return this.selectedAnnotations.length !== 1;
        },
        href: function () {
            return this.url + this.selectedAnnotations[0].id;
        },
    },
};
