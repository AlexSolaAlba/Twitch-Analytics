<?php

namespace TwitchAnalytics\Controllers\Register;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RegisterService;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Controllers\Validator\Validator;

class RegisterController extends BaseController
{
    private RegisterService $registerService;
    private Validator $validator;
    public function __construct(RegisterService $registerService, Validator $validator)
    {
        $this->registerService = $registerService;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->get('email'));
            return response()->json($this->registerService->register($email));
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
