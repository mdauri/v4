<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

use app\ado\model\SecFunctionalityAccessLevelRecord;

final class TSecFunctionalityAccessLevelService {

    function SecFunctionalitiesbyAccessLevel($AccessLevelId)
    {        
      
      $criteria = new TCriteria;
      //filtra por username
      $criteria->add(new TFilter('AccessLevelId', '=', $AccessLevelId));     
      //instancia um repositorio para usuário
      $repository = new TRepository('poscontrolconfig','SecFunctionalityAccessLevel');
      // retorna todos os objetos que satisfem o critério
      $secfunctionalities = $repository->load($criteria);
      return $secfunctionalities;   
    }
}
?>