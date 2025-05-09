<?php

namespace TwitchAnalytics\Controllers\Register;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\ValidationException;

class RegisterController extends BaseController
{
    private RegisterService $registerService;
    private RegisterValidator $validator;
    public function __construct(RegisterService $registerService, RegisterValidator $validator)
    {
        $this->registerService = $registerService;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
        try {
            $email = $this->validator->validate($request->get('email'));
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }

        return response()->json($this->registerService->register($email));
    }
}
