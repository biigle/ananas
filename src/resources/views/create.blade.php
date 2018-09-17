@extends('app')

@section('title', 'New Annotation Assistance Request')

@push('scripts')
<script src="{{ cachebust_asset('vendor/label-trees/scripts/main.js') }}"></script>
<script src="{{ cachebust_asset('vendor/ananas/scripts/main.js') }}"></script>
<script type="text/javascript">
    biigle.$declare('ananas.labelTrees', {!! $labelTrees !!});
    @if (old('request_labels'))
        biigle.$declare('ananas.oldLabels', {!! json_encode(old('request_labels')) !!});
    @endif
    @if (old('receiver_id'))
        biigle.$declare('ananas.oldReceiverId', {{old('receiver_id')}});
    @endif
</script>
@endpush

@push('styles')
<link href="{{ cachebust_asset('vendor/label-trees/styles/main.css') }}" rel="stylesheet">
<link href="{{ cachebust_asset('vendor/ananas/styles/main.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="col-sm-8 col-sm-offset-2 col-lg-6 col-lg-offset-3">
        <h2>New Annotation Assistance Request</h2>
        <form id="create-ananas-form" role="form" method="POST" action="{{url('api/v1/annotation-assistance-requests')}}">
            <input type="hidden" name="annotation_id" value="{{ $annotation->id }}">

            <div class="form-group{{ $errors->has('request_text') ? ' has-error' : '' }}">
                <label for="request_text">Short text to describe your request</label>
                <textarea class="form-control" name="request_text" id="request_text" placeholder="Hey Expert, can you tell me the correct label for this annotation?" required>{{ old('request_text') }}</textarea>
                @if ($errors->has('request_text'))
                   <span class="help-block">{{ $errors->first('request_text') }}</span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('receiver_id') ? ' has-error' : '' }}">
                <label for="receiver_id">BIIGLE user who should receive the annotation assistance request <span class="text-muted">(optional)</span></label>
                <typeahead v-if="!selectedUser" class="typeahead--block" :items="users" title="Add a new user to the session" placeholder="Joe User" v-on:select="selectUser" :clear-on-select="true" :template="typeaheadTemplate"></typeahead>
                <div v-else v-cloak class="input-group">
                    <input class="form-control" type="text" readonly :value="receiverName">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear selected user" v-on:click="clearSelectedUser"><i class="fa fa-times"></i></button>
                    </span>
                </div>
                <input type="hidden" name="receiver_id" id="receiver_id" :value="receiverId">
                @if ($errors->has('receiver_id'))
                   <span class="help-block">{{ $errors->first('receiver_id') }}</span>
                @else
                    <span class="help-block">
                        This user will get a notification when the annotation assistance request has been created.
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('request_labels') ? ' has-error' : '' }}">
                <label for="request_labels">Labels to suggest to the receiver of the assistance request <span class="text-muted">(optional)</span></label>
                <p v-if="hasTooManySelectedLabels" v-cloak class="text-info">Don't overwhelm the receiver of the assistance request with suggested labels. Pick only those you think most relevant.</p>
                <div class="form-control request-labels-list" readonly>
                    <span v-for="label in selectedLabels" v-text="label.name"></span>
                </div>
                <input v-for="label in selectedLabels" type="hidden" name="request_labels[]" :value="label.id">
                @if ($errors->has('request_labels'))
                   <span class="help-block">{{ $errors->first('request_labels') }}</span>
                @else
                    <span class="help-block">
                        Choose one or more labels below. The receiver of the assistance request will only see suggested labels, not all available labels.
                    </span>
                @endif
                <div class="request-labels-well well well-sm">
                    <label-trees :trees="labelTrees" :multiselect="true"></label-trees>
                </div>
            </div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="reset" class="btn btn-default" title="Discard this annotation assistance request" v-on:click="close">Discard</button>
            <button type="submit" class="btn btn-success pull-right" title="Create this annotation assistance request">Create</button>
        </form>
    </div>
</div>
@endsection
