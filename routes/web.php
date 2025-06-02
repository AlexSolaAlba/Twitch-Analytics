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
use TwitchAnalytics\Controllers\Register\RegisterController;
use TwitchAnalytics\Controllers\Streams\StreamsController;
use TwitchAnalytics\Controllers\Token\TokenController;
use TwitchAnalytics\Controllers\User\UserController;
use TwitchAnalytics\Controllers\TopsOfTheTops\TopsOfTheTopsController;

$router->post("/register", [
    'uses' => RegisterController::class,
]);

$router->post("/token", [
    'uses' => TokenController::class,
]);

$router->get("/analytics/streams", [
    'uses' => StreamsController::class,
]);

$router->get("/analytics/user", [
    'uses' => UserController::class,
]);

$router->get("/analytics/streams/enriched", [
    'uses' => EnrichedController::class,
]);

$router->get("/analytics/topsofthetops", [
    'uses' => TopsOfTheTopsController::class,
]);
