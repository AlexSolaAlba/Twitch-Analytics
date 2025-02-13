<?php
    header("Content-Type: application/json");
    $metodo = $_SERVER['REQUEST_METHOD'];
    if(strcmp($metodo, 'POST') === 0 ){
        if(isset($_POST['email'])){
            if(isset($_POST['api_key'])){
                $email = $_POST['email'];
                $key = $_POST['api_key'];
                if(comprobarEmail($email)){
                    if(comprobarApiKey($email,$key)){
                        $token = bin2hex(random_bytes(16));
                        if(guardarEnBBDD($email,$key,$token)){
                            $message = [
                                'token' => $token
                            ];
                            echo json_encode($message);
                        }else{
                            http_response_code(500);
                            $error_message = [
                                'error' => 'Internal server error'
                            ];
                            echo json_encode($error_message);
                        }
                    }else{
                        http_response_code(401);
                        $error_message = [
                            'error' => 'Unauthorized. API access token is invalid."'
                        ];
                        echo json_encode($error_message);
                    }
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
                'error' => 'The api_key is mandatory'
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
        if(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",$email)){
            $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
            #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        
            if (!$conexion) {
                return false;
            }
        
            $consulta = $conexion->prepare("SELECT userID FROM User WHERE userEmail = ?");
            $consulta->bind_param("s", $email);      
            if(!$consulta->execute()){
                return false;
            }
            $resultado = $consulta->get_result();
            $datos = $resultado->fetch_assoc();
            $consulta->close();
            if(isset($datos['userID'])){
                return true;
            }else{
                return false;
            }
            
        }

        return false;
    }

    function comprobarApiKey($email,$key){
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    
        if (!$conexion) {
            return false;
        }
    
        $consulta = $conexion->prepare("SELECT userApiKey FROM User WHERE userEmail = ?");
        $consulta->bind_param("s", $email);      
        if(!$consulta->execute()){
            return false;
        }
        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        if($key == $datos['userApiKey']){
            return true;
        }else{
            return false;
        }
        $consulta->close();
    }

    function guardarEnBBDD($email, $key,$token) {
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
    
        if (!$conexion) {
            return false;
        }
    
        $consulta = $conexion->prepare("SELECT userID FROM User WHERE userEmail = ?");
        $consulta->bind_param("s", $email);      
        if(!$consulta->execute()){
            return false;
        }
        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        $consulta->close();
    
        $id = $datos['userID'];
        $expiration = time() + 259200;
        $stmt = $conexion->prepare("UPDATE User SET userToken = ? , userTokenExpire = ?  WHERE userID = ?");
        $stmt->bind_param("sdi", $token, $expiration, $id);

    
        if(!$stmt->execute()){
            return false;
        }
    
        $stmt->close();
        $conexion->close();
        return true;
    }
    
?>