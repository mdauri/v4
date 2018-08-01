<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

use app\ado\model\CompanyGroupRecord;

final class TCompanyGroupService {

    function getCompanyGroups()
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('CompanyGroup.Deleted', '=', '0'));
        
        //instancia um repositorio para usuário

        //customizando o retorno dos campos
        $fields = array(
          "CompanyGroup.CompanyGroupID",
          "CompanyGroup.CNPJ Cnpj",           
          "CompanyGroup.Name", 
          "CompanyGroup.Trademark",
          "CompanyGroup.Modified",
          "CompanyGroup.Deleted",
          "CompanyGroup.DatetimeRegister",
          "CompanyGroup.DatetimeModified",
          "(select count(distinct CompanyID) from Company where Company.CompanyGroupID = CompanyGroup.CompanyGroupID) QtCompany",
          "CompanyGroup.ClientType",
          "ClientTypeDesc =  CASE ClientType 
                  WHEN '0' THEN 'Rever'
                  WHEN '1' THEN 'Demo'
                  WHEN '2' THEN 'POC'
                  WHEN '3' THEN 'Cliente'
                  WHEN '4' THEN 'Free'
                  WHEN '5' THEN 'Demo Revenda'
                  WHEN '6' THEN 'Cliente Revenda'
                END",
        "CompanyGroup.StatusID",
        "Status.Name StatusDesc",   
        );
        
        //customizando os joins
        $joins = array(
          "inner join Status on CompanyGroup.StatusID = Status.StatusID"
        );

        $repository = new TRepository('poscontrol','CompanyGroup');
        // retorna todos os objetos que satisfem o critério
        $companygroups = $repository->load($criteria,true,$fields,$joins);

        return $companygroups;
        
    }
}
?>