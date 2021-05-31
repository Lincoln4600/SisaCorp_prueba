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

    $key = "YOUR_SECRET_KEY";
    $data = json_decode(file_get_contents("php://input"));

    $jwt=isset($data->jwt) ? $data->jwt : "";
    $usuario->nombre = $data->nombre;


    if($jwt){
        try {
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            http_response_code(200);

            $usuario->getInfoUsuario();

            echo "Accesso concedido";

            echo json_encode(array(
                "data" => $decoded->data
            ));
    
        }
        catch (Exception $e){
            http_response_code(401);
            echo json_encode(array(
                "message" => "Acceso denegado",
                "error" => $e->getMessage()
            ));
        }

    }

}