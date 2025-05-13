<?php

namespace TwitchAnalytics\Controllers\Token;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\TokenService;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\EmailException;

class TokenController extends BaseController
{
    private TokenValidator $validator;
    private TokenService $tokenService;
    public function __construct(TokenValidator $validator, TokenService $tokenService)
    {
        $this->validator = $validator;
        $this->tokenService = $tokenService;
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validateEmail($request->get('email'));
            $key = $this->validator->validateKey($request->get('api_key'));
            return response()->json($this->tokenService->generateToken($email, $key));
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (ApiKeyException $ex) {
            return response()->json(['error' => $ex->getMessage()], 401);
        } catch (EmailException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
