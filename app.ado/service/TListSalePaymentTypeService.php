<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;
use ado\model\CompanyRecord;

final class TListSalePaymentTypeService {

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
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
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
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }
    
    function getListSaleItems1($SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $criteria->setProperty('group', 'convert(char(2), convert(time, DatetimeSale))');
        $criteria->setProperty('order', 'convert(char(2), convert(time, DatetimeSale))');
        $fields = array(
          "convert(char(2), convert(time, DatetimeSale))" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems2($SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT'. "(DATETIME,'" . $DatetimeBegin . "',120)"));
        $criteria->add(new TFilter('DatetimeSale', '<=', '$CONVERT'. "(DATETIME,'" . $DatetimeEnd . "',120)"));
        $criteria->setProperty('group', 'PaymentTypeName');
        $criteria->setProperty('order', 'PaymentTypeName');
        $fields = array(
          "PaymentTypeName" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems3($SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD'."(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $criteria->setProperty('order', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $fields = array(
          "convert(char(10), convert(date, DatetimeSale))",
          "convert(varchar(5),CONVERT(date,DatetimeSale,106),103)" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems4($Event, $SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD'."(DAY, -29, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $criteria->setProperty('order', 'convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)');
        $fields = array(
          "convert(char(10), convert(date, DatetimeSale))",
          "convert(varchar(5),CONVERT(date,DatetimeSale,106),103)" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems5($Event, $SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT'."((char(4), switchoffset(SYSDATETIMEOFFSET(), '-03:00')) + '-01-01'"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group', 'convert(char(7), convert(date, DatetimeSale)), substring(convert(varchar(10),CONVERT(date,DatetimeSale,106),103), 4, 7)');
        $criteria->setProperty('order', 'convert(char(7), convert(date, DatetimeSale)), substring(convert(varchar(10),CONVERT(date,DatetimeSale,106),103), 4, 7)');
        $fields = array(
          "convert(char(7), convert(date, DatetimeSale))",
          "substring(convert(varchar(10),CONVERT(date,DatetimeSale,106),103), 4, 7)" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);        
        return $saleitems;
        
    }

    function getListSaleItems6($Event, $SysType, $Companies, $Company, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT'."(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        if ($SysType !== "003" and $SysType !== "999") {
          if ($Company == -1) {
            $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
          } else {
            $criteria->add(new TFilter('CompanyID', '=', $Company));
          }
        }

        $criteria->setProperty('group', 'convert(char(2), convert(time, DatetimeSale))');
        $criteria->setProperty('order', 'convert(char(2), convert(time, DatetimeSale))');
        $fields = array(
          "convert(char(2), convert(time, DatetimeSale))" . " XLine",
          "sum(AmntTotal) AmntTotal"
        );
        
        $objCompany = new CompanyRecord('poscontrolconfig','CompanyID', $activeCompany);
        
        //instancia um repositorio para usuário
        if ($objCompany->NFCE) {
          $repository = new TRepository('poscontrol','_list_nfc_company_pos_payment_type');
        } else {
          $repository = new TRepository('poscontrol','_list_sale_paymenttype');
        }
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }
    
}
?>