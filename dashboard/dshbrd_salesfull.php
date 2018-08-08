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
use ado\service\TListSaleItemProductPGroupPosUserService;
use ado\service\TClosedDrawService;
use ado\service\TOpenDrawService;

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
            $topsaleType = $_GET["top_sale_type"];

            $tbl = dashboardsalesfull($token, $activeCompany, $dashboardType, $topsaleType);

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

function dashboardsalesfull($token, $activeCompany, $dashboardType, $topsaleType)
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
  
  //$listsaleitemservice = new TListSaleItemService;
  
  $saleitemproductpgroupposuserservice = new TListSaleItemProductPGroupPosUserService;
  $closeddrawservice = new TClosedDrawService;
  $opendrawservice = new TOpenDrawService;
  $events = getEvents($SysType, $topsaleType, $CompanyGroup, $activeCompany);
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

  switch ($SysType) {
    case '007':
    case '001_1':
    case '005':
    case '008':
      $rtrTopSale = $saleitemproductpgroupposuserservice->getTopSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopClosedSale = $closeddrawservice->getTopClosedSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopOpenSale = $opendrawservice->getTopOpenSale($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopClosedSale1 = $closeddrawservice->getTopClosedSale1($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      break;
    
    default:
      $rtrTopSale = $saleitemproductpgroupposuserservice->getTopSale1($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopClosedSale = $closeddrawservice->getTopClosedSale2($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopOpenSale = $opendrawservice->getTopOpenSale1($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      $rtrTopClosedSale1 = $closeddrawservice->getTopClosedSale3($DatetimeBegin, $DatetimeEnd, $Companies, $activeCompany);
      break;
  }

  for ($a = 0; $a < count($rtrTopClosedSale); ++$a) {
    $ClosedSale[$rtrTopClosedSale[$a]["PropertyNumber"]] = $rtrTopClosedSale[$a]["QTClosedSale"];
  }

  $ClosedSale1 = getClosedSale1($rtrTopClosedSale1);

  $qtFechamento = 0;
  
  for ($i = 0; $i < count($rtrTopSale); ++$i) {
      $currFechamento = 0;    
      if (isset($ClosedSale1[$rtrTopSale[$i]["PropertyNumber"]])) {
        $currFechamento = $ClosedSaleTot[$rtrTopSale[$i]["PropertyNumber"]];
        $qtFechamento++;
      }
      
      if (isset($currFechamento)){
        $rtrTopSale[$i]['currFechamento'] = $currFechamento;
      }
      
  }
      
  $tbl = array();

  $tbl["qtFechamento"] = count($rtrTopClosedSale);
  $tbl["rtrTopClosedSale"] = $rtrTopClosedSale;
  $tbl["rtrTopOpenSale"] = $rtrTopOpenSale;
  $tbl["rtrTopClosedSale1"] = $rtrTopClosedSale1;
  $tbl["rtrTopSale"] = $rtrTopSale;

  for ($i = 0; $i < count($rtrTopOpenSale); ++$i) {
    $tbl["rtrTopOpenSale"][$i]["Val"] = number_format($rtrTotalSale[$i]["Val"], 2, ".", "");
  }

  for ($i = 0; $i < count($rtrTopClosedSale1); ++$i) {
    $tbl["rtrTopClosedSale1"][$i]["ValSent"] = number_format($rtrTopClosedSale1[$i]["ValSent"], 2, ".", "");
    $tbl["rtrTopClosedSale1"][$i]["ValNSent"] = number_format($rtrTopClosedSale1[$i]["ValNSent"], 2, ".", "");
    $tbl["rtrTopClosedSale1"][$i]["ValTotal"] = number_format(($rtrTopClosedSale1[$i]["ValNSent"]+$rtrTopClosedSale1[$i]["ValSent"]), 2, ".", "");
  }

  for ($i = 0; $i < count($rtrTopSale); ++$i) {
    $tbl["rtrTopSale"][$i]["TotSale"] = number_format($rtrTopSale[$i]["TotSale"], 2, ".", "");
    $tbl["rtrTopSale"][$i]["currFechamento"] = number_format($rtrTopSale[$i]["currFechamento"], 2, ".", "");
  }

  return $tbl;

  
}

function getEvents($SysType, $topsaleType, $CompanyGroup, $activeCompany)
{
  $eventservice = new TEventService;

  switch ($SysType) {
    case "007":
    case "001_1":
      $events = $eventservice->getEventsbyDatetimeBeginCompanyGroup($CompanyGroup);
      break;
    
    case "005":          
    case "008":
      if ($CompanyGroup == "EF3AD951-9029-8D49-DAED-CD187610FC25") 
      {
        $events = $eventservice->getEventsbyDatetimeBeginCompanyGroup($CompanyGroup);
      } else {
        $events = $eventservice->getEventsbyDatetimeBeginCompany($activeCompany);        
      }
      break;
  }

  return $events;
}

function getClosedSale1($rtrTopClosedSale1)
{
  $curr_POS = "00000";
  $tmp = "";
  $ds = 1;
  $currQT_Vendas = 0;
  $currQT_Val = 0;
  $currQT_ValSent = 0;
  $currQT_ValNsent = 0;
  for ($b = 0; $b < count($rtrTopClosedSale1); ++$b) {
      if ($curr_POS <> $rtrTopClosedSale1[$b]["PropertyNumber"] and $curr_POS <> "00000") {
          if ($b <> 0){
              $tmp .= "Total --> Qtd Vendas: " . $currQT_Vendas . " - Faturamento: R$" . number_format($currQT_Val, 2, ',', '') . "\\n";
              $tmp .= "Total: R$" . number_format($currQT_ValSent, 2, ',', '');
              $tmp .= " + R$" . number_format($currQT_ValNsent, 2, ',', '');
              $tmp .= " = R$" . number_format($currQT_Val, 2, ',', '') . "\\n";
              $tmp .= "\\n Enviadas + Não Enviadas = Total\\n\\n";
              $ClosedSaleTot[$curr_POS] = $currQT_Val;
              $currQT_Vendas = 0;
              $currQT_Val = 0;
              $currQT_ValSent = 0;
              $currQT_ValNsent = 0;
          }
          $ClosedSale1[$curr_POS] = $tmp;
          $tmp = "";
          $ds = 1;
      }
      $tmp .= "F" . str_pad($ds, 2, "0", STR_PAD_LEFT) . ": R$" . number_format($rtrTopClosedSale1[$b]["ValSent"], 2, ',', '');
      $tmp .= " + R$" . number_format($rtrTopClosedSale1[$b]["ValNSent"], 2, ',', '');
      $tmp .= " = R$" . number_format($rtrTopClosedSale1[$b]["ValSent"]+$rtrTopClosedSale1[$b]["ValNSent"], 2, ',', '');
      $tmp .= " --> " . $rtrTopClosedSale1[$b]["DatetimeClosedDraw"] . "\\n";
      $curr_POS = $rtrTopClosedSale1[$b]["PropertyNumber"];
      $currQT_Vendas += $rtrTopClosedSale1[$b]["QT"];
      $currQT_ValSent += $rtrTopClosedSale1[$b]["ValSent"];
      $currQT_ValNsent += $rtrTopClosedSale1[$b]["ValNSent"];
      $currQT_Val += $rtrTopClosedSale1[$b]["ValNSent"]+$rtrTopClosedSale1[$b]["ValSent"];      
      $ds++;
  }
  $tmp .= "Total --> Qtd Vendas: " . $currQT_Vendas . " - Faturamento: R$" . number_format($currQT_Val, 2, ',', '') . "\\n";
  $tmp .= "Total: R$" . number_format($currQT_Val, 2, ',', '') . "\\n";
  $tmp .= "Total: R$" . number_format($currQT_ValSent, 2, ',', '');
  $tmp .= " + R$" . number_format($currQT_ValNsent, 2, ',', '');
  $tmp .= " = R$" . number_format($currQT_Val, 2, ',', '') . "\\n";

  $tmp .= "\\n Enviadas + Não Enviadas = Total\\n\\n";
  $ClosedSaleTot[$curr_POS] = $currQT_Val;
  $currQT_Vendas = 0;
  $currQT_Val = 0;
  if ($curr_POS <> "00000") {
      $ClosedSale1[$curr_POS] = $tmp;
  }

  return $ClosedSale1;
}
?>