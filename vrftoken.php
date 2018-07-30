<?php
chdir(dirname(__DIR__));

require_once('vendor/autoload.php');

use Zend\Http\PhpEnvironment\Request;
use Firebase\JWT\JWT;
use app\TApp;

/*
* Get all headers from the HTTP request
*/
$request = new Request();

if ($request->isOptions()) {
    $this->header = header('HTTP/1.0 200 OK');
    return;
}

try {
    if ($request->isGet()) {
        $hasHeader = hasHeader($request);
        if ($hasHeader) {
            $jwt = hasJWT($hasHeader);
            if ($jwt) {                
                try {
                    $token = getToken($jwt);
                    header('Content-type: application/json');
                    echo json_encode([
                    'JWT'   => $token
                    ]);
                } catch (Exception $e) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo($e->Message);
                }
            } 
        } else {
            header('HTTP/1.0 200 OK');
            echo('Token not found in request');
        }
    } else {
        header('HTTP/1.0 405 Method Not Allowed');
    }
  
} catch (Exception $e){
  if ($e->Message = 'Expired token'){
    header('HTTP/1.0 401 Unauthorized');
  }
}

function getToken($jwt){

    $app = TApp::open();

    $secretKey = base64_decode($app->JWT->key);

    $token = JWT::decode($jwt, $secretKey, [$app->JWT->algorithm]);
    
    return $token;
}

function hasHeader($request){
    return $request->getHeader('authorization');
}

function hasJWT($authHeader){
    list($jwt) = sscanf( $authHeader->toString(), 'Authorization: Bearer %s');
    return $jwt;    
}
?>