<?php
namespace ado\model;

use ado\core\TRecord;
use ado\core\TTransaction;
use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TLoggerTXT;
use ado\core\TRepository;
use app\TApp;


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

final class TUserService {

    function getUser($username)
    {        
        //obtem objetos do banco de dados
        // try
        // {
            $criteria = new TCriteria;
            //filtra por username
            $criteria->add(new TFilter('email', '=', $username));
            
            //instancia um repositorio para usuário
            $repository = new TRepository('Users');
            // retorna todos os objetos que satisfem o critério
            $users = $repository->load($criteria);
            if ($users) {
                if (count($users) == 1) {
                    $user = $users[0];                
                } else {
                    unset($user);
                }
            }
            return $user;
        // }
        // catch(Exception $e)
        // {
        //     echo 'Erro ' . $e->getMessage();
        //     //desfaz todas as alterações no banco de dados
        //     TTransaction::rollback();
        // }
    }
}
?>