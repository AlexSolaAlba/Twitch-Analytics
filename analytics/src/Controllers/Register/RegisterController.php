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

    public function __invoke(Request $request): false|string
    {
        if (!$request->isMethod('post')) {
            http_response_code(500);
            $error_message = [
                'error' => 'Internal server error'
            ];
            echo json_encode($error_message);
        }
        if (!$request->has('email')) {
            http_response_code(400);
            $error_message = [
                'error' => 'The email is mandatory'
            ];
            echo json_encode($error_message);
        }

        $email = $request->get('email');
        if (!comprobarEmail($email)) {
            http_response_code(400);
            $error_message = [
                'error' => 'The email must be a valid email address'
            ];
            echo json_encode($error_message);
        }
        return $this->registerService->register($email);
    }
}

function comprobarEmail($email): false|int
{
    return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
}
