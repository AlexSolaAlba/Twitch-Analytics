<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\EmailException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Repositories\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\Exceptions\DBException;

class TokenService
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
    public function generateToken(string $email, string $key): array
    {
        try {
            $user = $this->userRepository->checkUserExists($email, $key);
            $token = $this->keyGenerator->generateRandomKey();
            $this->userRepository->assignTokenToUser($user, $token);
            return [
                'token' => $user->getToken()
            ];
        } catch (RandomException) {
            throw new RandomException('Internal server error');
        } catch (ApiKeyException $e) {
            throw new ApiKeyException($e->getMessage());
        } catch (EmailException $e) {
            throw new EmailException($e->getMessage());
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        }
    }
}
