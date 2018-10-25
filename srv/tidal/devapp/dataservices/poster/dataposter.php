<?php

class dataposters { 
  
  public $responseCode = 400;
  public $rtnData = "";
  
  function __construct() { 
    $args = func_get_args(); 
    $nbrofargs = func_num_args(); 
    if (trim($args[0]) === "") { 
    } else { 
     $request = explode("/", $args[0]); 
      if (trim($request[1]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING","ITEMSFOUND" => 0, "DATA" => ""));
      } else {  
        $dp = new $request[2](); 
        if (method_exists($dp, $request[3])) { 
          $funcName = trim($request[3]); 
          $dataReturned = $dp->$funcName($args[0], $args[1]); 
          $this->responseCode = $dataReturned['statusCode']; 
          $this->rtnData = json_encode($dataReturned['data']);
        } else { 
          $this->responseCode = 404; 
          $this->rtnData = json_encode(array("MESSAGE" => "END-POINT FUNCTION NOT FOUND: {$request[2]}","ITEMSFOUND" => 0, "DATA" => ""));
        }
      }
    }
  }


}

class systemposts { 
     
    function captureguest($rqst, $pdta) {
        $rspcode = 400;
        $dta = json_decode($pdta, true);
        require(serverkeys . "/sspdo.zck");
        try {
          $insSQL = "insert into tidal_webcapture.sys_webrequests(sessioncode, city, region, country, postalcode, ip, organization, device, requested, onwhen) "
                . "values (:sessioncode, :city, :region, :country, :postalcode, :ip, :organization, :device, :requested, now())";
          $rs = $conn->prepare($insSQL);
          $rs->execute(array(
           ':sessioncode' => $dta['session'] 
           ,':city' => $dta['city']
           ,':region' => $dta['state']
           ,':country' => $dta['country']     
           ,':postalcode' => $dta['postalcode']     
           ,':ip' => $dta['ip']
           ,':organization' => $dta['organization']
           ,':device' => $dta['platform']
          ,':requested' => $dta['request']                              
          ));
          $rspcode = 200;
        } catch (PDOException $ex) {
          $err = $ex->getMessage();
          $rspcode = 503;
      }
        return array('statusCode' => $rspcode, 'data' => array("session" => $dta['session']));        
    }

    function publicsystemkeys($rqst, $pdta) { 
        $rspcode = 400;
        $dta = json_decode($pdta, true);
        require(serverkeys . "/sspdo.zck");
        $esecret = generaterandomstring(15);
        if ($dta['sid']) {
        try { 
            $delSQL = "DELETE FROM tidal_webcapture.sys_ekeys where sessionkey = :sid"; 
            $delR = $conn->prepare($delSQL); 
            $delR->execute(array(':sid' => $dta['sid']));
            $insSQL = "INSERT into tidal_webcapture.sys_ekeys (sessionkey, esecret) values(:sid, :esecret)";
            $insR = $conn->prepare($insSQL); 
            $insR->execute(array(':sid' => $dta['sid'], ':esecret' => $esecret));
            $vid = $conn->lastInsertId();
            $rspcode = 200;
        } catch (PDOException $ex) {
          $err = $ex->getMessage();
          $rspcode = 503;
        }
        } else { 
          $rspcode = 400; 
        }
        return array('statusCode' => $rspcode, 'data' => array("vid" => $vid, "passedData" => $dta['sid']));        
    }
    
    function searchidexists($rqst, $pdta) { 
        $rspcode = 404;
        $dta = json_decode($pdta, true);
        require(serverkeys . "/sspdo.zck");
        $chkSQL = "select searchjson from tidal_webcapture.tbl_searchrequests where (searchjson is not null and trim(ifnull(searchjson,'')) <> '') and requestid = :requestid";
        $rs = $conn->prepare($chkSQL); 
        $rs->execute(array(':requestid' => $dta['searchid']));
        if ($rs->rowCount() < 1) { 
        } else { 
          $rspcode = 200;
        }
        return array('statusCode' => $rspcode, 'data' => array("rtn"=>$dta['searchid']));        
    }
   
    function mastertidalsearch($rqst, $pdta) { 
        $rspcode = 404;
        $dta = json_decode($pdta, true);
        require(serverkeys . "/sspdo.zck");
        $chkSQL = "select searchjson from tidal_webcapture.tbl_searchrequests where (searchjson is not null and trim(ifnull(searchjson,'')) <> '') and requestid = :requestid";
        $rs = $conn->prepare($chkSQL); 
        $rs->execute(array(':requestid' => $dta['searchid']));
        if ($rs->rowCount() < 1) { 
        } else { 
          $srch = $rs->fetch(PDO::FETCH_ASSOC); 
          $rtnDta = runmastersearch($srch['searchjson']);
          $rspcode = 200;
        }
        return array('statusCode' => $rspcode, 'data' => array("rtn" => $rtnDta));        
    }

