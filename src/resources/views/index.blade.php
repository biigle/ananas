@extends('app')

@section('title', "Annotation Assistance Requests")

@section('content')
<div class="container">
    @include('partials.notification-tabs')
    <div class="row">
        <div class="col-sm-3 col-md-2 col-md-offset-1">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation" class="@if ($type === null) active @endif"><a href="{{route('index-assistance-requests')}}" title="Show all assistance requests">All</a></li>
                <li role="presentation" class="@if ($type === 'open') active @endif"><a href="{{route('index-assistance-requests')}}?t=open" title="Show all open assistance requests">Open</a></li>
                <li role="presentation" class="@if ($type === 'closed') active @endif"><a href="{{route('index-assistance-requests')}}?t=closed" title="Show all closed assistance requests">Closed</a></li>
            </ul>
        </div>
        <div class="col-sm-9 col-md-7 col-md-offset-1">
            <ul class="list-unstyled">
                @forelse ($requests as $request)
                    <li>
                        <strong><a href="{{route('show-assistance-request', $request->id)}}">Created <span title="{{$request->created_at}}">{{$request->created_at->diffForHumans()}}</span></a></strong>
                        @unless($type)
                            @if ($request->closed_at)
                                <span class="label label-default" title="{{$request->closed_at}}">Closed</span>
                            @else
                                <span class="label label-info">Open</span>
                            @endif
                        @endunless
                        <p class="text-muted">{{$request->request_text}}</p>
                    </li>
                @empty
                    <li class="text-muted">
                        @if ($type !== null)
                            There are no {{$type}} annotation assistance requests.
                        @else
                            There are no annotation assistance requests yet.
                        @endif
                    </li>
                @endforelse
            </ul>
            <nav class="text-center">
                {{$requests->links()}}
            </nav>
        </div>
    </div>
</div>
@endsection
