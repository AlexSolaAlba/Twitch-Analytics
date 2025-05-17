<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

class RegisterService
{
    private RandomKeyGenerator $keyGenerator;
    private UserRepositoryInterface $userRepository;
    public function __construct(RandomKeyGenerator $keyGenerator, UserRepositoryInterface $userRepository)
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
