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

  private static $conns;  //conexoes ativas com o banco
  private static $loggers; //objetos de LOG -> 1 pra cada conexão ativa

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
  /* metodo opendb()
   * Abre uma transação e uma conexão ao BD definido
   * @param $database = nome do banco de dados
   */
  public static function opendb($database)
  {
    // abre uma conexao e armazena na propriedade estatica $conn
    if (empty(self::$conns[$database])) {
      self::$conns[$database] = TConnection::open($database);
      // inicia a transação
      self::$conns[$database]->beginTransaction();
      //desliga o log do SQL
      self::$loggers[$database] = NULL;
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
   * método getdb()
   * retorna a conexao ativa da transação com o banco definido
   * @param $database = nome do banco de dados
   */
  public static function getdb($database)
  {
    // retorna a conexao ativa
    return self::$conns[$database];
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
   * metodo rollbackdb
   * desfaz todas as operacoes realizadas na transação
   * @param $database = nome do banco de dados
   */
  public static function rollbackdb($database)
  {
    if (self::$conns[$database]) {
      // desfaz as operações realizadas durante a transação
      self::$conns[$database]->rollback();
      self::$conns[$database] = NULL;
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
   * metodo closedb()
   * aplica todas as operações realizadas e fecha a transação
   * @param $database = nome do banco de dados
   */
  public static function closedb($database)
  {
    if (self::$conns[$database]) {
      //aplica as operações realizdas
      //durante a transação
      self::$conns[$database]->commit();
      self::$conns[$database] = NULL;
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
   * metodo setLoggerdb()
   * define qual estratégia (algoritimo de LOG será usado)
   * @param $database = nome do banco de dados
   */
  public static function setLoggerdb(TLogger $logger, $database)
  {
    self::$loggers[$database] = $logger;
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
  /*
   * metodo logdb()
   * armazena uma mensagem no arquivo de LOG
   * baseada na estratégia ($logger) atual
   * @param $database = nome do banco de dados
   */
  public static function logdb($message, $database)
  {
    //verifca se existe um logger
    if (self::$loggers[$database]) {
      self::$loggers[$database]->write($message);
    }
  }
} 
?>