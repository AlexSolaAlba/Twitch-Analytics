<?php

namespace TwitchAnalytics\Application\Services;

use Random\RandomException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Key\RandomKeyGenerator;
use TwitchAnalytics\Domain\Models\TwitchUser;
use TwitchAnalytics\Domain\Repositories\TwitchUserRepository\TwitchUserRepositoryInterface;
use TwitchAnalytics\Infraestructure\DB\DBException;

class RefreshTwitchTokenService
{
    private TwitchUserRepositoryInterface $twitchUserRepository;
    public function __construct(TwitchUserRepositoryInterface $twitchUserRepository)
    {
        $this->twitchUserRepository = $twitchUserRepository;
    }
    public function refreshTwitchToken(string $token): TwitchUser
    {
        /*$this->userRepository->verifyUserToken($token);*/
        try {
            $twitchUser = $this->twitchUserRepository->getTwitchUser();
            if (!$twitchUser || time() >= $twitchUser->getTokenExpire()) {
                /*return ['accessToken' => $newToken, 'clientId' => $twitchUser->getClientID()];*/
                return $this->getAccessToken();
            }
            return $twitchUser;
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
}
