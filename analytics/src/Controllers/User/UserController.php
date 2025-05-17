<?php

namespace TwitchAnalytics\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use TwitchAnalytics\Application\Services\RefreshTwitchTokenService;
use TwitchAnalytics\Controllers\ValidationException;
use TwitchAnalytics\Domain\Exceptions\ApiKeyException;
use TwitchAnalytics\Domain\Repositories\UserRepository\UserRepositoryInterface;

class UserController extends BaseController
{
    private RefreshTwitchTokenService $refreshTwitchToken;
    private UserValidator $userValidator;
    private UserRepositoryInterface $userRepository;
    public function __construct(
        RefreshTwitchTokenService $refreshTwitchToken,
        UserValidator $userValidator,
        UserRepositoryInterface $userRepository
    ) {
        $this->refreshTwitchToken = $refreshTwitchToken;
        $this->userValidator = $userValidator;
        $this->userRepository = $userRepository;
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
        $conection = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conection = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if (!$conection) {
            return [
                'error' => 'Internal server error.'
            ];
        }
        $user = $conection->query("SELECT * FROM usersTwitch where id = $userId");
        if ($user == false) {
            http_response_code(400);
            die("Error en la consulta: " . mysqli_error($conection));
        }

        if ($user->num_rows > 0) {
            while ($row = $user->fetch_assoc()) {
                $data = [
                    "id" => $row["id"],
                    "login" => $row["user_login"],
                    "display_name" => $row["display_name"],
                    "type" => $row["user_type"],
                    "broadcaster_type" => $row["broadcaster_type"],
                    "description" => $row["user_description"],
                    "profile_image_url" => $row["profile_image_url"],
                    "offline_image_url" => $row["offline_image_url"],
                    "view_count" => $row["view_count"],
                    "created_at" => $row["created_at"]
                ];
            }
            echo json_encode($data);
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
        $conection->close();
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
