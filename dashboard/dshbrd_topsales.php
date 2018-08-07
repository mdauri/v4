<?php
chdir(dirname(__DIR__));

require_once('vendor/autoload.php');

use Zend\Http\PhpEnvironment\Request;
use Firebase\JWT\JWT;
use app\TApp;

use ado\core\TTransaction;
use ado\core\TLoggerTXT;

use ado\model\TUsersRecord;
use ado\service\TSecFunctionalityService;
use ado\service\TSecFunctionalityAccessLevelService;
use ado\service\TEventService;
use ado\service\TListSaleItemPosService;

/*
 * Get all headers from the HTTP request
 */
$request = new Request();

$authHeader = $request->getHeader('authorization');
/*
  * Look for the 'authorization' header
  */
if ($authHeader) {
    /*
      * Extract the jwt from the Bearer
      */
    list($jwt) = sscanf( $authHeader->toString(), 'Authorization: Bearer %s');

    if ($jwt) {
      try {
        $app = TApp::open();
        $secretKey = base64_decode($app->JWT->key);
        $algorithm = $app->JWT->algorithm;
        $token = JWT::decode($jwt, $secretKey, [$algorithm]);
        if ($request->isGet()) {

          try
          {
            //inicia transação com o banco PosControlConfig
            TTransaction::opendb('poscontrolconfig');
            //inicia transação com o banco PosControl
            TTransaction::opendb('poscontrol');
            //define o arquivo de LOG
            TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrol.txt'), 'poscontrol');
            TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrolconfig.txt'),'poscontrolconfig');

            $activeCompany = $_GET["atv_cmp"];            
            $topsaleType = $_GET["top_sale_type"];
            $dashboardType = $_GET["dsh_type"];

            $tbl = dashboardtopsale($token, $activeCompany, $dashboardType, $topsaleType);

            TTransaction::closedb('poscontrolconfig');
            TTransaction::closedb('poscontrol'); 
            /*
            * return protected asset
            */
            header('Content-type: application/json');
            echo json_encode($tbl);
 
          }
          catch(Exception $e)
          {
               
            echo 'Erro ' . $e->getMessage();
            TTransaction::rollbackdb('poscontrol');
            TTransaction::rollbackdb('poscontrolconfig');
          }

        } else {
            header('HTTP/1.0 405 Method Not Allowed');
        }
      } catch (Exception $e) {
        header('HTTP/1.0 401 Token Expired');
      }        
    } else {
        /*
          * No token was able to be extracted from the authorization header
          */
        header('HTTP/1.0 400 Bad Request');
    }
} else {
    /*
      * The request lacks the authorization token
      */
    header('HTTP/1.0 400 Bad Request');
    echo 'Token not found in request';
}

