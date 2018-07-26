<?php
namespace app;
/*
 * classe TApp
 * gerencia os dados da APP.
 */
final class TApp
{
  /*
   * metodo __construct()
   * não existirão instâncias de TApp, por isso estamos marcando-o como private
   */
  private function __construct() 
  {

  }
  /*
   * metodo open()
   */
  public static function open()
  {
    // verifica se existe aquivo de configuração para este banco de dados
    chdir(dirname(__DIR__));
    if (file_exists("app.config/config.ini")) {
      // lê o INI e retorna um array
      $config = parse_ini_file("app.config/config.ini", true);
    } else {
      // se nao existir, lança erro
      throw new \Exception("Arquivo app.config não encontrado");      
    }
    
    //retorna o objeto instanciado
    return json_decode(json_encode($config), FALSE);;
  }

  public static function generateGuid($include_braces = false)
  {
    if (function_exists('com_create_guid')) {
      if ($include_braces === true) {
        return com_create_guid();
      } else {
        return substr(com_create_guid(), 1, 36);
      }
    } else {
      mt_srand((double)microtime() * 10000);
      $charid = strtoupper(md5(uniqid(rand(), true)));

      $guid = substr($charid, 0, 8) . '-' .
              substr($charid, 8, 4) . '-' .
              substr($charid, 12, 4) . '-' .
              substr($charid, 16, 4) . '-' .
              substr($charid, 20, 12);

      if ($include_braces) {
        $guid = '{' . $guid . '}';
      }
      return $guid;
    }
  }

  public static function validaCnpj($cnpj){
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
}
?>