<?php
namespace ado\core;
/*
 * classe TRepository
 * esta classe provê os métodos necessários para manipular coleções de objetos.
 */
final class TRepository
{
  private $class; //nome da classe manipulada pelo repositorio
  private $fullyqualifiedclass; //nome completo da classe
  private $database; //banco de dados
  /*
   * metodo __construct
   * intancia um Repositorio de objetos
   * @param $class = Classe dos Objetos
   */
  function __construct($database, $class)
  {
    $this->class = $class;
    $this->fullyqualifiedclass = '\ado\model\\' . $class;
    $this->database = $database;
  }
  /*
   * método load()
   * Recuperar um conjunto de objetos (collection) da base de dados
   * através de um cirtério de seleção e instanciá-los em memória
   * @param $criteria = objeto do tipo TCriteria
   */
  function load(TCriteria $criteria, $isArray=NULL, $fields=NULL, $joins=NULL)
  {
    // instancia a instrução de SELECT
    $sql = new TSqlSelect;
    
    //atribuindo campos
    if ($fields) {
      foreach ($fields as $field) {
        $sql->addColumn("$field");
      }
    } else {
      $sql->addColumn('*');
    }

    //atribuindo tabelas para o join
    if ($joins) {
      foreach ($joins as $join) {
        $sql->addJoin($join);
      }
    }
        
    $sql->setEntity($this->class);
    // atribui o critério passado como parâmetro
    $sql->setCriteria($criteria);

    //obtem transação ativa
    if (is_null($this->database)) {
      $conn = TTransaction::get();
    } else {
      $conn = TTransaction::getdb($this->database);
    }
    
    if ($conn) {
      // registra mensagem de log
      if (is_null($this->database)) {
        TTransaction::log($sql->getInstruction());
      } else {
        TTransaction::logdb($sql->getInstruction(),$this->database);
      }      

      //executa a consulta no banco de dados
      $result = $conn->Query($sql->getInstruction());

      if ($result) {
        //percorre os resultados da consulta, retornando um objeto
        //while ($row = $result->fetchObject($this->class . 'Record')) {
        while ($row = $result->fetchObject($this->fullyqualifiedclass . 'Record')) {
          //armazena no array $results;
          if ($isArray) {
            $results[] = $row->toArray();
          } else {
            $results[] = $row;
          }
          
        }
      }
      return $results;
    }
    else {
      // se nao tiver transação, retorna uma exceção
      throw new \Exception("Não há transação ativa!!");
    }
  }
  /*
   * metodo delete()
   * Excluir um conjunto de objetos (collection) da base de dados
   * através deum critério de seleção.
   * @param $criteria = objeto do tip TCriteria
   */
  function delete(TCriteria $criteria)
  {
    // instancia a instrução de DELETE
    $sql = new TSqlDelete;
    $sql->setEntity($this->class);
    // atribui o critério passado como parâmetro
    $sql->setCriteria($criteria);

    //obtem transacao ativa
    //if ($conn = TTransaction::get()) {
    if ($conn = TTransaction::getdb($this->database)) {
      // registra mensagem de log
      TTransaction::log($sql->getInstruction());
      //executa a instrução de DELETE
      $result = $conn->Query($sql->getInstruction());
      return $result;
    } else {
      // se nao tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
    }
  }
  /*
   * método count()
   * Retorna a quantidade de objetos da base de dados
   * que satisfazem um determinado critério de seleção.
   * @param $criteria = objeto do tipo TCriteria
   */
  function count(TCriteria $criteria)
  {
    // instancia a instrução de SELECT
    $sql = new TSqlSelect;
    $sql->addColumn('count(*)');
    $sql->setEntity($this->class);
    // atribui o critério passado como parâmetro
    $sql->setCriteria($criteria);

    //obetem transação ativa
    //if ($conn = TTransaction::get()) {
    if ($conn = TTransaction::getdb($this->database)) {
      // registra mensagem de log
      TTransaction::log($sql->getInstruction());
      //executa a instrução de SELECT
      $result = $conn->Query($sql->getInstruction());

      if ($result) {
        $row = $result->fetch();
      }
      return $row[0];
    }
    else {
      // se nao tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
    }
  } 
} 
?>