<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

final class TClosedDrawService {

    function getTopClosedSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeClosedDraw', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeClosedDraw', '<=', $DatetimeEnd));
        
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Closeddraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Closeddraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber');
        $criteria->setProperty('group', 'PropertyNumber');

        $joins = array(
          "inner join PosPdv on substring(Closeddraw.ClosedDrawPosCodeID, 1, 2) = 'FC' and Closeddraw.PosPdvID = PosPdv.PosPdvID and Closeddraw.CompanyID = PosPdv.CompanyID"
        );

        $fields = array(
          "PropertyNumber", 
          "count(distinct ClosedDrawPosCodeID) QTClosedSale"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','ClosedDraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;
        
    }

    function getTopClosedSale1($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeClosedDraw', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeClosedDraw', '<=', $DatetimeEnd));
        
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Closeddraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Closeddraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber, Closeddraw.DatetimeClosedDraw');
        
        $joins = array(
          "inner join PosPdv on substring(Closeddraw.ClosedDrawPosCodeID, 1, 2) = 'FC' and Closeddraw.PosPdvID = PosPdv.PosPdvID and Closeddraw.CompanyID = PosPdv.CompanyID"
        );

        $fields = array(
          "PropertyNumber", 
          "Closeddraw.QTSent+Closeddraw.QTNSent QT", 
          "Closeddraw.ValSent", 
          "Closeddraw.ValNSent", 
          "convert(nvarchar, DatetimeClosedDraw, 103) + ' ' + convert(nvarchar(5), convert(time, DatetimeClosedDraw)) DatetimeClosedDraw"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','ClosedDraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;
        
    }

    function getTopClosedSale2($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeClosedDraw', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Closeddraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Closeddraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber');
        $criteria->setProperty('group', 'PropertyNumber');

        $joins = array(
          "inner join PosPdv on substring(Closeddraw.ClosedDrawPosCodeID, 1, 2) = 'FC' and Closeddraw.PosPdvID = PosPdv.PosPdvID and Closeddraw.CompanyID = PosPdv.CompanyID"
        );

        $fields = array(
          "PropertyNumber", 
          "count(distinct ClosedDrawPosCodeID) QTClosedSale"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','ClosedDraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;        
    }

    function getTopClosedSale3($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeClosedDraw', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
                
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Closeddraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Closeddraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber, Closeddraw.DatetimeClosedDraw');
        
        $joins = array(
          "inner join PosPdv on substring(Closeddraw.ClosedDrawPosCodeID, 1, 2) = 'FC' and Closeddraw.PosPdvID = PosPdv.PosPdvID and Closeddraw.CompanyID = PosPdv.CompanyID"
        );

        $fields = array(
          "PropertyNumber", 
          "Closeddraw.QTSent+Closeddraw.QTNSent QT", 
          "Closeddraw.ValSent", 
          "Closeddraw.ValNSent", 
          "convert(nvarchar, DatetimeClosedDraw, 103) + ' ' + convert(nvarchar(5), convert(time, DatetimeClosedDraw)) DatetimeClosedDraw"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','ClosedDraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;
        
    }
}
?>