<?php
namespace ado\core;
/*
* classe TCriteria
* Esta classe provê uma interface utilizada para definição de cirtérios
*/
class TCriteria extends TExpression
{
  private $expressions; //armazena lista de expressões
  private $operators; //armazena lista de operadores
  private $properties; //propriedades do critério
  
  /*
  * metodo add()
  * adiciona uma expressão ao critério
  * @param $expression = expressao (objeto TExpression)
  * @param $operator = operador lógico de comparação
  */
  public function add(TExpression $expression, $operator = self::AND_OPERATOR)
  {
    //na primeira vez, não precisamos de operador lógico de expressões
    if (empty($this->expressions)) {
      unset($operator);
    }

    //agrega o resultado da expressão à lista de expressões
    $this->expressions[] = $expression;
    //if (isset($operator)) {
      $this->operators[] = $operator;
    //}
    
  }
  /*
   * metodo dump()
   * retorna a expressão final
   */
  public function dump()
  {
    $result = '';
    //concatena a lista de expressões
    if (is_Array($this->expressions)) {
      foreach ($this->expressions as $i => $expression) {
        $operator = $this->operators[$i];
        //concatena o operador com a respectiva expressão
        $result .= $operator. $expression->dump() . ' ';
      }
      $result = trim($result);
      return "({$result})";
    }
  }
  /*
   * metodo setProperty()
   * define o valor de uma propriedade
   * @param $property = propriedade
   * @param $value = valor
   */
  public function setProperty($property, $value)
  {
    $this->properties[$property] = $value;
  }
  /*
   * metodo getProperty()
   * retorna o valor de uma propriedade
   * @param $property = propriedade
   */
  public function getProperty($property)
  {
    return $this->properties[$property];
  }
}
?>