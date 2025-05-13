<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\DB\DBException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepository;

class RegisterService
{
    private RandomKeyGenerator $keyGenerator;
    private UserRepository $userRepository;
    public function __construct(RandomKeyGenerator $keyGenerator, UserRepository $userRepository)
    {
        $this->keyGenerator = $keyGenerator;
        $this->userRepository = $userRepository;
    }


    /**
     * @throws RandomException
     */
    public function register($email): array
    {
        try {
            $key = $this->keyGenerator->generateRandomKey();
            $user = $this->userRepository->registerUser($email, $key);
            return [
                'api_key' => $user->getApiKey()
            ];
        } catch (RandomException) {
            throw new RandomException('Internal server error');
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        }
    }
}
