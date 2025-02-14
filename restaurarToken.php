<?php
    function getAccessToken(){
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if (!$conexion) {
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }
        $consulta = $conexion->prepare("SELECT clientId, clientSecret FROM Token WHERE tokenID = 1");     
        if(!$consulta->execute()){
            $consulta->close();
            $conexion->close();
            die("Error en la consulta: " . mysqli_error($conexion));
        }
        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        $clientId = $datos['clientId'];
        $clientSecret = $datos['clientSecret'];
        $consulta->close();
        $conexion->close();

        $postFields = "client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials";
        $ch = curl_init('https://id.twitch.tv/oauth2/token');
        curl_setopt($ch,CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($response,true);
    
        if(isset($data['access_token'])) {
            saveTokenToDB($data['access_token'], $data['expires_in']);
            return $data['access_token'];
        } else {
            die("Error obteniendo el token: " . $response);
        }
    }

    function saveTokenToDB($accessToken,$expiresIn){
        $expiresAt = time() + $expiresIn;
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if(!$conexion){
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }
        $consulta = $conexion->prepare("UPDATE Token set accessToken = ?, tokenExpire = ? WHERE tokenID = 1");
        $consulta->bind_param("si", $accessToken,$expiresAt); 
        if(!$consulta->execute()){
            $consulta->close();
            $conexion->close();
            die("Error en la consulta: " . mysqli_error($conexion));
        }
        $consulta->close();
        $conexion->close();
    }

    function getTokenFromDB(){
        $conexion = mysqli_connect("db5017192767.hosting-data.io", "dbu2466002", "s9saGODU^mg2SU", "dbs13808365");
        #$conexion = mysqli_connect("localhost", "root", "", "twitch-analytics");
        if(!$conexion){
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }
        $consulta = $conexion->prepare("SELECT accessToken, tokenExpire, clientId FROM Token WHERE tokenID =?");
        $id = 1;
        $consulta->bind_param("i", $id);
        if(!$consulta->execute()){
            $consulta->close();
            $conexion->close();
            die("Error en la consulta: " . mysqli_error($conexion));
        }
        $resultado = $consulta->get_result();
        $datos = $resultado->fetch_assoc();
        $accessToken = $datos['accessToken'];
        $tokenExpire = $datos['tokenExpire'];
        $clientId = $datos['clientId'];
        $consulta->close();
        $conexion->close();
        return ['accessToken' => $accessToken, 'tokenExpire' => $tokenExpire, 'clientId' => $clientId];
    }
    
    function getValidToken() {
        $tokenData = getTokenFromDB();
        if(!$tokenData || time() >= $tokenData['tokenExpire']){
            $newToken = getAccessToken();
            return ['accessToken' => $newToken, 'clientId' => $tokenData['clientId']];
        }
        return ['accessToken' => $tokenData['accessToken'], 'clientId' => $tokenData['clientId']];
    }
?>