<?php

namespace TwitchAnalytics\Controllers\Response;

class JsonResponse
{
    public function __construct(array $message, ?int $status = 200)
    {
        $this->response = response()->json($message, $status);
    }
}
