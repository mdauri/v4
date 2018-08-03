<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

final class TEventService {

    function getEventsbyDatetimeBegin()
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeBegin', '<=', '$GETDATE()'));
        $criteria->setProperty('order', 'DatetimeBegin desc');
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Event');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria);        
        return $events;
        
    }
    function getEventsbyDatetimeBeginCompany($activeCompany)
    {
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeBegin', '<=', '$GETDATE()'));
        $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        $criteria->setProperty('order', 'DatetimeBegin desc');
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Event');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria);        
        return $events;
    }

    function getEventsbyDatetimeBeginCompanyGroup($CompanyGroupID)
    {
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeBegin', '<=', '$GETDATE()'));
        $criteria->add(new TFilter('CompanyGroupID', '=', $CompanyGroupID));
        $criteria->setProperty('order', 'DatetimeBegin desc');
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Event');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria);        
        return $events;
    }

    function getEventsbyDatetimeBeginCompanyAsc($activeCompany)
    {
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeBegin', '<=', '$GETDATE()'));
        $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        $criteria->setProperty('order', 'DatetimeBegin');
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Event');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria);        
        return $events;
    }

    function getEventsbyDatetimeBeginCompanyGroupAsc($CompanyGroupID)
    {
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeBegin', '<=', '$GETDATE()'));
        $criteria->add(new TFilter('CompanyGroupID', '=', $CompanyGroupID));
        $criteria->setProperty('order', 'DatetimeBegin');
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Event');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria);        
        return $events;
    }
}
?>