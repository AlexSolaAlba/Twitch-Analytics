<?php

namespace TwitchAnalytics\Tests\Unit\Service;

use Laravel\Lumen\Testing\TestCase;
use Mockery;
use Random\RandomException;
use TwitchAnalytics\Application\Services\EnrichedService;
use TwitchAnalytics\Domain\Repositories\EnrichedRepositoryInterface;
use TwitchAnalytics\Domain\Models\EnrichedStream;

class EnrichedServiceTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../../bootstrap/app.php';
    }
    /**
     * @throws RandomException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testReturnEnrichedStreamsInfo()
    {
        $repositoryMock = Mockery::mock(EnrichedRepositoryInterface::class);
        $streamMock = Mockery::mock(EnrichedStream::class);
        $streamMock->shouldReceive('getStreamerId')->andReturn('123');
        $streamMock->shouldReceive('getUserId')->andReturn('456');
        $streamMock->shouldReceive('getUserName')->andReturn('streamer_name');
        $streamMock->shouldReceive('getViewerCount')->andReturn('789');
        $streamMock->shouldReceive('getUserDisplayName')->andReturn('StreamerDisplayName');
        $streamMock->shouldReceive('getTitle')->andReturn('Live Stream Title');
        $streamMock->shouldReceive('getProfileImageUrl')->andReturn('http://image.url');

        $repositoryMock
            ->shouldReceive('returnEnrichedStreamInfoFromAPI')
            ->once()
            ->with(1, 'valid_token')
            ->andReturn([$streamMock]);

        $service = new EnrichedService($repositoryMock);
        $result = $service->returnEnrichedStreamsInfo(1, 'valid_token');
        $this->assertEquals([
            [
                'streamer_id' => '123',
                'user_id' => '456',
                'user_name' => 'streamer_name',
                'viewer_count' => '789',
                'user_display_name' => 'StreamerDisplayName',
                'title' => 'Live Stream Title',
                'profile_image_url' => 'http://image.url',
            ]
        ], $result);
    }
}
