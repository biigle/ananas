<template>
    <div class="ananas-assistance-request">
        <button
            v-if="isDisabled"
            class="btn btn-default btn-block"
            title="Please select a single annotation to consult a colleague about"
            disabled="disabled"
            >
            Consult a colleague
        </button>
        <a
            v-else
            :href="href"
            class="btn btn-default btn-block"
            title="Consult a colleague with an annotation assistance request for the selected annotation"
            target="_blank"
            >
            Consult a colleague
        </a>
    </div>
</template>
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
