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
            //TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrol.txt'), 'poscontrol');
            //TTransaction::setLoggerdb(new TLoggerTXT('c:\temp\poscontrolconfig.txt'),'poscontrolconfig');

            $activeCompany = $_GET["atv_cmp"];
            $dashboardType = $_GET["dsh_type"];
            $graphType = $_GET["graph_type"];

            $tbl = dashboardfaturamento($token, $activeCompany, $dashboardType, $graphType);

            TTransaction::closedb('poscontrolconfig');
            TTransaction::closedb('poscontrol'); 
            /*
            * return protected asset
            */
            header('Content-type: application/json');
            echo json_encode([        
              'menu'    => $MENU
            ]);
 
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

function dashboardfaturamento($token, $activeCompany, $dashboardType, $graphType)
{
  $User = $Token->token->data->UserId;
  $SysType = $Token->token->data->SYSType;
    
  $Companies = array();
  for ($i=0; $i < count($Token->token->data->Companies); $i++) {
    array_push($Companies,$Token->token->data->Companies[$i]->CompanyID);
  }
  
  $CompaniesQRYIN = "'" . implode("','", $Companies) . "'";
    
  $CompaniesQRY = "";
  for ($i=0; $i < count($Companies); $i++) {
      $CompaniesQRY .= " or CompanyID = '" . $Companies[$i] . "' ";
  }
  $CompaniesQRY = substr($CompaniesQRY, 6);

  $CompanyGroup = $Token->token->data->GrpId;

  if ($CompanyGroup == "OLDDE7125A0-42AA-BC19-099C-4525D8E09ED4"
  or $CompanyGroup == "5A0086E2-8DA3-358A-DDB5-31A7B5C29449"
  or $CompanyGroup == "TOPC9E0C809-94FF-A49C-834E-705E436D8BBA") {
      $SysType = "001_1";
  }

  if ($CompanyGroup == "EF3AD951-9029-8D49-DAED-CD187610FC25") {
    $SysType = "001_1";
  }

  if ($dashboardType !== ":1") {
    switch ($SysType) {
      case "003":
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() order by DatetimeBegin desc";
        $stmtEvent = $conn->query($sqlEvent);
        $rtrEvent = $stmtEvent->fetchAll(PDO::FETCH_ASSOC);
        if ($graphType < 10) {
            $curr_evnt = 0 + $graphType;
        } else {
            $curr_evnt = $graphType;
        }
        $varFilter = "  DatetimeSale >= '" . $rtrEvent[$curr_evnt]["DatetimeBegin"] . "' and DatetimeSale <= '" . $rtrEvent[$curr_evnt]["DatetimeEnd"] . "'";
        $varGroup = "convert(char(2), convert(time, DatetimeSale))";
        break;

      case "001_1":
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin desc";
        break;

      case "005":
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin desc";
        break;

      case "007":
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin desc";
        break;
      
      case "008":
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin desc";
        break;
      
      default:
        $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyID = '" . $activeCompany . "' order by DatetimeBegin desc";
        break;
    }
    $stmtEvent = $conn->query($sqlEvent);
    $rtrEvent = $stmtEvent->fetchAll(PDO::FETCH_ASSOC);
    switch ($graphType) {
      case '99':
        switch ($SysType) {
          case '001_1':
            $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin";
            break;
          
          case '007':
            $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyGroupID = '" . $CompanyGroup . "'  order by DatetimeBegin";
            break;
          
          default:
            $sqlEvent = "select * from Event where DatetimeBegin <= getdate() and CompanyID = '" . $activeCompany . "' order by DatetimeBegin";
            break;
        }
        $stmtEvent = $conn->query($sqlEvent);
        $rtrEvent = $stmtEvent->fetchAll(PDO::FETCH_ASSOC);
        $sql = "";
        for ($i=0;$i<count($rtrEvent);$i++) {
          if ($i > 0) {
              $sql .= " union ";
          }
          $sql .= "select";
          $sql .= "     '" . $rtrEvent[$i]["Name"] . "' XLine, sum(AmntTotal) AmntTotal";
          $sql .= " from";
          $sql .= "   _list_sale_items";
          $sql .= " where";
          if ($activeCompany == -1) {
              $sql .= "    CompanyID in (" . $currCompaniesQRYIN . ")";
          } else {
              $sql .= "    CompanyID = '" . $activeCompany . "'";
          }
          $sql .= " and " . "  DatetimeSale >= '" . $rtrEvent[$i]["DatetimeBegin"] . "' and DatetimeSale <= '" . $rtrEvent[$i]["DatetimeEnd"] . "'";
        }
        break;      
      default:
        if ($graphType < 10) {
          $curr_evnt = 0 + $graphType;
        }else {
          $curr_evnt = $graphType;
        }
        $varFilter = "  DatetimeSale >= '" . $rtrEvent[$curr_evnt]["DatetimeBegin"] . "' and DatetimeSale <= '" . $rtrEvent[$curr_evnt]["DatetimeEnd"] . "'";
        $varGroup = "convert(char(13), convert(datetime, DatetimeSale), 120), convert(char(2), convert(time, DatetimeSale))";
        break;
    }

  } else {
    switch ($graphType) {
      case 1:
        $varGroup = "convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)";
        $varFilter = "   DatetimeSale >= DATEADD(DAY, -9, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))";
        break;
      case 2:
        $varGroup = "convert(char(10), convert(date, DatetimeSale)), convert(varchar(5),CONVERT(date,DatetimeSale,106),103)";
        $varFilter = "   DatetimeSale >= DATEADD(DAY, -29, convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00')))";
        break;
      case 3:
        $varGroup = "convert(char(7), convert(date, DatetimeSale)), substring(convert(varchar(10),CONVERT(date,DatetimeSale,106),103), 4, 7)";
        $varFilter = "   DatetimeSale >= convert(char(4), switchoffset(SYSDATETIMEOFFSET(), '-03:00')) + '-01-01'";
        break;
      
      default:
        $varGroup = "convert(char(2), convert(time, DatetimeSale))";
        $varFilter = "   DatetimeSale >= convert(date, switchoffset(SYSDATETIMEOFFSET(), '-03:00'))";
        break;
    }
  }

  $tbl = array();
  if ($graphType <> 99) {
      $sql = "select";
      $sql .= "     " . $varGroup . " XLine, sum(AmntTotal) AmntTotal";
      $sql .= " from";
      $sql .= "   _list_sale_items";
      $sql .= " where";
      if ($SysType !== "003" and $SysType !== "999") {
          if ($activeCompany == -1) {
              $sql .= "    CompanyID in (" . $currCompaniesQRYIN . ")";
          } else {
              $sql .= "    CompanyID = '" . $activeCompany . "'";
          }
      } else {
          $sql .= "    0 = 0";
      }
      $sql .= " and " . $varFilter;

      $sql .= " group by";
      $sql .= "   " . $varGroup;
      $sql .= " order by";
      $sql .= "   " . $varGroup;
  }

  try {      
    $stmt = $conn->query($sql);
    $rtr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    for ($i = 0; $i < count($rtr); ++$i) {
      $tbl["dataProvider"][$i]["xline"] = $rtr[$i]["XLine"];
      $tbl["dataProvider"][$i]["income"] = number_format($rtr[$i]["AmntTotal"], 2, ".", "");
    }
  
    return $tbl;

  } catch( PDOException $Exception ) {
      throw new Exception( $Exception->getMessage(),$Exception->getCode());
  }

  
}

?>