<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

final class TListSaleItemPosCortesiaService {

    function getListSaleItemPosTotalSaleHD($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $fields = array(
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemPosTotalSaleHD1($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        $fields = array(
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemPosTotalSaleHD2($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -29, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        $fields = array(
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemPosTotalSaleHD3($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(char(4), switchoffset(SYSDATETIMEOFFSET(), '-03:00')) + '-01-01'"));
        $fields = array(
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemPosTotalSaleHD4($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        $fields = array(
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSale($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        $criteria->setProperty('group', '_list_sale_items_pos_cortesia.ProductID, ProductName, ImageBase64');
        $criteria->setProperty('order', 'sum(AmntTotal) desc');


        $fields = array(
          "_list_sale_items_pos_cortesia.ProductID",
          "ProductName",
          "ImageBase64",
          "count(distinct SaleID) Qt",
          "sum(Quantity) QtItem", 
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria, true, $fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSale1($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        $criteria->setProperty('group', '_list_sale_items_pos_cortesia.ProductID, ProductName, ImageBase64');
        $criteria->setProperty('order', 'sum(AmntTotal) desc');


        $fields = array(
          "_list_sale_items_pos_cortesia.ProductID",
          "ProductName",
          "ImageBase64",
          "count(distinct SaleID) Qt",
          "sum(Quantity) QtItem", 
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria, true, $fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSale2($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -29, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        $criteria->setProperty('group', '_list_sale_items_pos_cortesia.ProductID, ProductName, ImageBase64');
        $criteria->setProperty('order', 'sum(AmntTotal) desc');


        $fields = array(
          "_list_sale_items_pos_cortesia.ProductID",
          "ProductName", 
          "ImageBase64",
          "count(distinct SaleID) Qt",
          "sum(Quantity) QtItem", 
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria, true, $fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSale3($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(char(4), switchoffset(SYSDATETIMEOFFSET(), '-03:00')) + '-01-01'"));
        $criteria->setProperty('group', '_list_sale_items_pos_cortesia.ProductID, ProductName, ImageBase64');
        $criteria->setProperty('order', 'sum(AmntTotal) desc');

        $fields = array(
          "_list_sale_items_pos_cortesia.ProductID",
          "ProductName", 
          "ImageBase64",
          "count(distinct SaleID) Qt",
          "sum(Quantity) QtItem", 
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria, true, $fields);
        return $saleitems;
        
    }
    
    function getListSaleItemTopSale4($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        $criteria->setProperty('group', '_list_sale_items_pos_cortesia.ProductID, ProductName, ImageBase64');
        $criteria->setProperty('order', 'sum(AmntTotal) desc');

        $fields = array(
          "_list_sale_items_pos_cortesia.ProductID",
          "ProductName",
          "ImageBase64",
          "count(distinct SaleID) Qt",
          "sum(Quantity) QtItem", 
          "sum(AmntTotal) AmntTotal"
        );

        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria, true, $fields);
        return $saleitems;
        
    }
    
    function getListSaleItemTopSaleHD($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', $DatetimeBegin));
        $criteria->add(new TFilter('DatetimeSale', '<=', $DatetimeEnd));
        
        $fields = array(
          "count(distinct SaleID) Qt",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSaleHD1($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        
        $fields = array(
          "count(distinct SaleID) Qt",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSaleHD2($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$DATEADD' . "(DAY, -29, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))"));
        
        $fields = array(
          "count(distinct SaleID) Qt",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSaleHD3($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(char(4), switchoffset(SYSDATETIMEOFFSET(), '-03:00')) + '-01-01'"));
        
        $fields = array(
          "count(distinct SaleID) Qt",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }

    function getListSaleItemTopSaleHD4($Companies, $activeCompany)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        if ($activeCompany == -1) {
          $criteria->add(new TFilter('CompanyID', 'IN', $Companies));
        } else {
          $criteria->add(new TFilter('CompanyID', '=', $activeCompany));
        }
        $criteria->add(new TFilter('DatetimeSale', '>=', '$CONVERT' . "(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))"));
        
        $fields = array(
          "count(distinct SaleID) Qt",
          "sum(AmntTotal) AmntTotal"
        );
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrol','_list_sale_items_pos_cortesia');
        // retorna todos os objetos que satisfem o critério
        $saleitems = $repository->load($criteria,true,$fields);
        return $saleitems;
        
    }
}
?>