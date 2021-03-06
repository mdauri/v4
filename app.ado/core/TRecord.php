<?php
namespace ado\core;

/*
 * classe TRecord
 * Esta classe provê os métodos necessários para persistir e 
 * recuperar objetos da base de dados (Active Record)
 */
abstract class TRecord
{
  protected $data;  // array contendo os dados do objeto

  /*
   * metodo __construct()
   * instancia um Active Record. Se passado o $id, já carrega o objeto
   * @param[$id] = ID do objeto
   */
  public function __construct($nomebd = NULL, $nomecampo = NULL, $id = NULL) 
  {
    
    if ($id) {  // se o ID for informado
      // carrega o objeto correspondente
      if ($nomebd) {
        $object = $this->loaddb($nomebd, $nomecampo,$id);
      } else {
        $object = $this->load($nomecampo,$id);
      }      
      if ($object) {
        $this->fromArray($object->toArray());
      }
    }
  }

  /*
   * metodo __clone()
   * executando quando o objeto for clonado.
   * limpa o ID para que seja gerado um novo ID para o clone.
   */
  public function __clone()
  {
    unset($this->id);
  }

  /*
   * metodo __set()
   * executado sempre que uma propriedade for atribuida.
   */
  public function __set($prop, $value)
  {
    // verifica se existe método set_<propriedade>
    if (method_exists($this, 'set_'.$prop)) {
      // executa o método set_<proprieda>
      call_user_func(array($this, 'set_'.$prop), $value);
    }
    else {
      // atribui o valor da propriedade
      $this->data[$prop] = $value;
    }
  }
  /*
   * método __get()
   * executado sempre que uma propriedade for requerida
   */
  public function __get($prop) 
  {
    //verifica se existe método get_<propriedade>
    if (method_exists($this, 'get_'.$prop)) {
      //executa o método get_<propriedade>
      return call_user_func(array($this, 'get_'.$prop));
    }
    else {
      // retorna o valor da propriedade
      return $this->data[$prop];
    }
  }
  /*
   * metodo getEntity()
   * retorna o nome da entidade (tabela)
   */
  private function getEntity()
  {
    // obtem o nome da classe
    $classe = strtolower(get_class($this));
    
    //removendo "\ado\model\"
    $classe = substr(strrchr($classe, "\\"), 1);
    
    // retorna o nome da classe - "Record"
    return substr($classe, 0, -6);
  }
  /* 
   * metodo fromArray
   * preenche os dados do objeto com um array
   */
  public function fromArray($data) 
  {
    $this->data = $data;
  }
  /*
   * metodo toArray
   * retorna os dados do objeto como array
   */
  public function toArray()
  {
    return $this->data;
  } 
  /*
   * metodo store()
   * armazena o objeto na base de dados e retorna
   * o numero de linhas afetadas pela instrução SQL (zero ou um)
   */
  public function store()
  {
    // verifica se tem ID ou se existe na base de dados
    if (empty($this->data['id']) or (!$this->load($this->id))) {
      // incrementa o ID
      $this->id = $this->getLast() + 1;
      // cria uma instrução de insert
      $sql = new TSqlInsert;
      $sql->setEntity($this->getEntity());
      // percorre os dados do objeto
      foreach ($this->data as $key => $value) {
        //passa os dados do objeto para o SQL
        $sql->setRowData($key, $this->$key);
      }
    }
    else {
      // instancia instrução de update
      $sql = new TSqlUpdate;
      $sql->setEntity($this->getEntity());
      // cria um critério de seleção baseado no ID
      $criteria = new TCriteria;
      $criteria->add(new TFilter('id','=',$this->id));
      $sql->setCriteria($criteria);
      // percorre os dados do objeto
      foreach ($this->data as $key => $value) {
        if ($key !== 'id') // o ID não precisa ir no UPDATE 
        {
          // passa od dados do objeto para o SQL
          $sql->setRowData($key, $this->$key);
        }  
      }
    }
    // obtem transação ativa
    if ($conn = TTransaction::get() ) {
      // faz o log e executa o SQL
      TTransaction::log($sql->getInstruction());
      $result = $conn->exec($sql->getInstruction());
      //retorna o resultado
      return $result;
    }
    else {
      // se nao tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
    }
  }
  /*
   * metodo load()
   * recupera (retorna) um objeto da base de dados
   * através de seu ID e instancia ele na memória
   * @param $nomecampo = Nome do campo na tabela a ser pesquisado
   * @param $id = ID do objeto
   */
  public function load($nomecampo, $id)
  {
    //instancia instrução de SELECT
    $sql = new TSqlSelect;
    $sql->setEntity($this->getEntity());
    $sql->addColumn('*');

    //cria critério de seleção baseado no ID
    $criteria = new TCriteria;
    $criteria->add(new TFilter($nomecampo, '=', $id));
    // define o critério de seleção de dados
    $sql->setCriteria($criteria);
    // obtem trasação ativa
    if ($conn = TTransaction::get()) {
      //cria mensagem de log e executa a consulta
      TTransaction::log($sql->getInstruction());
      $result = $conn->Query($sql->getInstruction());
      // se retornou algum dado
      if ($result) {
        //retorna os dados em forma de objeto
        $object = $result->fetchObject(get_class($this));
      }
      return $object;
    }
    else {
      // se não tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!!");
    }
  }
  /*
   * metodo loaddb()
   * recupera (retorna) um objeto da base de dados
   * através de seu ID e instancia ele na memória
   * @param $nomedb = Nome do banco de dados
   * @param $nomecampo = Nome do campo na tabela a ser pesquisado
   * @param $id = ID do objeto
   */
  public function loaddb($nomedb, $nomecampo, $id)
  {
    //instancia instrução de SELECT
    $sql = new TSqlSelect;
    $sql->setEntity($this->getEntity());
    $sql->addColumn('*');

    //cria critério de seleção baseado no ID
    $criteria = new TCriteria;
    $criteria->add(new TFilter($nomecampo, '=', $id));
    // define o critério de seleção de dados
    $sql->setCriteria($criteria);
    // obtem trasação ativa
    if ($conn = TTransaction::getdb($nomedb)) {
      //cria mensagem de log e executa a consulta
      TTransaction::logdb($sql->getInstruction(),$nomedb);
      $result = $conn->Query($sql->getInstruction());
      // se retornou algum dado
      if ($result) {
        //retorna os dados em forma de objeto
        $object = $result->fetchObject(get_class($this));
      }
      return $object;
    }
    else {
      // se não tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!!");
    }
  }
  /*
   * metodo delete()
   * exclui um objeto da base de dados através de seu ID
   * @param $id = ID do objeto
   */
  public function delete($id = NULL)
  {
    // o ID é o parametro ou a propriedade ID
    $id = $id ? $id : $this->id;
    // instancia uma instrução de DELETE
    $sql = new TSqlDelete;
    $sql->setEntity($this->getEntity());

    //cria critério de seleção de dados
    $criteria = new TCriteria;
    $criteria->add(new TFilter('id','=', $id));
    // define o critério de seleção baseado no ID
    $sql->setCriteria($criteria);

    //ontem transação ativa
    if ($conn = TTransaction::get()) {
      // faz o log e executa o SQL
      TTransaction::log($sql->getInstruction());
      $result = $conn->exec($sql->getInstruction());
      //retorna o resultado
      return $result;
    }
    else {
      // se nao tiver transação, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
    }
  } 
  /*
   * metodo getLast()
   * retorna o ultimo ID
   */
  private function getLast() 
  {
    //inicia transação
    if ($conn = TTransaction::get()) {
      // instancia a instrução de SELECT
      $sql = new TSqlSelect;
      $sql->addColumn('max(id) as ID');
      $sql->setEntity($this->getEntity());
      //cria log e executa instrução SQL
      TTransaction::log($sql->getInstruction());
      $result = $conn->Query($sql->getInstruction());
      // retorna os dados do banco
      $row = $result->fetch();
      return $row[0];
    }
    else {
      //se nao tiver transacao, retorna uma exceção
      throw new Exception("Não há transação ativa!!");
    }
  }
}