<?php
$router->group([
    'namespace' => 'Api',
    'prefix' => 'api/v1',
    'middleware' => 'auth:web,api',
], function ($router) {
    $router->resource('annotation-assistance-requests', 'AnnotationAssistanceRequestController', [
        'only' => ['store', 'update', 'destroy'],
    ]);
});
