<?php

namespace TwitchAnalytics\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepositoryInterface;
use TwitchAnalytics\Infraestructure\DB\DataBaseHandler;

class UserController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    private DataBaseHandler $dataBaseHandler;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository,
        DataBaseHandler $dataBaseHandler
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
        $this->dataBaseHandler = $dataBaseHandler;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->refreshTwitchToken->refreshTwitchToken();
            $this->userValidator->validateUserId($request->get('id'));
            $this->userValidator->validateToken($request->header('Authorization'));
            $user = $this->userRepository->verifyUserToken($request->header('Authorization'));

            return response()->json($this->returnStreamerInfo($request->get('id'), env('CLIENT_ID'), $user->getToken()));
        } catch (ApiKeyException $ex) {
            return response()->json(['error' => $ex->getMessage()], 401);
        } catch (ValidationException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        } catch (\Throwable $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function returnStreamerInfo($userId, $clientID, $accessToken): array
    {
        $connection = $this->dataBaseHandler->connectWithDB();
        $this->dataBaseHandler->checkConnection($connection);

        $stmt = $connection->prepare("SELECT * FROM usersTwitch where id = ?");
        $stmt->bind_param("i", $userId);
        $this->dataBaseHandler->checkStmtExecution($stmt);
        $streamerRaw = $stmt->get_result();

        if ($streamerRaw->num_rows > 0) {
            $streamer = $streamerRaw->fetch_assoc();
            return [
                "id" => $streamer["id"],
                "login" => $streamer["user_login"],
                "display_name" => $streamer["display_name"],
                "type" => $streamer["user_type"],
                "broadcaster_type" => $streamer["broadcaster_type"],
                "description" => $streamer["user_description"],
                "profile_image_url" => $streamer["profile_image_url"],
                "offline_image_url" => $streamer["offline_image_url"],
                "view_count" => $streamer["view_count"],
                "created_at" => $streamer["created_at"]
            ];
        } else {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, "https://api.twitch.tv/helix/users?id=$userId");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Client-ID: $clientID",
                "Authorization: Bearer $accessToken"
            ]);
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $responseData = json_decode($response, true);


            switch ($httpCode) {
                case 200:
                    if (isset($responseData['data'][0])) {
                        $this->insertIntoDB($responseData['data'][0]);
                        echo $response;
                    } else {
                        http_response_code(404);
                        $errorMessage = [
                            'error' => 'User not found.'
                        ];
                        echo json_encode($errorMessage);
                    }
                    break;
                case 400:
                    http_response_code(400);
                    $errorMessage = [
                        'error' => 'Invalid or missing id parameter.'
                    ];
                    echo json_encode($errorMessage);
                    break;
                case 401:
                    http_response_code(401);
                    $errorMessage = [
                        'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
                    ];
                    echo json_encode($errorMessage);
                    break;
                case 404:
                    http_response_code(404);
                    $errorMessage = [
                        'error' => 'User not found.'
                    ];
                    echo json_encode($errorMessage);
                    break;
                case 500:
                    http_response_code(500);
                    $errorMessage = [
                        'error' => 'Internal server error.'
                    ];
                    echo json_encode($errorMessage);
                    break;
            }
        }
        $connection->close();
    }

    private function insertIntoDB($user)
    {
        $conection = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conection = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if (!$conection) {
            http_response_code(500);
            $errorMessage = [
                'error' => 'Internal server error. Please try again later.'
            ];
            echo json_encode($errorMessage);
            exit();
        }

        $query = $conection->prepare(
            "INSERT INTO usersTwitch(id, user_login, display_name, user_type, broadcaster_type,
        user_description, profile_image_url, offline_image_url, view_count, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)"
        );
        $query->bind_param(
            "ssssssssss",
            $user["id"],
            $user["login"],
            $user["display_name"],
            $user["type"],
            $user["broadcaster_type"],
            $user["description"],
            $user["profile_image_url"],
            $user["offline_image_url"],
            $user["view_count"],
            $user["created_at"]
        );
        if (!$query->execute()) {
            $query->close();
            $conection->close();
            http_response_code(400);
            die("Error en la consulta: " . mysqli_error($conection));
        }
        $query->close();
        $conection->close();
    }
}
