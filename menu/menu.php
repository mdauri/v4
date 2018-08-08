<?php
chdir(dirname(__DIR__));

require_once('vendor/autoload.php');

use Zend\Http\PhpEnvironment\Request;
use Firebase\JWT\JWT;
use app\TApp;

use ado\core\TTransaction;
use ado\core\TLoggerTXT;

use ado\model\TUsersRecord;
use ado\service\TSecFunctionalityService;
use ado\service\TSecFunctionalityAccessLevelService;

/*
 * Get all headers from the HTTP request
 */
$request = new Request();

if ($request->isOptions()) {
  //$this->header = header('HTTP/1.0 200 OK');
  header('HTTP/1.0 200 OK');
  return;
}

$authHeader = $request->getHeader('authorization');
/*
  * Look for the 'authorization' header
  */
if ($authHeader) {
    /*
      * Extract the jwt from the Bearer
      */
    list($jwt) = sscanf( $authHeader->toString(), 'Authorization: Bearer %s');

    if ($jwt) {
      try {
        $app = TApp::open();
        $secretKey = base64_decode($app->JWT->key);
        $algorithm = $app->JWT->algorithm;
        $token = JWT::decode($jwt, $secretKey, [$algorithm]);
        if ($request->isGet()) {

          try
          {
            //obtendo o valor após a ultima virgula da url
            $uri = $_SERVER["REQUEST_URI"];           
            
            $uriArray = explode('/', $uri);

            if ($uriArray[count($uriArray)-2] == 'menu'){
              $uriArray2 = explode('&', $uriArray[count($uriArray)-1]);

              if(isset($uriArray2[0])){
                $uriArray3 = explode('=', $uriArray2[0]);
                if ($uriArray3[0]=='UsersID'){$UsersID = $uriArray3[1];}
              }
             
              if(!isset($UsersID) OR strlen($UsersID) <= 0){$UsersID = -1;}
              
            }

            if(isset($UsersID) AND $UsersID > -1)
            {      
              //inicia transação com o banco PosControlConfig
              TTransaction::opendb('poscontrolconfig');
              //inicia transação com o banco PosControl
              TTransaction::opendb('poscontrol');
              //define o arquivo de LOG
              //TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrol.txt'), 'poscontrol');
              //TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrolconfig.txt'),'poscontrolconfig');

              $MENU = get_menu($UsersID);

              TTransaction::closedb('poscontrolconfig');
              TTransaction::closedb('poscontrol');
            } 
            /*
            * return protected asset
            */
            header('Content-type: application/json');
            echo json_encode([        
              'menu'    => $MENU
            ]);
 
          }
          catch(Exception $e)
          {
               
            echo 'Erro ' . $e->getMessage();
            TTransaction::rollbackdb('poscontrol');
            TTransaction::rollbackdb('poscontrolconfig');
          }

        } else {
            header('HTTP/1.0 405 Method Not Allowed');
        }
      } catch (Exception $e) {
        header('HTTP/1.0 401 Token Expired');
      }        
    } else {
        /*
          * No token was able to be extracted from the authorization header
          */
        header('HTTP/1.0 400 Bad Request');
    }
} else {
    /*
      * The request lacks the authorization token
      */
    header('HTTP/1.0 400 Bad Request');
    echo 'Token not found in request';
}

function get_menu($UsersID){

  //obtem Usuario
  $user = new ado\model\UsersRecord('poscontrolconfig','UsersID', $UsersID);
  
  //obtem todas as funcionalidades
  $secfunctionalityservice = new TSecFunctionalityService;
  $menus = $secfunctionalityservice->getMenuSecFunctionalities();
  
  $secfunctionalityaccesslevelservice = new TSecFunctionalityAccessLevelService;
  //obtem todas as funcionalidades por AccessLevel
  $secfunctionalitiesbyaccesslevel = $secfunctionalityaccesslevelservice->SecFunctionalitiesbyAccessLevel($user->AccessLevelID);

  // Chamada inicial da função
  $menuFinal = [];
  construirMenu($menus, $menuFinal, null, 0, $secfunctionalitiesbyaccesslevel);

  return $menuFinal;
}

function construirMenu(array $menus, array &$menuFinal, $menuSuperiorId, $nivel = 0, $secfunctionalitiesbyaccesslevel)
{

    // Passando por todos os menus
    foreach ($menus as $menu) {

        $menu['SecFunctionalityURL'] = trim($menu['SecFunctionalityURL']);

        // Se for um menu filho do menu superior que estamos procurando
        if ($menu['SecParentFunctionalityId'] == $menuSuperiorId AND IsAuth($menu['SecFunctionalityId'],$secfunctionalitiesbyaccesslevel)) {
            // Armazenando no menu final
            $menuFinal[] = $menu;
        }
    }
 
    // Incrementando nível
    $nivel++;
 
    // Passando pelos menus desse nível
    for ($i = 0; $i < count($menuFinal); $i++) {
 
        // Inicializando indices
        $menuFinal[$i]['sub_menus'] = [];
        $menuFinal[$i]['nivel'] = $nivel;
 
        // Chamando a função novamente para construção dos submenus
        construirMenu($menus, $menuFinal[$i]['sub_menus'], $menuFinal[$i]['SecFunctionalityId'], $nivel, $secfunctionalitiesbyaccesslevel);
    }
 
}

function IsAuth($SecFunctionalityId, $secfunctionalitiesbyaccesslevel){

  foreach ($secfunctionalitiesbyaccesslevel as $record ) {
    if ($record->SecFunctionalityId==$SecFunctionalityId) {
      return true;
    }
  }
  return false;
  
}
?>