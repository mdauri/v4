<?php
namespace ado;
/*
 * classe TConnection
 * gerencia conexões com bancos de dados através de arquivos de configuração.
 */
final class TConnection
{
  /*
   * metodo __construct()
   * não existirão instâncias de TConnection, por isso estamos marcando-o como private
   */
  private function __construct() 
  {

  }
  /*
   * metodo open()
   * recebe o nome do banco de dados e instancia o objeto PDO correspondente
   */
  public static function open($name)
  {
    // verifica se existe aquivo de configuração para este banco de dados
    chdir(dirname(__DIR__));
    if (file_exists("app.config/{$name}.ini")) {
      // lê o INI e retorna um array
      $db = parse_ini_file("app.config/{$name}.ini");
    } else {
      // se nao existir, lança erro
      throw new \Exception("Arquivo '$name' não encontrado");      
    }

    // lê as informações contidas no arquivo
    $user = $db['user'];
    $pass = $db['pass'];
    $name = $db['name'];
    $host = $db['host'];
    $type = $db['type'];

    //descobre qual o tipo (driver) de banco de dados a ser utilizado
    switch ($type) {
      case 'pgsql':
        $conn = new \PDO("pgsql:dbname={$name};user={$user};password{$pass};host=$host");
        break;
      case 'mysql':
        $conn = new \PDO("mysql:host={$host};port=3307;dbname={$name}", $user, $pass);
        break;
      case 'sqlite':
        $conn = new \PDO("sqlite:{$name}");
        break;
      case 'ibase':
        $conn = new \PDO("firebird:dbname={$name}",$user, $pass);
        break;
      case 'oci8':
        $conn = new \PDO("oci:dbname={$name}",$user, $pass);
        break;        
      case 'mssql':
        $conn = new \PDO("sqlsrv:server={$host};Database={$name}",$user, $pass);
        break;    
    }

    //defime para que o PDO lance exceções na ocorrência de erros
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    //retorna o objeto instanciado
    return $conn;
  }
}
?>