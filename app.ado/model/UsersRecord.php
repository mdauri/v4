<?php
namespace ado\model;

use ado\core\TRecord;
use ado\core\TTransaction;
use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TLoggerTXT;
use ado\core\TRepository;
use app\TApp;

use ado\model\CompanyGroupRecord;

class CompanyUsersRecord extends TRecord{}
class AccessLevelRecord extends TRecord{}
//class CompanyRecord extends TRecord{}
class PosPdvConfigRecord extends TRecord{}

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
        $repository = new TRepository('poscontrolconfig','CompanyUsers');

        $auxcompanies = $repository->load($criteria);
        $Companies =  array();
        foreach ($auxcompanies as $company) {
            $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $company->CompanyID);

            $criteria1 = new TCriteria;
            $criteria1->add(new TFilter('CompanyID', '=', $company->CompanyID));
            $criteria1->add(new TFilter('Value', '=', 'S'));
            $criteria1->add(new TFilter('Id', '=', 66));
            $repository = new TRepository('poscontrol','PosPdvConfig');
            $pospdvconfig = $repository->load($criteria1);
            if ($pospdvconfig) {
                $objCompany->NFCE = True;
            } else {
                $objCompany->NFCE = False;
            }


            $Companies[] = $objCompany;
        };
        //retorna todas as empresas que satisfazem o criterio
        return $Companies;//$repository->load($criteria);
    }
    
    function get_CompanyGroup()
    {
        $companygroup = new CompanyGroupRecord('poscontrolconfig','CompanyGroupID',$this->CompanyGroupID);
        return $companygroup;
    }

    function get_AccessLevel()
    {
        $acesslevel = new AccessLevelRecord('poscontrolconfig','AccessLevelID',$this->AccessLevelID);
        return $acesslevel;
    }
}
?>