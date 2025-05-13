<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Exceptions\EmailException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;
use TwitchAnalytics\Infraestructure\DB\DBException;

class TokenService
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
    public function generateToken($email, $key): array
    {
        try {
            $this->databaseHandler->checkEmailExists($email);
            $this->databaseHandler->checkApiKeyExists($email, $key);
            $token = $this->keyGenerator->generateRandomKey();
            $this->databaseHandler->insertTokenIntoDB($email, $token);
            return [
                'token' => $token
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
