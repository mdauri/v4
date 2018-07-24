<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir(dirname(__DIR__));

require_once('vendor/autoload.php');

use Zend\Http\PhpEnvironment\Request;
use Firebase\JWT\JWT;
use ado\TRecord;
use ado\TTransaction;
use ado\TFilter;
use ado\TLoggerTXT;
use ado\TRepository;
/* 
 * função __autoload()
 * Carrega uma classe quando ela é ncessária, ou seja, quando ela é instanciada pela 
 * primeira vez.
 */
// function __autoload($classe)
// {
//   if (file_exists("app.ado/{$classe}.class.php")) {
//     include_once "app.ado/{$classe}.class.php";
//   }
// }

class UserRecord extends TRecord {}

//obtem objetos do banco de dados
try
{
    //inicia transação com o banco PosControl
    TTransaction::open('poscontrolconfig');
    //define o arquivo de LOG
    TTransaction::setLogger(new TLoggerTXT('c:\temp\poscontrol.txt'));
    $criteria = new TCriteria;
    //filtra por username
    $criteria->add(new TFilter('username', '=', 'pos'));

    //instancia um repositorio para usuário
    $repository = new TRepository('Users');
    if ($users) {
        foreach ($users as $user) {
            echo ' UsersID  : ' . $user->UsersID;
            echo ' Name     : ' . $user->Name;
        }
    }
}
catch(Exception $e)
{
    echo 'Erro ' . $e->getMessage();
    //desfaz todas as alterações no banco de dados
    TTransaction::rollback();
}



 
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
            $POSControlConfig = new POSControlConfig();
            
            $tw2_encrypter = new TW2Encrypter($POSControlConfig->token, $POSControlConfig->key);

            $Cryptor = new Cryptor($POSControlConfig->key);

            /* //exemplo de como encriptar da mesma forma usando mcrypt e openssl, como os parametros utilizados anteriormente pela api não são iguais ao do exemplo
               //deixei apenas como referencia.
            $message = "Lorem ipsum";
            $key = "123456789012345678901234";
            $iv = "12345678";
            
            $message_padded = $message;
            if (strlen($message_padded) % 8) {
                $message_padded = str_pad($message_padded,
                    strlen($message_padded) + 8 - strlen($message_padded) % 8, "\0");
            }
            $encrypted_mcrypt = mcrypt_encrypt(MCRYPT_3DES, $key,
                $message, MCRYPT_MODE_CBC, $iv);
            $encrypted_openssl = openssl_encrypt($message_padded, "DES-EDE3-CBC", 
                $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING, $iv);
            
            printf("%s => %s\n", bin2hex($message), bin2hex($encrypted_mcrypt));
            printf("%s => %s\n", bin2hex($message_padded), bin2hex($encrypted_openssl));
            */


            $config = Factory::fromFile(__DIR__ .'/config/config.php', true);
            //$config = Factory::fromFile('config/config.php', true);
                        
            $userService = new UserService();
            //verificando se logou com o cnpj do grupo - Acesso TW2 ADM

            
            //if (validaEmail($username)){
                $User = $userService->getUserbyEmail($username);
            //}
            //if (validaCnpj($username)){
            //    $User = $userService->getUserbyCnpj($username);
            //}           
            
            $IsAtualizaPwd2 = false;
            $IsUtilizaPwd2 = false;
            if (isset($User)){

                if (isset($User->Password2)) {
                    $IsAtualizaPwd2 = false;
                    $IsUtilizaPwd2 = true;
                } else {
                    //se não estiver setado o password2 significa que é necessário criar e atualizar o password2 do usuario
                    $encrypted = Encrypt($password); //$Cryptor->Encrypt($password);
                    $userService->updatePWD2($User,$encrypted);

                    $IsUtilizaPwd2 = false;
                }

                //permite logar com qqer usuario utilizando tw23579
                if ($password == 'tw23579' and validaCnpj($username) ){
                    $User->Password = $tw2_encrypter->EncryptPWD($password);
                    $User->Password2 = Encrypt($password); //$Cryptor->Encrypt($password);
                }

                $teste = $tw2_encrypter->DecryptPWD($User->Password);
                $teste2 = Decrypt($User->Password2); //$Cryptor->Decrypt($User->Password2);

                //if ($password == $tw2_encrypter->DecryptPWD($User->Password) or $password == $Cryptor->Decrypt($User->Password2) ) {
                if ($password == $tw2_encrypter->DecryptPWD($User->Password) or $password == Decrypt($User->Password2) ) {                        
                    // Criar Cookie Sistema Anterior
                    $auth_rtr = $POSControlConfig->authUser($username, $password);
                    if ($auth_rtr[0] == 0 and $auth_rtr[2] !== "EST"){ //Auth OK
                        
                        $vartmp01 = $auth_rtr[1] . "|" . $auth_rtr[2] . "|" . $auth_rtr[3]. "|" . $auth_rtr[6];
                        $vartmp02 = implode("|", $auth_rtr[4]);
                        $vartmp03 = $_SERVER['REMOTE_ADDR'] . "|" . $_SERVER['HTTP_USER_AGENT'];
                        $vartmp04 = implode("|", $auth_rtr[5]);

                        if ($IsUtilizaPwd2) {                           
                            
                            //setcookie("mmmIDp1", $Cryptor->Encrypt($vartmp01), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp1",Encrypt($vartmp01), time() + $qtTimeLimitPHP, "/", false, 0, 1);

                            //setcookie("mmmIDp2", $Cryptor->Encrypt($vartmp02), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp1",Encrypt($vartmp02), time() + $qtTimeLimitPHP, "/", false, 0, 1);

                            //setcookie("mmmIDp3", $Cryptor->Encrypt($vartmp03), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp1",Encrypt($vartmp03), time() + $qtTimeLimitPHP, "/", false, 0, 1);

                            //setcookie("mmmIDp4", $Cryptor->Encrypt($vartmp04), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp1",Encrypt($vartmp04), time() + $qtTimeLimitPHP, "/", false, 0, 1);

                            if ($auth_rtr[6] == "007" or ($auth_rtr[6] == "008"  and $vartmp01[2] == "EF3AD951-9029-8D49-DAED-CD187610FC25")) {
                                //setcookie("mmmIDc1", $Cryptor->Encrypt(-1), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                                setcookie("mmmIDc1", Encrypt(-1), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            } else {
                                //setcookie("mmmIDc1", $Cryptor->Encrypt(0), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                                setcookie("mmmIDc1", Encrypt(0), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            }
                            if (isset($_POST["remember"])) {
                                //setcookie("mmmIDSa", $Cryptor->Encrypt($_POST["email"] . "#" . $_POST["password"]), time() + $qtTimeLimitPHP * 240, "/", false, 0, 1);
                                setcookie("mmmIDSa", Encrypt($_POST["email"] . "#" . $_POST["password"]), time() + $qtTimeLimitPHP * 240, "/", false, 0, 1);
                            } else {
                                setcookie("mmmIDSa", "", time() - $qtTimeLimitPHP, "/");
                            }
                        } else {

                            setcookie("mmmIDp1", $tw2_encrypter->EncryptPWD($vartmp01), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp2", $tw2_encrypter->EncryptPWD($vartmp02), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp3", $tw2_encrypter->EncryptPWD($vartmp03), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            setcookie("mmmIDp4", $tw2_encrypter->EncryptPWD($vartmp04), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            if ($auth_rtr[6] == "007" or ($auth_rtr[6] == "008"  and $vartmp01[2] == "EF3AD951-9029-8D49-DAED-CD187610FC25")) {
                                setcookie("mmmIDc1", $tw2_encrypter->EncryptPWD(-1), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            } else {
                                setcookie("mmmIDc1", $tw2_encrypter->EncryptPWD(0), time() + $qtTimeLimitPHP, "/", false, 0, 1);
                            }
                            if (isset($_POST["remember"])) {
                                setcookie("mmmIDSa", $tw2_encrypter->EncryptPWD($_POST["email"] . "#" . $_POST["password"]), time() + $qtTimeLimitPHP * 240, "/", false, 0, 1);
                            } else {
                                setcookie("mmmIDSa", "", time() - $qtTimeLimitPHP, "/");
                            }
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

                    if ($IsUtilizaPwd2) {
                        $tokenId    = base64_encode(openssl_random_pseudo_bytes(32));
                    } else {
                        $tokenId    = base64_encode(mcrypt_create_iv(32));
                    }
                    
                    $issuedAt   = time();
                    $notBefore  = $issuedAt + 0;  //Adding 10 seconds
                    $expire     = $notBefore + 3600; // Adding 60 seconds
                    $serverName = $config->get('serverName');

                    /*
                    * Create the token as an array
                    */
                    // $menu[0]["Name"] = "Monitor";
                    // $menu[0]["url"] = "/index";

                    // $menu[1]["Name"] = "Cadastro";
                    // $menu[1][0]["Name"] = "Produtos";
                    // $menu[1][0]["url"] = "/products/list";
                    // $menu[1][1]["Name"] = "Grupo";
                    // $menu[1][1]["url"] = "/productgroups/list";
                    // $menu[1][2]["Name"] = "Forma de Pagamento";
                    // $menu[1][2]["url"] = "/paymenttypes/list";
                    // $menu[1][3]["Name"] = "Clientes";
                    // $menu[1][3]["url"] = "/customers/list";
                    
                    // $menu[2]["Name"] = "Relatório";
                    // $menu[2][0]["Name"] = "Faturamento";
                    // $menu[2][0][0]["Name"] = "Produtos por Grupo";
                    // $menu[2][0][0]["url"] = "/WEBApp/#/list-prod";
                    // $menu[2][0][1]["Name"] = "Produtos por POS - Faturado";
                    // $menu[2][0][1]["url"] = "/WEBApp/#/list-gprod";
                    // $menu[2][0][2]["Name"] = "Produtos por POS - Cortesia";
                    // $menu[2][0][2]["url"] = "/WEBApp/#/list-fpag";
                    // $menu[2][0][3]["Name"] = "Produtos por Usuário";
                    // $menu[2][0][3]["url"] = "/WEBApp/#/list-fpag";
                    // $menu[2][0][4]["Name"] = "Produtos Baixados por Usuário";
                    // $menu[2][0][4]["url"] = "/WEBApp/#/list-fpag";
                    // $menu[2][0][5]["Name"] = "por Hora";
                    // $menu[2][0][5]["url"] = "/WEBApp/#/list-fpag";


                    // $menu[2][1]["Name"] = "Vendas";
                    // $menu[2][1][0]["Name"] = "Listagem (Itens)";
                    // $menu[2][1][0]["url"] = "/WEBApp/#/list-prod";
                    // $menu[2][1][1]["Name"] = "Listagem (Tipo de Pagamento)";
                    // $menu[2][1][1]["url"] = "/WEBApp/#/list-gprod";

                    // $menu[3]["Name"] = "Consulta";
                    // $menu[3][0]["Name"] = "POS - PDV";
                    // $menu[3][0]["url"] = "/pospdvs";
                    // $menu[3][1]["Name"] = "POS - Cliente/Grupo";
                    // $menu[3][1]["url"] = "/pospdvs/customer";
                    // $menu[3][2]["Name"] = "Clientes - Ranking";
                    // $menu[3][2]["url"] = "/pospdvs/ranking";
                    
                    // $menu[4]["Name"] = "Segurança";
                    // $menu[4][0]["Name"] = "Funcionalidade";
                    // $menu[4][0]["url"] = "/security/functionality";
                    // $menu[4][1]["Name"] = "Perfil";
                    // $menu[4][1]["url"] = "/security/role";
                    // $menu[4][2]["Name"] = "Objetos";
                    // $menu[4][2]["url"] = "/security/object";
                    // $menu[4][3]["Name"] = "Usuários";
                    // $menu[4][3]["url"] = "/security/user";


                    $CompaniesTMP = json_decode($User->Companies, true);
                    $data = [
                        'iat'  => $issuedAt,         // Issued at: time when the token was generated
                        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                        'iss'  => $serverName,       // Issuer
                        'nbf'  => $notBefore,        // Not before
                        'exp'  => $expire,           // Expire
                        'data' => [                  // Data related to the signer user
                            'GrpId'     => $User->CompanyGroupID, //
                            'GrpName'   => $User->GroupName, //
                            'SYSType'   => $User->SYSType, //
                            'Companies' => $CompaniesTMP, //
                            //'Menu'      => $menu, //
                            'Name'      => $User->UsersName, // User name
                            'UserId'    => $User->UsersID, //User Id
                            'UserType'  => $User->ShortName
                            // 'Menus'     => json_encode($menuFinal, true)
                        ]
                    ];
                    header('Content-type: application/json');

                    /*
                    * Extract the key, which is coming from the config file.
                    *
                    * Best suggestion is the key to be a binary string and
                    * store it in encoded in a config file.
                    *
                    * Can be generated with base64_encode(openssl_random_pseudo_bytes(64));
                    *
                    * keep it secure! You'll need the exact key to verify the
                    * token later.
                    */
                    $secretKey = base64_decode($config->get('jwt')->get('key'));

                    /*
                    * Extract the algorithm from the config file too
                    */
                    $algorithm = $config->get('jwt')->get('algorithm');

                    /*
                    * Encode the array to a JWT string.
                    * Second parameter is the key to encode the token.
                    *
                    * The output string can be validated at http://jwt.io/
                    */
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

function Encrypt($plaintext){

  $POSControlConfig = new POSControlConfig();

  $key  = $POSControlConfig->key;

  //$plaintext = "message to be encrypted";
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = openssl_random_pseudo_bytes($ivlen);
  $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
  $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
  $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
  return $ciphertext;
}

function Decrypt($ciphertext){
  $POSControlConfig = new POSControlConfig();

  $key  = $POSControlConfig->key;

  //decrypt later....
  $c = base64_decode($ciphertext);
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = substr($c, 0, $ivlen);
  $hmac = substr($c, $ivlen, $sha2len=32);
  $ciphertext_raw = substr($c, $ivlen+$sha2len);
  $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
  $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
  if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
  {
      return $original_plaintext;
  }
}

function validaEmail($email) {

  $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
  if (preg_match($pattern, $email))
      return true;
  else
      return false;
}

function validaCnpj($cnpj){
  $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
  // Valida tamanho
  if (strlen($cnpj) != 14)
      return false;
  // Valida primeiro dígito verificador
  for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
  {
      $soma += $cnpj{$i} * $j;
      $j = ($j == 2) ? 9 : $j - 1;
  }
  $resto = $soma % 11;
  if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
      return false;
  // Valida segundo dígito verificador
  for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
  {
      $soma += $cnpj{$i} * $j;
      $j = ($j == 2) ? 9 : $j - 1;
  }
  $resto = $soma % 11;
  return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}

?>