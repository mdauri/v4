<?php
namespace ado;
/*
* classe TSqlInsert
* Esta classe provê meios para manipulação de uma instrução de INSERT no banco de dados
*/
final class TSqlInsert extends TSqlInstruction
{
  /*
   * método setRowData()
   * Atribui valores à determinadas colunas do banco de dados que serão inseridas
   * @param $column = coluna da tabela
   * @param $value = valor a ser armazenado
   */
  public function setRowData($column, $value)
  {
    //monta um array indexado pelo nome da coluna
    if (is_string($value)) {
      //adiciona \ em aspas
      $value = addslashes($value);
      //caso seja uma string
      $this->columnValues[$column] = "'$value'";
    } elseif (is_bool($value)) {
      // caso seja um boolean
      $this->columnValues[$column] = $value ? 'TRUE' : 'FALSE';
    } elseif (isset($value)) {
      //caso seja outro tipo de dado
      $this->columnValues[$column] = $value;
    } else {
      //caso seja NULL
      $this->columnValues[$column] = "NULL";
    }
  } 

  /*
   * metodo setCriteria($criteria)
   * não existe no contexto desta classe, logo irá lancar um erro se for executado
   */
  public function setCriteria($criteria)
  {
    // Lança o erro
    throw new Exception("Cannot call setCriteria from " . __CLASS__);
  }

  /*
   * metodo getInstruction()
   * retorna a instrução de INSERT em forma de string.
   */
  public function getInstruction()
  {
    $this->sql = "INSERT INTO {$this->entity} (";
    // monta uma strinf contendo os nomes de colunas
    $columns = implode(', ', array_keys($this->columnValues));
    // monta uma string contendo os valores
    $values = implode(', ', array_values($this->columnValues));
    $this->sql .= $columns . ')';
    $this->sql .= " VALUES ({$values})";

    return $this->sql;
  }
}
?>