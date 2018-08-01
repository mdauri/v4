<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

use app\ado\model\UsersRecord;

final class TSecFunctionalityService {

    function getMenuSecFunctionalities()
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('SecFunctionalityType', '=', '6'));
        $criteria->setProperty('ORDER', 'SecFunctionalityOrder');
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrolconfig','SecFunctionality');
        // retorna todos os objetos que satisfem o critério
        $secfunctionalities = $repository->load($criteria,true);
        return $secfunctionalities;
        
    }
}
?>