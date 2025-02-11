<?php
    header("Content-Type: application/json");
    $metodo = $_SERVER['REQUEST_METHOD'];
    if(strcmp($metodo, 'POST') === 0 ){
        if(isset($_POST['email'])){
            $email = $_POST['email'];
            if(comprobarEmail($email)){
                $key = bin2hex(random_bytes(16));
                #guardarEnBaseDeDatos($email,$key);
                $message = [
                    'api_key' => $key
                ];
                echo json_encode($message);
            }else{
                http_response_code(400);
                $error_message = [
                    'error' => 'The email must be a valid email address'
                ];
                echo json_encode($error_message);
            } 
        }else{
            http_response_code(400);
            $error_message = [
                'error' => 'The email is mandatory'
            ];
            echo json_encode($error_message);
        }
    }else{
        http_response_code(500);
        $error_message = [
            'error' => 'Internal server error'
        ];
        echo json_encode($error_message);
    }

    function comprobarEmail($email){
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",$email);
    }
?>