<?php
namespace ado;
/* 
 * classe TLogger
 * Esta classe provê uma interface abstrata para definicao de algoritimos de LOG
 */
abstract class TLogger
{
  protected $filename;      // local do arquivo de LOG
  /*
   * metodo __contruct()
   * instancia um logger
   * @param $filename = local do arquivo de LOG
   */
  public function __construct($filename)
  {
    $this->filename = $filename;
    // reseta o conteudo do arquivo
    file_put_contents($filename, '');
  }

  //define o método write como obrigatório
  abstract function write($message);
} 
?>