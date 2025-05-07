<?php

namespace TwitchAnalytics\Controllers\Register;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RegisterService;

class RegisterController extends BaseController
{
    private RegisterService $registerService;
    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Internal server error'], 500);
        }

        if (!$request->has('email')) {
            return response()->json(['error' => 'The email is mandatory'], 400);
        }

        $email = $request->get('email');

        if (!$this->comprobarEmail($email)) {
            return response()->json(['error' => 'The email must be a valid email address'], 400);
        }

        return response()->json(json_decode($this->registerService->register($email), true));
    }


    private function comprobarEmail($email): false|int
    {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
    }
}


