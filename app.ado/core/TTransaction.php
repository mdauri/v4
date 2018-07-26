<?php
namespace ado\core;
/*
 * classe TTransaction
 * esta classe provê os métodos necessários para manipular transações
 */
final class TTransaction
{
  private static $conn;     //conexão ativa
  private static $logger;   // objeto de LOG

  /*
   * método __construct()
   * Está declarado como private para impedir que se crie instancias de TTransaction
   */
  private function __construct()
  {

  }
  /* metodo open()
   * Abre uma transação e uma conexão ao BD
   * @param $database = nome do banco de dados
   */
  public static function open($database)
  {
    // abre uma conexao e armazena na propriedade estatica $conn
    if (empty(self::$conn)) {
      self::$conn = TConnection::open($database);
      // inicia a transação
      self::$conn->beginTransaction();
      //desliga o log do SQL
      self::$logger = NULL;
    }
  }
  /*
   * método get()
   * retorna a conexao ativa da transação
   */
  public static function get()
  {
    // retorna a conexao ativa
    return self::$conn;
  } 

  /*
   * metodo rollback
   * desfaz todas as operacoes realizada na transação
   */
  public static function rollback()
  {
    if (self::$conn) {
      // desfaz as operações realizadas durante a transação
      self::$conn->rollback();
      self::$conn = NULL;
    }
  }

  /*
   * metodo close()
   * aplica todas as oeprações realizadas e fecha a transação
   */
  public static function close()
  {
    if (self::$conn) {
      //aplica as operações realizdas
      //durante a transação
      self::$conn->commit();
      self::$conn = NULL;
    }
  }

  /*
   * metodo setLogger()
   * define qual estratégia (algoritimo de LOG será usado)
   */
  public static function setLogger(TLogger $logger)
  {
    self::$logger = $logger;
  }
  /*
   * metodo log()
   * armazena uma mensagem no arquivo de LOG
   * baseada na estratégia ($logger) atual
   */
  public static function log($message)
  {
    //verifca se existe um logger
    if (self::$logger) {
      self::$logger->write($message);
    }
  }
} 
?>