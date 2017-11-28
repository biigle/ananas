@extends('manual.base')

@section('manual-title', 'Annotation assistance requests')

@section('manual-content')
<div class="row">
    <p class="lead">
        Learn how to ask any person for advice on the label of an annotation.
    </p>
    <p>
        Sometimes while annotating images, you may not be sure which label is correct for a particular object or region on the image. Although you are certain that the object or region is of interest, you can't decide between a few candidate labels or can't identify it at all but you know just the right expert who might help you. This is where annotation assistance requests come in. With an annotation assistance request you can ask any person with an email address for help with a particular annotation. BIIGLE handles all the details to make the information exchange efficient and easy.
    </p>
    <p>
        Annotation assistance requests (or just "assistance requests") can be created by project editors or admins. To do so, open the <a href="{{route('manual-tutorials', ['annotations', 'sidebar'])}}#annotations-tab"><i class="fa fa-map-marker"></i> annotations tab</a> in the annotation tool and then select an annotation. At the bottom of the annotations tab, you will now see the active button <button class="btn btn-default btn-xs">Request annotation assistance</button>. Click this button to create a new assistance request for the selected annotation. An annotation assistance request can only be created for a single annotation. If the annotation is deleted, the assistance request is deleted, too.
    </p>
    <p>
        Each new assistance request requires the email address of the receiver of the request (i.e. the person you want to ask for advice) and a short text. Use the text to describe your request and ask questions. Be sure not to write too much, so the receiver of your request does not have to spend too much time with it. Optionally, you can select one or more candidate labels to suggest to the receiver of the assistance request. This makes it even easier for them to respond, as they only have to select a candidate label and then submit the response. Be careful not to select too many candidate labels here, as well. Finally, click <button class="btn btn-success btn-xs">Create</button> and you will be redirected to the new assistance request.
    </p>
    <p>
        Each user can only create one annotation assistance request per minute. Be conservative when you send assistance requests to not overwhelm and annoy the people you are asking for help. If you have a few annotations with an uncertain label but which display the same thing, only send a single assistance request. Once you know the correct label you can attach it to the rest of the annotations yourself.
    </p>
    <p>
        Whenever a new assistance request has been created, the receiver of the request is automatically notified by email. The email contains a unique link to a website for this specific assistance request. The website displays the image and the annotation that are associated with the assistance request, as well as your request text and suggested labels. The receiver of the assistance request is then asked to either pick one of the suggested labels, enter a response text or both. Once they responded to the assistance request, you will get a notification in your <a href="/manual/tutorials/notifications">notification center</a>. The assistance request is now <span class="label label-default">Closed</span> and the unique link for the receiver of the assistance request is no longer valid.
    </p>
    <p>
        You can view a list of all your annotation assistance requests in the <a href="{{route('index-assistance-requests')}}"><i class="fa fa-comments"></i> Assistance requests</a> tab of your notification center. Click on an assistance request to view its details. If an assistance request is still <span class="label label-info">Open</span> (i.e. has no response yet) only the first of the two tabs in the sidebar, showing the request information, is active. Once the assistance request is closed, the response information tab will be activated, showing you the response text and/or the selected label of the receiver of the request.
    </p>
    <p>
        You can delete an assistance request at any time. Note that the email is still sent but the link to respond to the assistance request will no longer work.
    </p>
    <p>
        If you suggested one or more labels to the receiver of the assistance request and they chose one of them, the <button class="btn btn-success btn-xs">Attach this label</button> button is displayed. Use this button to conveniently attach the chosen label to the annotation. If you already attached the label or the label has been deleted in the meantime, the button will not be shown.
    </p>
    <p>
        If the receiver of the assistance request only responded with text, use the <button class="btn btn-default btn-xs">View in annotation tool</button> button to open the annotation tool. You can then modify the labels of the annotation there.
    </p>
    <div class="panel panel-info">
        <div class="panel-body text-info">
            Go ahead and send an annotation assistance request to yourself, to see the whole process in action.
        </div>
    </div>
</div>
@endsection
