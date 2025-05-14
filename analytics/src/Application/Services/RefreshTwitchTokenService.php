<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepository\TwitchUserRepositoryInterface;
use TwitchAnalytics\Infraestructure\DB\DBException;

class RefreshTwitchTokenService
{
    private RandomKeyGenerator $keyGenerator;
    private TwitchUserRepositoryInterface $twitchUserRepository;
    public function __construct(RandomKeyGenerator $keyGenerator, TwitchUserRepositoryInterface $twitchUserRepository)
    {
        $this->twitchUserRepository = $twitchUserRepository;
        $this->keyGenerator = $keyGenerator;
    }
    public function refreshTwitchToken(string $token): void
    {
        /*$this->userRepository->verifyUserToken($token);*/
        try {
            $this->getValidToken();
        } catch (RandomException) {
            throw new RandomException('Internal server error');
        } catch (ApiKeyException $e) {
            throw new ApiKeyException($e->getMessage());
        } catch (DBException $e) {
            throw new DBException($e->getMessage());
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function getAccessToken()
    {
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if (!$conexion) {
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."]);
            exit();
        }
        $consulta = $conexion->prepare("SELECT clientId, clientSecret FROM token WHERE tokenID = 1");
        if (!$consulta->execute()) {
            $consulta->close();
            $conexion->close();
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."]);
            exit();
        }

        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        $clientId = $datos['clientId'];
        $clientSecret = $datos['clientSecret'];
        $consulta->close();
        $conexion->close();

        $postFields = "client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials";
        $curl = curl_init('https://id.twitch.tv/oauth2/token');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['access_token'])) {
            $this->saveTokenToDB($data['access_token'], $data['expires_in']);
            return $data['access_token'];
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."]);
            exit();
        }
    }

    private function saveTokenToDB($accessToken, $expiresIn): void
    {
        $expiresAt = time() + $expiresIn;
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if (!$conexion) {
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."]);
            exit();
        }
        $consulta = $conexion->prepare("UPDATE token set accessToken = ?, tokenExpire = ? WHERE tokenID = 1");
        $consulta->bind_param("si", $accessToken, $expiresAt);
        if (!$consulta->execute()) {
            $consulta->close();
            $conexion->close();
            http_response_code(500);
            echo json_encode(["error" => "Internal server error."]);
            exit();
        }
        $consulta->close();
        $conexion->close();
    }


    private function getValidToken()
    {
        $tokenData = $this->twitchUserRepository->getTwitchUserFromDB();
        if (!$tokenData || time() >= $tokenData['tokenExpire']) {
            $newToken = $this->getAccessToken();
            return ['accessToken' => $newToken, 'clientId' => $tokenData['clientId']];
        }
        return ['accessToken' => $tokenData['accessToken'], 'clientId' => $tokenData['clientId']];
    }
}
