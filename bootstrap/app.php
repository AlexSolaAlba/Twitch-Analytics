<?php

use TwitchAnalytics\Controllers\Register\RegisterController;
use TwitchAnalytics\Controllers\Token\TokenController;

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
//$app->withFacades();

//$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/
$app->singleton(
    TwitchAnalytics\Domain\Repositories\UserRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\UserRepository::class
);

$app->singleton(
    TwitchAnalytics\Domain\Repositories\TwitchUserRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository::class
);

$app->singleton(
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\ApiTwitchTokenInterface::class,
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\ApiTwitchToken::class
);

$app->singleton(
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\ApiTwitchStreamsInterface::class,
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreams\FakeApiTwitchStreams::class
);

$app->singleton(
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\ApiTwitchVideosInterface::class,
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchVideos\FakeApiTwitchVideos::class
);

$app->singleton(
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\ApiTwitchEnrichedInterface::class,
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\FakeApiTwitchEnriched::class
);

$app->singleton(
    TwitchAnalytics\Domain\Time\TimeProviderInterface::class,
    TwitchAnalytics\Infraestructure\Time\SystemTimeProvider::class
);

$app->singleton(
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreamer\ApiTwitchStreamerInterface::class,
    TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchStreamer\FakeApiTwitchStreamer::class
);

$app->singleton(
    TwitchAnalytics\Domain\Repositories\StreamerRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\StreamerRepository::class
);

$app->singleton(
    TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\StreamsRepository::class
);

$app->singleton(
    TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\EnrichedRepository::class
);

$app->singleton(
    TwitchAnalytics\Domain\Repositories\TopsOfTheTopsRepositoryInterface::class,
    TwitchAnalytics\Infraestructure\Repositories\TopsOfTheTopsRepository::class
);

$app->singleton(
    TwitchAnalytics\Application\Services\RegisterService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\RegisterService(
            $app->make(TwitchAnalytics\Domain\Key\RandomKeyGenerator::class),
            $app->make(TwitchAnalytics\Domain\Repositories\UserRepositoryInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\TokenService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\TokenService(
            $app->make(TwitchAnalytics\Domain\Key\RandomKeyGenerator::class),
            $app->make(TwitchAnalytics\Domain\Repositories\UserRepositoryInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\RefreshTwitchTokenService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\RefreshTwitchTokenService(
            $app->make(TwitchAnalytics\Domain\Repositories\TwitchUserRepositoryInterface::class),
            $app->make(TwitchAnalytics\Domain\Time\TimeProviderInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\UserService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\UserService(
            $app->make(TwitchAnalytics\Domain\Repositories\StreamerRepositoryInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\StreamsService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\StreamsService(
            $app->make(TwitchAnalytics\Domain\Repositories\StreamsRepositoryInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\EnrichedService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\EnrichedService(
            $app->make(TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface::class)
        );
    }
);

$app->singleton(
    TwitchAnalytics\Application\Services\TopsOfTheTopsService::class,
    function ($app) {
        return new TwitchAnalytics\Application\Services\TopsOfTheTopsService(
            $app->make(TwitchAnalytics\Domain\Repositories\TopsOfTheTopsRepositoryInterface::class)
        );
    }
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Laravel\Lumen\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'prefix' => '',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
