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
    biigle.$declare('annotations.tilesUri', '{{ asset(config('image.tiles.uri')) }}/{uuid}/');
    biigle.$declare('annotations.volumeIsRemote', @if ($isRemote) true @else false @endif);
    biigle.$declare('ananas.annotation', {!! $annotation !!});
    biigle.$declare('ananas.userId', {!! $user->id !!});
    biigle.$declare('ananas.suggestedLabelId', {!! $request->response_label_id !!});
    biigle.$declare('ananas.labels', {!! $existingLabels !!});
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
    @if ($request->closed_at)
        <sidebar open-tab="response" v-cloak>
    @else
        <sidebar open-tab="request" v-cloak>
    @endif
        <sidebar-tab name="request" icon="comment" title="Request information" class="sidebar-tab--flex">
            <div class="sidebar-tab__content">
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
            </div>
            <div class="sidebar-tab__foot">
                <form method="POST" action="{{url('api/v1/annotation-assistance-requests/'.$request->id)}}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-danger btn-block" title="Delete this annotation assistance request" @unless ($request->closed_at) onclick="return confirm('Are you sure you want to delete this unanswered assistance request?')" @endunless>Delete this request</button>
                </form>
            </div>
        </sidebar-tab>
        @if ($request->closed_at)
            <sidebar-tab name="response" icon="comments" title="Response information" class="sidebar-tab--flex">
                <div class="sidebar-tab__content">
                    @if ($request->response_text)
                        <p>
                            {{$request->email}} responded:
                        </p>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{$request->response_text}}
                            </div>
                        </div>
                    @endif
                    @if ($request->response_label)
                        @if ($request->response_text)
                            <p>They chose the following suggested label:</p>
                        @else
                            <p>{{$request->email}} chose the following suggested label:</p>
                        @endif
                        <div class="panel panel-default">
                            <ul class="list-group">
                                <li class="list-group-item suggested-label">
                                    <span class="suggested-label__color" style="background-color:#{{$request->response_label['color']}}"></span> {{$request->response_label['name']}}
                                </li>
                            </ul>
                            @if (!$responseLabelExists)
                                <div class="panel-body text-danger">
                                    This label does not exist any more!
                                </div>
                            @else
                                <div v-if="attachedSuggestedLabel" v-cloak class="panel-body text-success">
                                    This label is attached!
                                </div>
                            @endif
                        </div>
                        @if ($responseLabelExists)
                            <button v-if="!attachedSuggestedLabel" v-cloak type="button" class="btn btn-success btn-block" title="Attach the chosen label to the annotation" v-on:click="attach">Attach this label</button>
                        @endif
                    @endif
                    @if (!$request->response_label || !$responseLabelExists)
                        <p>
                            <a href="{{route('show-annotation', $request->annotation_id)}}" class="btn btn-default btn-block" title="View the annotation in the annotation tool">View in annotation tool</a>
                        </p>
                    @endif
                </div>
                <div class="sidebar-tab__foot">
                    <form method="POST" action="{{url('api/v1/annotation-assistance-requests/'.$request->id)}}">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="btn btn-danger btn-block" title="Delete this annotation assistance request">Delete this request</button>
                    </form>
                </div>
            </sidebar-tab>
        @else
            <sidebar-tab name="response" icon="comments" :disabled="true" title="{{$request->email}} has not responded yet"></sidebar-tab>
        @endif
    </sidebar>
</div>
@endsection
