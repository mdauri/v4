<?php
namespace ado\core;
/*
 * classe TRepository
 * esta classe provê os métodos necessários para manipular coleções de objetos.
 */
final class TRepository
{
  private $class; //nome da classe manipulada pelo repositorio
  /*
   * metodo __construct
   * intancia um Repositorio de objetos
   * @param $class = Classe dos Objetos
   */
  function __construct($class)
  {
    $this->class = $class;
  }
  /*
   * método load()
   * Recuperar um conjunto de objetos (collection) da base de dados
   * através de um cirtério de seleção e instanciá-los em memória
   * @param $criteria = objeto do tipo TCriteria
   */
  function load(TCriteria $criteria)
  {
    // instancia a instrução de SELECT
    $sql = new TSqlSelect;
    $sql->addColumn('*');
    $sql->setEntity($this->class);
    // atribui o critério passado como parâmetro
    $sql->setCriteria($criteria);

    //obetem transação ativa
    if ($conn = TTransaction::get()) {
      // registra mensagem de log
      TTransaction::log($sql->getInstruction());

      //executa a consulta no banco de dados
      $result = $conn->Query($sql->getInstruction());

      if ($result) {
        //percorre os resultados da consulta, retornando um objeto
        while ($row = $result->fetchObject($this->class . 'Record')) {
          //armazena no array $results;
          $results[] = $row;
        }
      }
      return $results;
    }
    else {
      // se nao tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
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
    if ($conn = TTransaction::get()) {
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
    if ($conn = TTransaction::get()) {
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