<?php

$router->group([
    'namespace' => 'Api',
    'prefix' => 'api/v1',
    'middleware' => 'auth:web,api',
], function ($router) {
    $router->resource('annotation-assistance-requests', 'AnnotationAssistanceRequestController', [
        'only' => ['store', 'destroy'],
    ]);
});

// These endpoints are public and protected by a token only known to the receiver of the
// assistance request.
$router->put('api/v1/annotation-assistance-requests/{token}', 'Api\AnnotationAssistanceRequestController@update');
$router->get('api/v1/annotation-assistance-requests/{token}/image', 'Api\ImageController@show');

$router->get('annotation-assistance-requests/respond/{token}', [
    'as'   => 'respond-assistance-request',
    'uses' => 'Views\AnnotationAssistanceRequestController@respond',
]);

$router->group([
    'namespace' => 'Views',
    'middleware' => 'auth',
], function ($router) {
    $router->get('annotation-assistance-requests/create', [
        'as'   => 'create-assistance-request',
        'uses' => 'AnnotationAssistanceRequestController@create',
    ]);

    $router->get('annotation-assistance-requests/{id}', [
        'as'   => 'show-assistance-request',
        'uses' => 'AnnotationAssistanceRequestController@show',
    ]);

    $router->get('annotation-assistance-requests', [
        'as'   => 'index-assistance-requests',
        'uses' => 'AnnotationAssistanceRequestController@index',
    ]);
});
