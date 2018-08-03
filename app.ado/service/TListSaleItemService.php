<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

final class TListSaleItemService {

    function getListSaleItemsActiveCompany($activeCompany, $eventName, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $fields = array(
          "$eventName . XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItemsCompanies($Companies, $eventName, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $fields = array(
          "$eventName . XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }
    
    function getListSaleItems1($SYSType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($SysType !== "003" and $SysType !== "999") {
          if ($activeCompany == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $criteria->setProperty('group by', 'convert(char(2), convert(time, DatetimeSale))');
        $criteria->setProperty('order by', 'convert(char(2), convert(time, DatetimeSale))');
        $fields = array(
          "convert(char(2), convert(time, DatetimeSale))" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems2($SYSType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($SysType !== "003" and $SysType !== "999") {
          if ($activeCompany == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $criteria->setProperty('group by', 'convert(char(13), convert(datetime, DatetimeSale), 120), convert(char(2), convert(time, DatetimeSale))');
        $criteria->setProperty('order by', 'convert(char(13), convert(datetime, DatetimeSale), 120), convert(char(2), convert(time, DatetimeSale))');
        $fields = array(
          "convert(char(13), convert(datetime, DatetimeSale), 120)",
          "convert(char(2), convert(time, DatetimeSale))" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems3($SYSType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD'."(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($activeCompany == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group by', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $criteria->setProperty('order by', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $fields = array(
          "convert(char(10), convert(date, DatetimeSale))",
          "convert(varchar(5),CONVERT(date,DatetimeSale,106),103)" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems4($Event, $SYSType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD'."(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($activeCompany == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group by', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $criteria->setProperty('order by', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $fields = array(
          "convert(char(10), convert(date, DatetimeSale))",
          "convert(varchar(5),CONVERT(date,DatetimeSale,106),103)" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }
    
}
?>