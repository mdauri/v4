<?php
namespace ado\model;

use ado\core\TRecord;
use ado\core\TCriteria;
use ado\core\TFilter;
use ado\core\TRepository;

class CompanyGroupRecord extends TRecord{}
class CompanyUsersRecord extends TRecord{}
class AccessLevelRecord extends TRecord{}
class CompanyRecord extends TRecord{}

class UsersRecord extends TRecord 
{
    /*
        * metodo get_companies()
        * executado sempre que for acessada a propriedade "companies"
        */
    function get_Companies()
    {
        // cria um critério de seleção
        $criteria = new TCriteria;
        //filtra por codigo de Usuario
        $criteria->add(new TFilter('UsersID','=',$this->UsersID));

        //instancia repositorio CompanyUsers
        $repository = new TRepository('CompanyUsers');

        $auxcompanies = $repository->load($criteria);
        $Companies =  array();
        foreach ($auxcompanies as $company) {
            $objCompany = new CompanyRecord('CompanyID', $company->CompanyID);
            $Companies[] = $objCompany;
        };
        //retorna todas as empresas que satisfazem o criterio
        return $Companies;//$repository->load($criteria);
    }
    
    function get_CompanyGroup()
    {
        $companygroup = new CompanyGroupRecord('CompanyGroupID',$this->CompanyGroupID);
        return $companygroup;
    }

    function get_AccessLevel()
    {
        $acesslevel = new AccessLevelRecord('AccessLevelID',$this->AccessLevelID);
        return $acesslevel;
    }
}
?>