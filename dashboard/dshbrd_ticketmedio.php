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
use ado\service\TListSaleItemTicketMedioService;

/*
 * Get all headers from the HTTP request
 */
$request = new Request();

if ($request->isOptions()) {
  //$this->header = header('HTTP/1.0 200 OK');
  header('HTTP/1.0 200 OK');
  return;
}

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
            $dashboardType = $_GET["dsh_type"];
            $graphType = $_GET["graph_type"];

            $tbl = dashboardticketmedio($token, $activeCompany, $dashboardType, $graphType);

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

function dashboardticketmedio($token, $activeCompany, $dashboardType, $graphType)
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
  
  $listsaleitemticketmedioservice = new TListSaleItemTicketMedioService;
  if (isset($dashboardType) and $dashboardType !== ":1") {
    $events = getEvents($dashboardType, $SysType, $graphType, $CompanyGroup, $activeCompany);
    if ($graphType < 10) {
      $curr_evnt = 0 + $graphType;
    } else {
        $curr_evnt = $graphType;
    }
    if ($graphType == 99) {
      $curr_evnt = count($events)-1;
    }
    $event = $events[$curr_evnt];
    $DatetimeBegin = $events[$curr_evnt]->DatetimeBegin;
    $DatetimeEnd = $events[$curr_evnt]->DatetimeEnd;
    if ($SysType == "003") {            
      $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems1($event, $SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
    } else {
      $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems2($SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
    }
  } else {
    switch ($graphType) {
      case 1:
        $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems3($SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
        break;
      case 2:
        $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems4($SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
        break;
      case 3:
        $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems5($SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
        break;
      default:
        $listsaleitems = $listsaleitemticketmedioservice->getListSaleItems5($SysType, $CompaniesQRYIN, $activeCompany, $DatetimeBegin, $DatetimeEnd);
        break;
    }
  }

  $tbl = array();
  if ($graphType == 99) {
    foreach ($events as $event) {
      if ($activeCompany == -1) {
        $listsaleitems[] = $listsaleitemticketmedioservice->getListSaleItemsActiveCompany($activeCompany, $event->Name, $event->DatetimeBegin, $event->DatetimeEnd);
      } else {  
        $listsaleitems[] = $listsaleitemticketmedioservice->getListSaleItemsCompanies($currCompaniesQRYIN, $event->Name, $event->DatetimeBegin, $event->DatetimeEnd);
      }
    }
  }  

  try {          

    for ($i = 0; $i < count($listsaleitems); ++$i) {
      $tbl["dataProvider"][$i]["xline"] = $listsaleitems[$i]["XLine"];
      $tbl["dataProvider"][$i]["income"] = number_format($listsaleitems[$i]["AmntTotal"], 2, ".", "");

      if ($listsaleitems[$i]["XLine"] == "31/07") {
        for ($a = 1; $a <= $i; $a++) {
            $tbl["dataProvider"][$a]["xline"] = $listsaleitems[$a-1]["XLine"];
            $tbl["dataProvider"][$a]["income"] = number_format($listsaleitems[$a-1]["TktMedio"], 2, ".", "");
        }
        $tbl["dataProvider"][0]["xline"] = $listsaleitems[$i]["XLine"];
        $tbl["dataProvider"][0]["income"] = number_format($listsaleitems[$i]["TktMedio"], 2, ".", "");
      } else {
          $tbl["dataProvider"][$i]["xline"] = $listsaleitems[$i]["XLine"];
          $tbl["dataProvider"][$i]["income"] = number_format($listsaleitems[$i]["TktMedio"], 2, ".", "");
      }
    }  
    return $tbl;

  } catch( PDOException $Exception ) {
      throw new Exception( $Exception->getMessage(),$Exception->getCode());
  }
  
}

function getEvents($dashboardType, $SysType, $graphType, $CompanyGroup, $activeCompany)
{
  $eventservice = new TEventService;
  if ($dashboardType !== ":1") {
    switch ($SysType) {
      case "003":
        $events = $eventservice->getEventsbyDatetimeBegin();        
        break;

      case "001_1":      
      case "008":
        $events = $eventservice->getEventsbyDatetimeBeginCompanyGroup($CompanyGroup);
        break;
      
      default:
        $events = $eventservice->getEventsbyDatetimeBeginCompany($activeCompany);        
        break;
    }
    if ($graphType == '99') {
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