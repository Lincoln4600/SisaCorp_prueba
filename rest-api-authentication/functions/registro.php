<?php


header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../api/config/database.php';
include_once '../api/objects/usuario.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);


if($_SERVER['REQUEST_METHOD']=='POST')
{

    $data = json_decode(file_get_contents("php://input"));

    $usuario->nombre = $data->nombre;
    $usuario->apellido = $data->apellido;
    $usuario->correo = $data->correo;
    $usuario->contrasena = $data->contrasena; 

if(
    !empty($usuario->nombre) &&
    !empty($usuario->correo) &&
    !empty($usuario->contrasena) &&
    $usuario->create()
){
 
    http_response_code(200);

    echo json_encode(array("mensaje" => "Usuario creado."));
}

else{
 
    http_response_code(400);

    echo json_encode(array("mensaje" => "Error: No se ha podido crear el usuario."));
}


}

?>