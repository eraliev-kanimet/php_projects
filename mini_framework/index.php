<?php

require_once 'App.php';

$app = new App();

$app->get('', function (App $app) {
    $app->response(['data' => 'Hello world!!!']);
});

$app->run();