<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

final class TOpenDrawService {

    function getTopOpenSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeOpenDraw', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeOpenDraw', '<=', $DatetimeEnd));
        
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Opendraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Opendraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber, Opendraw.DatetimeOpenDraw');
        //$criteria->setProperty('group', 'PropertyNumber, Opendraw.DatetimeOpenDraw');

        $fields = array(
          "PropertyNumber", 
          "Opendraw.Amount Val", 
          "convert(nvarchar, DatetimeOpenDraw, 103) + ' ' + convert(nvarchar(5), convert(time, DatetimeOpenDraw))  DatetimeOpenDraw"
        );

        $joins = array(
          "inner join PosPdv on substring(Opendraw.OpenDrawPosCodeID, 1, 2) = 'FC' and Opendraw.PosPdvID = PosPdv.PosPdvID and Opendraw.CompanyID = PosPdv.CompanyID"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Opendraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;
        
    }

    function getTopOpenSale1($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeOpenDraw', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        
        $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('Opendraw.CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('Opendraw.CompanyID', '=', $activeCompany));
        }
        $criteria->setProperty('order', 'PropertyNumber');
        $criteria->setProperty('group', 'PropertyNumber, Opendraw.DatetimeOpenDraw');

        $joins = array(
          "inner join PosPdv on substring(Opendraw.OpenDrawPosCodeID, 1, 2) = 'FC' and Opendraw.PosPdvID = PosPdv.PosPdvID and Opendraw.CompanyID = PosPdv.CompanyID"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','Opendraw');
        // retorna todos os objetos que satisfem o critério
        $events = $repository->load($criteria,true,$fields,$joins);        
        return $events;
        
    }
}
?>