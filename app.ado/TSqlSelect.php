<?php
namespace ado;
/*
 * classe TSqlSelect
 * Esta classe provÊ meios para manipulação de uma instrução SELECT no banco de dados
 */
final class TSqlSelect extends TSQLInstruction
{
  private $columns;     //array de colunas a serem retornadas.

  /*
   * metodo addColumn
   * adiciona uma coluna a ser retornada pelo SELECT
   * @param $column = coluna da tabela
   */
  public function addColumn($column)
  {
    $this->columns[] = $column;
  }
  /*
   * metodo getInstruction()
   * retorna a instrução SELECT em forma de string.
   */
  public function getInstruction()
  {
    //monta a instrução SELECT
    $this->sql = 'SELECT ';
    //monta a string com os nomes de colunas
    $this->sql .= implode(',',$this->columns);
    //adiciona na cláusula FROM o nome da tabela
    $this->sql .= ' FROM ' . $this->entity;

    // obtem a cláusula WHERE do objeto criteria
    if ($this->criteria) {
      $expression = $this->criteria->dump();
      if ($expression) {
        $this->sql .= ' WHERE ' . $expression;
      }
      // obtem as propriedades do critério
      $order = $this->criteria->getProperty('order');
      $limit = $this->criteria->getProperty('limit');
      $offset = $this->criteria->getProperty('offset');

      //obtem a ordenação do SELECT
      if ($order) {
        $this->sql .= ' ORDER BY ' . $order;
      }
      if ($limit) {
        $this->sql .= ' LIMIT ' . $limit;
      }
      if ($offset) {
        $this->sql .= ' OFFSET ' . $offset;
      }
    }
    return $this->sql;
  } 
}
?>