    function recordlogin($rqst, $pdta) {
        //THIS IS A TESTING ONLY LOGIN METHOD!!!  DO NOT USE IN PRODUCTION!!!! 
        $rspcode = 404;
        $dta = json_decode($pdta, true);
        require(serverkeys . "/sspdo.zck");
        $rtnDta = $dta['acctname'];
        $chkSQL = "select acctid from tidal_webcapture.base_accounts where acctname = :acctname";
        $rs = $conn->prepare($chkSQL); 
        $rs->execute(array(':acctname' => $dta['acctname']));
        $r = $rs->fetch(PDO::FETCH_ASSOC);
        $updSQL = "update tidal_webcapture.base_accounts set lastLogDate = now() where acctid = :acctid";
        $updR = $conn->prepare($updSQL); 
        $updR->execute(array(':acctid' => $r['acctid']));
        $sysUpdSQL = "update tidal_webcapture.sys_acctlogin set activeind = 0 where acctid = :acctid";
        $sysUpdR = $conn->prepare($sysUpdSQL); 
        $sysUpdR->execute(array(':acctid' => $r['acctid']));
        $insSQL = "insert into tidal_webcapture.sys_acctlogin(sessionid, acctid, loggedOn, activeind) value(:sessid, :acctid ,now() ,1)";
        $insR = $conn->prepare($insSQL); 
        $insR->execute(array(':sessid' => $dta['sessid'], ':acctid' => $r['acctid']));
        $rspcode = 200;
        return array('statusCode' => $rspcode, 'data' => array("rtn" => $r['acctid']));        
    }

}


function runmastersearch($searchjson) { 
  require(serverkeys . "/sspdo.zck");
  $data = array();

  
  $rqstArr = json_decode($searchjson, true);
  
  if (array_key_exists('SrvList',$rqstArr)) { 
      if ($rqstArr['SrvList'] === "All Divisions") { 
        $getServiceList = "select identifiercode,servicename,requesttype,endpoint,credentialuser, credentialpassword, authencode, samplebody, headerspecificjson as hdrs from tidal_webcapture.sys_registeredservices where activeind = 1";
        $getServiceR = $conn->prepare($getServiceList);
        $getServiceR->execute();
      } else { 
        $getServiceList = "select identifiercode,servicename,requesttype,endpoint,credentialuser, credentialpassword, authencode, samplebody, headerspecificjson as hdrs from tidal_webcapture.sys_registeredservices where activeind = 1 and servicename = :srvname";
        $getServiceR = $conn->prepare($getServiceList);
        $getServiceR->execute(array(':srvname' => $rqstArr['SrvList']));
      }
  } else { 
      $getServiceList = "select identifiercode,servicename,requesttype,endpoint,credentialuser, credentialpassword, authencode, samplebody, headerspecificjson as hdrs from tidal_webcapture.sys_registeredservices where activeind = 1";
      $getServiceR = $conn->prepare($getServiceList);
      $getServiceR->execute();
  }


  if ($getServiceR->rowCount() < 1) {
     //ERROR - NO SERVICES FOUND 
  } else { 
    $srvcCntr = 0;  
    while ($srvc = $getServiceR->fetch(PDO::FETCH_ASSOC)) { 
      $data[$srvcCntr]['servicename'] = $srvc['servicename'];
      $data[$srvcCntr]['identifiercode'] = $srvc['identifiercode'];  
      $data[$srvcCntr]['requesttype'] = $srvc['requesttype'];
      $data[$srvcCntr]['endpoint'] = $srvc['endpoint']; 
      $data[$srvcCntr]['credentialuser'] = $srvc['credentialuser'];  
      $data[$srvcCntr]['credentialpassword'] = $srvc['credentialpassword'];  
      $data[$srvcCntr]['authencode'] = $srvc['authencode'];  
      $data[$srvcCntr]['samplebody'] = $srvc['samplebody'];  
      $data[$srvcCntr]['headrs'] = $srvc['hdrs'];  
      $srvcCntr++;
    }
    $r = multiRequest($data, $searchjson); 
  }
return $r;
}

function multiRequest($data, $rqst) { 
$rqstArr = json_decode($rqst, true);
foreach ($rqstArr as $arrKey => $arrVal) { 
    if (substr($arrKey,0,3) === "prp" && (int)$arrVal === 1) {
        $prpListing[] = substr($arrKey,3); 
    }
}
$st = "\"{$rqstArr['Site']}\"";
$dx = "\"{$rqstArr['Diagnosis']}\"";
$sp = "\"{$rqstArr['SpecimenCategory']}\"";
$result = array(); 
$mh = curl_multi_init(); 
$srvcCnt = 1;
foreach ($data as $key => $value) {

  //TODO:ERROR CHECK-MAKE SURE ENDPOINT SERVICE IS AVAILABLE BEFORE ADDING TO THE CURL_MULTI 
    
  $rqstStr = $value['samplebody']; 
  $rplc = array("#PRPLIST#","#SITE#","#DX#","#SPECCAT#","#PAGE#"); 
  $wth  = array(json_encode($prpListing),$st,$dx,strtoupper($sp),0);
  $newRqstStr = str_replace($rplc, $wth, $rqstStr);
  ${"curly_{$srvcCnt}"} = curl_init();
  curl_setopt(${"curly_{$srvcCnt}"}, CURLOPT_URL, $value['endpoint']);
  curl_setopt(${"curly_{$srvcCnt}"}, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt(${"curly_{$srvcCnt}"}, CURLOPT_POSTFIELDS, $newRqstStr);
  curl_setopt(${"curly_{$srvcCnt}"}, CURLOPT_POST, 1);
  $headers = array();
  $headrs = json_decode($value['headrs'], true);
  foreach ($headrs as $headval) { 
    $headers[] = $headval; 
  } 
  curl_setopt(${"curly_{$srvcCnt}"}, CURLOPT_HTTPHEADER, $headers);
  curl_multi_add_handle($mh,${"curly_{$srvcCnt}"});
  $srvcCnt++;
}
$running = null;
do {  
  curl_multi_exec($mh, $running);
} while ($running > 0);
curl_multi_close($mh);

for ($i = 1; $i < $srvcCnt; $i++) {
  curl_multi_remove_handle($mh, ${"curly_{$i}"});
  $result[$i] = curl_multi_getcontent(${"curly_{$i}"});
}
$srvcCnt--;
return $result;
//return json_encode(curl_multi_info_read($mh));
//$info = curl_getinfo($curly_1);
}




