<?php
include_once '../api/config/database.php';
include_once '../api/objects/usuario.php';
include_once '../vendor/autoload.php';
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);

if($_SERVER['REQUEST_METHOD']=='POST')
{
    $data = json_decode(file_get_contents("php://input"));

    $usuario->correo = $data->correo;

    $exist_correo = $usuario->correo_existe();

    if($exist_correo && password_verify($data->contrasena, $usuario->contrasena))
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "THE_ISSUER"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 120; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $usuario->id,
                "nombre" => $usuario->nombre,
                "apellido" => $usuario->apellido,
                "correo" => $usuario->correo
            )
            );

            http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "email" => $usuario->correo,
                "expireAt" => $expire_claim
            ));
    }

}