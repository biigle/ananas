@extends('manual.base')

@section('manual-title', 'Annotation assistance requests')

@section('manual-content')
<div class="row">
    <p class="lead">
        Learn how to ask any person for advice on the label of an annotation.
    </p>
    <p>
        Sometimes while you annotate images you might not be sure what the correct label is for a particular object or region on the image. Although you are certain that the object or region is of interest, you can't decide between a few candidate labels or can't identify it at all but you know just the right expert who might help you. This is where annotation assistance requests come in. With an annotation assistance request you can ask any person with an email address for help with a particular annotation. BIIGLE handles all the details to make the information exchange efficient and easy.
    </p>
    <p>
        Annotation assistance requests (or just "assistance requests") can be created by project editors or admins. To do so, open the <a href="{{route('manual-tutorials', ['annotations', 'sidebar'])}}#annotations-tab"><i class="fa fa-map-marker"></i> annotations tab</a> in the annotation tool and then select an annotation. At the bottom of the annotations tab you will now see the active button <button class="btn btn-default btn-xs">Request annotation assistance</button>. Click this button to create a new assistance request for the selected annotation. Annotation assistance requests can only be created for a single annotation. When the annotation is deleted, the assistance request is deleted, too.
    </p>
    <p>
        Each new assistance request requires the email address of the receiver of the request (i.e. the person you want to ask for advice) and a short text. Use the text to describe your request and ask questions. Be sure not to write too much, so the receiver of your request does not have to spend too much time with it. Optionally, you can select one or more candidate labels to suggest to the receiver of the assistance request, as well. This makes it even easier for them to respond, as they only have to select a candidate label and then submit the request. Be careful not to select too many candidate labels here, too. Finally, click <button class="btn btn-success btn-xs">Create</button> and you will be redirected to the new assistance request.
    </p>
    <div class="panel panel-info">
        <div class="panel-body text-info">
            Each user can only create one annotation assistance request per minute.
        </div>
    </div>
    <p>
        When a new assistance request has been created, the receiver of the request is automatically notified by email. The email contains a unique link to a website for this specific assistance request. The website displays the image and the annotation that are associated with the assistance request, as well as your request text and suggested labels. The receiver of the assistance request is then asked to either pick one of the suggested labels, enter a response text or both. Once they responded to the assistance request, you will get a notification in your <a href="/manual/tutorials/notifications">notification center</a>. The assistance request is now <span class="label label-default">Closed</span> and the unique link for the receiver of the assistance request is no longer valid.
    </p>
    <p>
        You can view a list of all your annotation assistance requests in the <a href="{{route('index-assistance-requests')}}"><i class="fa fa-comments"></i> Assistance requests</a> tab of your notification center. Click on an assistance request to view its details. If an assistance request is still <span class="label label-info">Open</span> (i.e. has no response yet) only the first of the two tabs in the sidebar, showing the request information, is active. Once the assistance request is closed, the response information tab will be activated, showing you the response text and/or the selected label of the receiver of the request.
    </p>
    <div class="panel panel-info">
        <div class="panel-body text-info">
            You can delete an assistance request at any time. Note that the email is still sent but the link to respond to the assistance request will no longer work.
        </div>
    </div>
    - If receiver selected label, button is displayed to attach the label
    - If label is already attached, no button
    - If label no longer exists, no button
    - If receiver only entered text, button to get to the annotation tool

    - Advice for no spam (one request per suspected label, not per annotation)
</div>
@endsection
