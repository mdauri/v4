<?php
namespace ado\core;
/*
* classe TFilter
* Esta classe provê uma interface para definição de filtros de seleção
*/
class TFilter extends TExpression
{
  private $variable; // variavel
  private $operator; // operador
  private $value; // valor
/*
 * método __construct()
 * instancia um novo filtro
 * @param $variable = variavel
 * @param $operator = operador (>,<)
 * @param $value = valor a ser comparado
 */
  public function __construct($variable, $operator, $value)
  {
    //armazena as propriedades
    $this->variable = $variable;
    $this->operator = $operator;
    //transforma o valor de acordo com certas regras antes de atribuir á propriedade $this->value
    $this->value = $this->transform($value);
  }

  /*metodo transform
   * recebe um valor e faz as modificacoes necessárias para ele ser interpretado pelo banco
   * podendo ser um integer/string/boolean ou array.
   * @param $value = valor a ser transformado
   */
  private function transform($value)
  {
    //caso seja um array
    if (is_Array($value)) {
      //percorre os valores
      foreach ($value as $x) {
        // se for o primeiro
        if (is_integer($x)) {
          $foo[]= $x;
        }
        elseif (is_string($x)) {
          $foo[] = "'$x'";
        }
      }
      // converte o array em string separada por ","
      $result = '(' . implode(',',$foo) . ')';
    }
    //caso seja uma string
    elseif (is_string($value)) {
      //adiciona aspas
      $result = "'$value'";
      //feita para tratar as instruções SQL diretas
      if (substr($value,0,1)==='$') {
        $result = substr($value,1,strlen($value)-1);
      }
    }
    //caso seja um valor nulo
    elseif (is_null($value)) {
      //armazena NULL
      $result = 'NULL';
    }
    //caso seja booleano
    elseif (is_bool($value)) {
      //armazena TRUE ou FALSE
      $result = $value ? 'TRUE' : 'FALSE';
    }
    else {
      $result = $value;
    }
    //retorna o valor
    return $result;
  }

  /*
   * metodo dump()
   * retorna filtro em forma de expressão
   */
  public function dump()
  {
    //concatena a expressão
    return "{$this->variable} {$this->operator} {$this->value}";
  } 
}
?>