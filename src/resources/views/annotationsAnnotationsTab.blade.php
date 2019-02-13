@can('add-annotation', $image)
<component :is="plugins.assistanceRequest" :annotations="annotations" url="{{route('create-assistance-request')}}?annotation_id=" inline-template>
    <div class="ananas-assistance-request">
        <button v-if="isDisabled" class="btn btn-default btn-block" title="Please select a single annotation to make a new annotation assistance request" disabled="disabled">Request annotation assistance</button>
        <a v-else :href="href" class="btn btn-default btn-block" title="Make a new annotation assistance request for the selected annotation" target="_blank">Request annotation assistance</a>
    </div>
</component>
@endcan
