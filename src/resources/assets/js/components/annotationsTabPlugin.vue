<script>
/**
 * The plugin component showing a button to create a new annotation assistance request
 * in the annotations tab.
 *
 * @type {Object}
 */
export default {
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
        selectedAnnotations() {
            return this.annotations.filter((a) => a.selected);
        },
        isDisabled() {
            return this.selectedAnnotations.length !== 1;
        },
        href() {
            return this.url + this.selectedAnnotations[0].id;
        },
    },
};
</script>
