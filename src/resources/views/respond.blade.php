@extends('app')
@section('full-navbar', true)

@section('title', "Annotation Assistance Request")

@push('scripts')
@if (app()->environment('local'))
    <script src="{{ cachebust_asset('vendor/annotations/scripts/ol-debug.js') }}"></script>
@else
    <script src="{{ cachebust_asset('vendor/annotations/scripts/ol.js') }}"></script>
@endif
<script src="{{ cachebust_asset('vendor/annotations/scripts/main.js') }}"></script>
<script src="{{ cachebust_asset('vendor/ananas/scripts/main.js') }}"></script>
<script type="text/javascript">
    biigle.$declare('annotations.imageFileUri', '{!! url("api/v1/annotation-assistance-requests/{$request->token}/image") !!}');
    biigle.$declare('annotations.tilesUri', '{{ asset(config('image.tiles.uri')) }}/{uuid}/');
    biigle.$declare('annotations.volumeIsRemote', @if($isRemote) true @else false @endif);
    biigle.$declare('ananas.annotation', {!! $annotation !!});
    biigle.$declare('ananas.token', '{!! $request->token !!}');
</script>
@endpush

@push('styles')
<link href="{{ cachebust_asset('vendor/annotations/styles/ol.css') }}" rel="stylesheet">
<link href="{{ cachebust_asset('vendor/annotations/styles/main.css') }}" rel="stylesheet">
<link href="{{ cachebust_asset('vendor/ananas/styles/main.css') }}" rel="stylesheet">
@endpush

@section('navbar')
<div class="navbar-text navbar-text--ananas">
    Annotation assistance request from <strong>{{$request->user->firstname}} {{$request->user->lastname}}</strong> to <strong>{{$request->email}}</strong>.
</div>
@endsection

@section('content')
<div id="ananas-respond-container" class="annotator-container annotator-container--ananas" v-cloak>
    <div class="annotator-container__canvas">
        <loader-block :active="loading"></loader-block>
        <annotation-canvas
            :editable="false"
            :image="image"
            :annotations="annotations"
            :show-minimap="showMinimap"
            ref="canvas"
            inline-template>
            <div class="annotation-canvas">
                <minimap v-if="showMinimap" :extent="extent" :projection="projection" v-cloak inline-template>
                    <div class="annotation-canvas__minimap"></div>
                </minimap>
            </div>
        </annotation-canvas>
    </div>
    <sidebar open-tab="response" :show-buttons="false">
        <sidebar-tab name="response" icon="fa-comments">
            <div v-if="!closed" class="panel panel-info">
                <div class="panel-body text-info">
                    <strong>Please help {{$request->user->firstname}} {{$request->user->lastname}} to identify this annotation!</strong>
                </div>
            </div>
            <p>{{$request->user->firstname}} says:</p>
            <div class="panel panel-default">
                <div class="panel-body">
                    {{$request->request_text}}
                </div>
            </div>
            <form v-on:submit.prevent="submit">
                @if ($request->request_labels)
                    <p>{{$request->user->firstname}} suggests these labels: <span v-if="!hasPickedLabel" class="text-muted">(select one)</span></p>
                    <div class="panel panel-default panel--ananas">
                        <div class="list-group list-group--ananas">
                            @foreach ($request->request_labels as $label)
                                <button type="button" class="list-group-item text-success" :class="{active: pickedLabel==={{$label['id']}}, disabled: hasDisabledControls}" v-on:click="pickLabel({{$label['id']}})">{{$label['name']}}</button>
                            @endforeach
                        </div>
                    </div>
                @endif
                <p>Your response: <span v-if="hasPickedLabel" class="text-muted" v-cloak>(optional)</span></p>
                <div class="form-group form-group--ananas">
                    <textarea class="form-control" name="response_text" id="response_text" placeholder="Hi {{$request->user->firstname}}, I think this is..." v-model="responseText" :required="!hasPickedLabel" :disabled="hasDisabledControls"></textarea>
                </div>
                <p v-if="hasErrors" v-for="error in errors" class="text-danger" v-text="error" v-cloak></p>
                <button v-if="!closed" type="submit" class="btn btn-success btn-block" :disabled="hasDisabledControls">Submit</button>
                <div v-else v-cloak class="panel panel-success">
                    <div class="panel-body text-success text-center">
                        <strong>Thank you!</strong>
                    </div>
                </div>
            </form>
        </sidebar-tab>
    </sidebar>
</div>
@endsection