function dashboardtopsale($token, $activeCompany, $dashboardType, $topsaleType)
{
  $User = $token->data->UserId;
  $SysType = $token->data->SYSType;
    
  $Companies = array();
  for ($i=0; $i < count($token->data->Companies); $i++) {
    array_push($Companies,$token->data->Companies[$i]->CompanyID);
  }
  
  $CompaniesQRYIN = "'" . implode("','", $Companies) . "'";
    
  $CompaniesQRY = "";
  for ($i=0; $i < count($Companies); $i++) {
      $CompaniesQRY .= " or CompanyID = '" . $Companies[$i] . "' ";
  }
  $CompaniesQRY = substr($CompaniesQRY, 6);

  $CompanyGroup = $token->data->GrpId;

  if ($CompanyGroup == "OLDDE7125A0-42AA-BC19-099C-4525D8E09ED4"
  or $CompanyGroup == "5A0086E2-8DA3-358A-DDB5-31A7B5C29449"
  or $CompanyGroup == "TOPC9E0C809-94FF-A49C-834E-705E436D8BBA") {
      $SysType = "001_1";
  }

  if ($CompanyGroup == "EF3AD951-9029-8D49-DAED-CD187610FC25") {
    $SysType = "001_1";
  }
  
  $listsaleitemposservice = new TListSaleItemPosService;
  if (isset($dashboardType) AND $dashboardType !== ":1") {
    $events = getEvents($dashboardType, $SysType, $topsaleType, $CompanyGroup, $activeCompany);
    if ($topsaleType < 10) {
      $curr_evnt = 0 + $topsaleType;
    } else {
      $curr_evnt = $topsaleType;
    }
    if ($topsaleType == 99) {
      $curr_evnt = count($events)-1;
    }    
    $event = $events[$curr_evnt];
    $DatetimeBegin = $events[$curr_evnt]->DatetimeBegin;
    $DatetimeEnd = $events[$curr_evnt]->DatetimeEnd;

    $rtrTotalSaleHD = $listsaleitemposservice->getListSaleItemPosTotalSaleHD($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd);
    $rtrTopSale     = $listsaleitemposservice->getListSaleItemTopSale($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd);
    $rtrTopSaleHD   = $listsaleitemposservice->getListSaleItemTopSaleHD($Companies, $activeCompany, $DatetimeBegin, $DatetimeEnd);

  } else {
    switch ($topsaleType) {
      case 1:
        $rtrTotalSaleHD = $listsaleitemposservice->getListSaleItemPosTotalSaleHD1($Companies, $activeCompany);
        $rtrTopSale     = $listsaleitemposservice->getListSaleItemTopSale1($Companies, $activeCompany);
        $rtrTopSaleHD   = $listsaleitemposservice->getListSaleItemTopSaleHD1($Companies, $activeCompany);
        break;
      case 2:
        $rtrTotalSaleHD = $listsaleitemposservice->getListSaleItemPosTotalSaleHD2($Companies, $activeCompany);
        $rtrTopSale     = $listsaleitemposservice->getListSaleItemTopSale2($Companies, $activeCompany);
        $rtrTopSaleHD   = $listsaleitemposservice->getListSaleItemTopSaleHD2($Companies, $activeCompany);
        break;
      case 3:
        $rtrTotalSaleHD = $listsaleitemposservice->getListSaleItemPosTotalSaleHD3($Companies, $activeCompany);
        $rtrTopSale     = $listsaleitemposservice->getListSaleItemTopSale3($Companies, $activeCompany);
        $rtrTopSaleHD   = $listsaleitemposservice->getListSaleItemTopSaleHD3($Companies, $activeCompany);
        break;      
      default:
        $rtrTotalSaleHD = $listsaleitemposservice->getListSaleItemPosTotalSaleHD4($Companies, $activeCompany);
        $rtrTopSale     = $listsaleitemposservice->getListSaleItemTopSale4($Companies, $activeCompany);
        $rtrTopSaleHD   = $listsaleitemposservice->getListSaleItemTopSaleHD4($Companies, $activeCompany);
        break;
    }
  }

  $tbl = array();

  $tbl["rtrevent"] = $rtrEvent;
  $tbl["rtrTopSale"] = $rtrTopSale;
  $tbl["rtrTopSaleHD"] = $rtrTopSaleHD;

  for ($i = 0; $i < count($rtrTopSale); ++$i) {
    $tbl["rtrTopSale"][$i]["AmntTotal"] = number_format($rtrTopSale[$i]["QtItem"], 2, ".", "");
    $tbl["rtrTopSale"][$i]["AmntTotal"] = number_format($rtrTopSale[$i]["AmntTotal"], 2, ".", "");
  }

  for ($i = 0; $i < count($rtrTopSaleHD); ++$i) {
    $tbl["rtrTopSaleHD"][$i]["AmntTotal"] = number_format($rtrTopSaleHD[$i]["AmntTotal"], 2, ".", "");
  }

  return $tbl;
}

function getEvents($dashboardType, $SysType, $topsaleType, $CompanyGroup, $activeCompany)
{
  $eventservice = new TEventService;
  if (isset($dashboardType) AND $dashboardType !== ":1") {
    switch ($SysType) {
      case "003":
        $events = $eventservice->getEventsbyDatetimeBegin();        
        break;

      case "001_1":
      //case "005":
      //case "007":      
      case "008":
        $events = $eventservice->getEventsbyDatetimeBeginCompanyGroup($CompanyGroup);
        break;
      
      default:
        $events = $eventservice->getEventsbyDatetimeBeginCompany($activeCompany);        
        break;
    }
    if ($topsaleType == '99') {
      switch ($SysType) {
        case '001_1':         
        case '007':            
          $events = $eventservice->getEventsbyDatetimeBeginCompanyGroupAsc($CompanyGroup);
          break;
        
        default:
          $events = $eventservice->getEventsbyDatetimeBeginCompanyAsc($activeCompany);        
          break;
      }
    }
  }
  return $events;
}
?>