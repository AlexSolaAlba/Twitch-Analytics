<?php

namespace TwitchAnalytics\Tests\Integration\Controller;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\TestCase;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\Enriched\EnrichedController;
use TwitchAnalytics\Controllers\Enriched\EnrichedValidator;
use TwitchAnalytics\Controllers\User\UserValidator;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchEnriched\FakeApiTwitchEnriched;
use TwitchAnalytics\Infraestructure\ApiClient\ApiTwitchToken\FakeApiTwitchToken;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\Repositories\TwitchUserRepository;
use TwitchAnalytics\Infraestructure\Repositories\UserRepository;
use TwitchAnalytics\Infraestructure\Time\SystemTimeProvider;

class EnrichedControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    private EnrichedController $enrichedController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataBaseHandler = new DataBaseHandler();
        $fakeApiTwitchClient = new FakeApiTwitchToken();
        $twitchUserRepository = new TwitchUserRepository($this->dataBaseHandler, $fakeApiTwitchClient);
        $timeProvider = new SystemTimeProvider();
        $refreshTwitchToken = new RefreshTwitchTokenService($twitchUserRepository, $timeProvider);
        $userValidator = new UserValidator();
        $enrichedValidator = new EnrichedValidator();
        $userRepository = new UserRepository($this->dataBaseHandler);
        $apiStreams = new FakeApiTwitchEnriched();
        $this->enrichedController = new EnrichedController($refreshTwitchToken, $userValidator, $enrichedValidator, $userRepository, $apiStreams);
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongTokenReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bear',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }

    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function notGivenTokenReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => '',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenWrongLimitReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 'a',
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Invalid or missing limit parameter.'
        ], $response->getData(true));
    }
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function givenTokenThatNotExistsReturnsAnException()
    {
        $request = Request::create('/streams/enriched', 'GET', [
            'limit' => 2,
        ], [], [], [
            'HTTP_Authorization' => 'Bearer ',
        ]);

        $response = $this->enrichedController->__invoke($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Unauthorized. Token is invalid or expired.'
        ], $response->getData(true));
    }
}
