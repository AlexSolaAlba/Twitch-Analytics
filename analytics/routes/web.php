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

use TwitchAnalytics\Controllers\Enriched\EnrichedController;
use TwitchAnalytics\Controllers\Streams\StreamsController;
use TwitchAnalytics\Controllers\User\UserController;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get("/streams", [
    'uses' => StreamsController::class,
]);

$router->get("/user", [
    'uses' => UserController::class,
]);

$router->get("/streams/enriched", [
    'uses' => EnrichedController::class,
]);

$router->get("/topsofthetops", [
    'uses' => TopsOfTheTopsController::class,
]);
