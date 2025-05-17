<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use TwitchAnalytics\Controllers\User\UserController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('streams', function () {
    require __DIR__ . '/../src/streams.php';
});

$router->get("/user", [
    'uses' => UserController::class,
]);


$router->get('/streams/enriched', function () {
    require __DIR__ . '/../src/enriched.php';
});

$router->get('topsofthetops', function () {
    require __DIR__ . '/../src/topsofthetops.php';
});
