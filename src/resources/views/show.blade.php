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
    biigle.$declare('annotations.imageFileUri', '{!! url('api/v1/images/{id}/file') !!}');
    biigle.$declare('annotations.volumeIsRemote', @if ($isRemote) true @else false @endif);
    biigle.$declare('ananas.annotation', {!! $annotation !!});
</script>
@endpush

@push('styles')
<link href="{{ cachebust_asset('vendor/annotations/styles/ol.css') }}" rel="stylesheet">
<link href="{{ cachebust_asset('vendor/annotations/styles/main.css') }}" rel="stylesheet">
<link href="{{ cachebust_asset('vendor/ananas/styles/main.css') }}" rel="stylesheet">
@endpush

@section('navbar')
<div class="navbar-text">
    Annotation assistance request from <strong>{{$request->user->firstname}} {{$request->user->lastname}}</strong> to <strong>{{$request->email}}</strong>.
    @if ($request->closed_at)
        <span class="label label-default" title="{{$request->closed_at}}">
            Closed {{$request->closed_at->diffForHumans()}}
        </span>
    @else
        <span class="label label-info">
            No response yet
        </span>
    @endif
</div>
@endsection

@section('content')
<div id="ananas-show-container" class="annotator-container" v-cloak>
    <div class="annotator-container__canvas">
        <loader-block :active="loading"></loader-block>
        <annotation-canvas
            :editable="false"
            :image="image"
            :annotations="annotations"
            ref="canvas"
            inline-template>
            <div class="annotation-canvas">
                <minimap :extent="extent" :projection="projection" inline-template>
                    <div class="annotation-canvas__minimap"></div>
                </minimap>
            </div>
        </annotation-canvas>
    </div>
    <sidebar open-tab="request">
        <sidebar-tab name="request" icon="fa-comment" title="Request information">
            <p>Request created <span title="{{$request->created_at}}">{{$request->created_at->diffForHumans()}}</span>.</p>
            <p>
                Text:
            </p>
            <div class="panel panel-default">
                <div class="panel-body">
                    {{$request->request_text}}
                </div>
            </div>
            @if ($request->request_labels)
                <p>Suggested labels:</p>
                <div class="panel panel-default">
                    <ul class="list-group">
                        @foreach ($request->request_labels as $label)
                            <li class="list-group-item suggested-label">
                                <span class="suggested-label__color" style="background-color:#{{$label['color']}}"></span> {{$label['name']}}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button type="button" class="btn btn-danger btn-block" title="Delete this annotation assistance request">Delete</button>
        </sidebar-tab>
        <sidebar-tab name="response" icon="fa-comments" :disabled="true" title="{{$request->email}} has not responded yet">

        </sidebar-tab>
    </sidebar>
</div>
@endsection
