<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\DB\DataBaseHandler;
use TwitchAnalytics\Domain\DB\DBException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;

class RegisterService
{
    private RandomKeyGenerator $keyGenerator;
    private DataBaseHandler $databaseHandler;
    public function __construct(RandomKeyGenerator $keyGenerator, DataBaseHandler $databaseHandler)
    {
        $this->keyGenerator = $keyGenerator;
        $this->databaseHandler = $databaseHandler;
    }


    /**
     * @throws RandomException
     */
    public function register($email): array
    {
        try {
            $key = $this->keyGenerator->generateRandomKey();
            $this->databaseHandler->saveUserAndApiKeyInDB($email, $key);
            return [
                'api_key' => $key
            ];
        } catch (RandomException) {
            throw new RandomException('Internal server error');
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        }
    }
}
