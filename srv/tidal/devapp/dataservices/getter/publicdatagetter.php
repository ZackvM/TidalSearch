<?php


class datagetters { 
  public $responseCode = 400;
  public $rtnData = "";
  
  function __construct() { 
    $args = func_get_args(); 
    $nbrofargs = func_num_args(); 
    if (trim($args[0]) === "") { 
    } else { 
      $request = explode("/", $args[0]); 
      if (trim($request[2]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING","ITEMSFOUND" => 0, "DATA" => json_encode($request)));
      } else {  

        $dp = new publicends(); 
        if (method_exists($dp, $request[2])) { 
          $funcName = trim($request[2]); 
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

class publicends { 

    function servicelist($rqst, $usr) { 
        $rspcode = 400;
        $itemsfound = 0; 
        $message = "";
        $rtndata = array();
        $rqstArr = explode("/",$rqst);
        require(serverkeys . "/sspdo.zck");
        $regSQL = "select identifiercode, servicename from tidal_webcapture.sys_registeredservices where activeind = 1";
        $regR = $conn->prepare($regSQL); 
        $regR->execute();
        $itemsfound = $regR->rowCount();
        while ($r = $regR->fetch(PDO::FETCH_ASSOC)) { 
            $rtndata[] = $r; 
        } 
        $rspcode = 200;
        return array('statusCode' => $rspcode, 'data' => array("MESSAGE" => $message, "ITEMSFOUND" => $itemsfound, "DATA" => $rtndata));
    }

    function logininformation($rqst, $usr) {
        ///THIS IS FOR TESTING PURPOSES ONLY!!! 
        //TAKE OUT FOR PRODUCTION 
        $rspcode = 400;
        $itemsfound = 0; 
        $message = "";
        $rtndata = array();
        $rqstArr = explode("/",$rqst);
        require(serverkeys . "/sspdo.zck");
        $acctSQL = "select lg.acctid, ac.fname, ac.lname, ac.salutation, ac.institution, ac.acctname from tidal_webcapture.sys_acctlogin lg left join tidal_webcapture.base_accounts ac on lg.acctid = ac.acctid where sessionid = :sessid and activeind = 1";
        $acctR = $conn->prepare($acctSQL); 
        $acctR->execute(array(':sessid' => $rqstArr[3]));
        $rtndata = $acctR->fetch(PDO::FETCH_ASSOC);
        $rspcode = 200;
        return array('statusCode' => $rspcode, 'data' => array("MESSAGE" => $message, "ITEMSFOUND" => $itemsfound, "DATA" => $rtndata));
        //THIS IS FOR TESTING ONLY
        //TAKE OUT FOR PRODUCTION
    }

    function divisionalcontact($rqst, $usr) { 
        $rspcode = 400;
        $itemsfound = 0; 
        $message = "";
        $rtndata = array();
        $rqstArr = explode("/",$rqst);
        if (trim($rqstArr[3]) === "") {
           $message = "NO DIVISIONAL IDENTIFIER CODE SPECIFIED"; 
        } else { 
          require(serverkeys . "/sspdo.zck");
          $contactSQL = "select name_first_last, officephone, officeemail, divisionalhtmldisplay, webaddress from tidal_webcapture.tbl_divisionalcontacts where maincontactind = 1 and identifiercode = :divisionalcode";
          $rs = $conn->prepare($contactSQL);
          $rs->execute(array(':divisionalcode' => $rqstArr[3]));
          if ($rs->rowCount() < 1) { 
            $rspcode = 404; 
            $message = "NO CONTACT FOUND FOR DIVISIONAL CODE";
          } else {
            $rspcode = 200;
            $itemsfound = 1;
            $rtndata = $rs->fetch(PDO::FETCH_ASSOC);
          } 
        }
        return array('statusCode' => $rspcode, 'data' => array("MESSAGE" => $message, "ITEMSFOUND" => $itemsfound, "DATA" => $rtndata));
    }

    function menus($rqst, $usr) { 
        $rspcode = 400;
        $itemsfound = 0; 
        $message = "";
        $rtndata = array();  
        $rqstArr = explode("/",$rqst);
        if (trim($rqstArr[3]) === "") {
           $message = "NO MENU SPECIFIED"; 
        } else { 

            $mnubldr = new menubuilder(); 
            $rqstmenu = trim($rqstArr[3]);
            if (method_exists($mnubldr,$rqstmenu)) { 
              $criteria = $mnubldr->$rqstmenu();
              require(serverkeys . "/sspdo.zck");
              $mnuSQL = "select ifnull(mvalue,'') as menuvalue, ifnull(mdisplay,'') as menudisplay, ifnull(additional,'') as additional from tidal_webcapture.base_menus where dspind = 1 and menuname = :criteria order by morder";
              $rs = $conn->prepare($mnuSQL);
              $rs->execute(array(':criteria' => $criteria));
              if ($rs->rowCount() < 1) { 
                $message = "NO MENU ITEMS FOUND";
              } else {
                $itemsfound = $rs->rowCount();
                while ($r = $rs->fetch(PDO::FETCH_ASSOC)) { 
                  $rtndata[] = $r;
                }
                $rspcode = 200;
              }
            } else { 
              $rspcode = 404;  
              $message = "SPECIFIED MENU NOT FOUND";
            }
        }
        return array('statusCode' => $rspcode, 'data' => array("MESSAGE" => $message, "ITEMSFOUND" => $itemsfound, "DATA" => $rtndata));
    }

}

class menubuilder { 

    function specimencategory() {  
      $SQL = "SPECIMENCATEGORY";
      return $SQL;
    }


}

