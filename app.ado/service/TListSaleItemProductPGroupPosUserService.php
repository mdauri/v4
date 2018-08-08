<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use ado\core\TExpression;
use app\TApp;

final class TListSaleItemProductPGroupPosUserService
{
  public function getListPendencia($topsaleType, $Companies, $activeCompany)
  {
    $criteria = new TCriteria;
    $criteria1 = new TCriteria;
    
    $criteria0 = new TCriteria;
    $criteria0->add(new TFilter('QTDSalesNotSynced', '<>', 0));    
    
    $criteria2 = new TCriteria;
    $criteria2->add(new TFilter('BattLevel', '<', 60));
    
    $criteria3 = new TCriteria;
    $criteria3->add(new TFilter('Charging', '=', 0));
    
    $criteria4 = new TCriteria;
    $criteria5 = new TCriteria;
    $criteria6 = new TCriteria;

    if (isset($topsaleType) and $topsaleType == 0) {      
      $criteria1->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(date, dateadd(day, 0, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));

      $criteria4->add($criteria0);
      $criteria4->add($criteria1);

      $criteria5->add($criteria2);
      $criteria5->add($criteria3);
      $criteria5->add($criteria1);

      $criteria6->add($criteria4, TExpression::OR_OPERATOR);
      $criteria6->add($criteria5, TExpression::OR_OPERATOR);


    }else {
      $criteria6->add($criteria0, TExpression::OR_OPERATOR);
      $criteria6->add($criteria2, TExpression::OR_OPERATOR);
      $criteria6->add($criteria3, TExpression::OR_OPERATOR);
    }
    
    if ($activeCompany == -1) {
      $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
    } else {
      $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
    }

    $criteria6->add($criteria);
    $criteria6->setProperty('group', 'CompanyName, PropertyNumber, SYSVersion, BattLevel, Charging, QTDSalesNotSynced, QTDItemsNotSynced, ValSalesNotSynced');
    $criteria6->setProperty('order', 'QTDSalesNotSynced desc, BattLevel, PropertyNumber');
    
    $fields = array(
      "distinct CompanyName", 
      "PropertyNumber", 
      "SYSVersion", 
      "BattLevel", 
      "Charging", 
      "QTDSalesNotSynced", 
      "QTDItemsNotSynced",
      "ValSalesNotSynced", 
      "convert(varchar(10), max(DatetimeSaleRegister), 103) + ' ' + convert(varchar(10), max(DatetimeSaleRegister), 108) DatetimeSale"
    );
    
    //instancia um repositorio para usuário
    $repository = new TRepository('poscontrol','_list_sale_items_product_pgroup_pos_user');
    // retorna todos os objetos que satisfem o critério
    $saleitems = $repository->load($criteria6,true,$fields);
    return $saleitems;
  }

  function getTopSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany)
  {
    
    //DatetimeSale >= '" . $rtrEvent[$curr_evnt]["DatetimeBegin"] . "' and DatetimeSale <= '" . $rtrEvent[$curr_evnt]["DatetimeEnd"] . "'"
    
    //convert(char(13), convert(datetime, DatetimeSale), 120), convert(char(2), convert(time, DatetimeSale))

    $criteria = new TCriteria;
    $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
    $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
    if ($activeCompany == -1) {
      $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
    } else {
      $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
    }
    $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
    $criteria->setProperty('group', 'PropertyNumber, SerialNumber, SYSVersion');
    $criteria->setProperty('order', 'PropertyNumber');
    
    $fields = array(
      "PropertyNumber", 
      "SerialNumber", 
      "SYSVersion", 
      "count(distinct SalePosCodeID) QTSale", 
      "sum(AmntTotal) TotSale", 
      "DATEDIFF(second, max(DatetimeSale), convert(datetime, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))) DiffUltVend",
      "DATEDIFF(second, max(DatetimeSaleRegister), getdate()) DiffUltVendReg", 
      "convert(varchar(10), max(DatetimeSale), 103) + ' ' + convert(varchar(10), max(DatetimeSale), 108) LastSale"
    );
    
    //instancia um repositorio para usuário
    $repository = new TRepository('poscontrol','_list_sale_items_product_pgroup_pos_user');
    // retorna todos os objetos que satisfem o critério
    $saleitems = $repository->load($criteria,true,$fields);        
    return $saleitems;
  }

  function getTopSale1($Companies, $activeCompany)
  {
    
    //DatetimeSale >= '" . $rtrEvent[$curr_evnt]["DatetimeBegin"] . "' and DatetimeSale <= '" . $rtrEvent[$curr_evnt]["DatetimeEnd"] . "'"
    
    //convert(char(13), convert(datetime, DatetimeSale), 120), convert(char(2), convert(time, DatetimeSale))

    $criteria = new TCriteria;
    $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
    if ($activeCompany == -1) {
      $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
    } else {
      $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
    }
    $criteria->add(new TFilter('PropertyNumber', '<>', 'Property #'));
    $criteria->setProperty('group', 'PropertyNumber, SerialNumber, SYSVersion');
    $criteria->setProperty('order', 'PropertyNumber');
    
    $fields = array(
      "PropertyNumber", 
      "SerialNumber", 
      "SYSVersion", 
      "count(distinct SalePosCodeID) QTSale", 
      "sum(AmntTotal) TotSale", 
      "DATEDIFF(second, max(DatetimeSale), convert(datetime, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))) DiffUltVend",
      "DATEDIFF(second, max(DatetimeSaleRegister), getdate()) DiffUltVendReg", 
      "convert(varchar(10), max(DatetimeSale), 103) + ' ' + convert(varchar(10), max(DatetimeSale), 108) LastSale"
    );
    
    //instancia um repositorio para usuário
    $repository = new TRepository('poscontrol','_list_sale_items_product_pgroup_pos_user');
    // retorna todos os objetos que satisfem o critério
    $saleitems = $repository->load($criteria,true,$fields);        
    return $saleitems;
  }
}