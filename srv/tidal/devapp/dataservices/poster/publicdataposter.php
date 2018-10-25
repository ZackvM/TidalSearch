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
      if (trim($request[2]) === "") { 
        $this->responseCode = 400; 
        $this->rtnData = json_encode(array("MESSAGE" => "DATA NAME MISSING","ITEMSFOUND" => 0, "DATA" => json_encode($request)));
      } else {  

        $dp = new publicends(); 
        if (method_exists($dp, $request[2])) { 
          $funcName = trim($request[2]); 
          $dataReturned = $dp->$funcName($args[0], $args[1], $args[2]); 
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
     
    function submitsearch($rqst, $pdta, $session) {
      $rspcode = 400;
      $dta = json_decode($pdta, true);
      $itemsfound = NULL; 
      $rtndata = NULL;
      $message = "";
      $allow = 1;
      if (trim($dta['Site']) === "" && trim($dta['Diagnosis']) === "") { 
         $message .= "\r\n- You must specify either a Site or a Diagnosis";
         $allow = 0;    
      }     

      if (trim($dta['Site']) !== "" && (trim($dta['Diagnosis']) === "" && trim($dta['SpecimenCategory']) === "")) { 
         $message .= "\r\n- With an Anatomic Site, you must also specify a Specimen Category and/or a Diagnosis";
         $allow = 0;    
      }

      if (trim($dta['Site']) !== "" && strlen(trim($dta['Site'])) < 4) { 
         $message .= "\r\n- With an Anatomic Site, provide more than 3 characters";
         $allow = 0;    
      }

      if (trim($dta['Diagnosis']) !== "" && strlen(trim($dta['Diagnosis'])) < 4) { 
         $message .= "\r\n- When querying a diagnosis, provide more than 3 characters";
         $allow = 0;    
      }

      if (trim($dta['SrvList']) === "") {  
         $message .= "\r\n- A CHTN Division must be specified";
         $allow = 0;    
      } 

      $prepSpecified = 0;
      foreach($dta as $ky =>  $val) {
          if (substr($ky,0,3) === "prp") {   
              if ((int)$val === 1) { 
                 $prepSpecified = 1;
              }
        }
      }
      if ($prepSpecified === 0) { 
          $message .= "\r\n- You must specify a preparation type";
          $allow = 0;     
      }
      if (trim($session) === "") { 
        $message .= "\r\n\- There was a system error.  Please refresh the page and try again.";
        $allow = 0;
      }
      if ($allow === 1) {
        require(serverkeys . "/sspdo.zck");
        //SAVE REQUEST
        $ident = str_replace("-","",strtolower(guidv4())); 
        $insSQL = "insert into tidal_webcapture.tbl_searchrequests(requestid, sessionid, onwhen, searchJSON) values(:requestid, :sessionid, now(), :searchjson)";
        $rs = $conn->prepare($insSQL);  
        $rs->bindParam(':requestid', $ident, PDO::PARAM_STR);
        $rs->bindParam(':sessionid', $session, PDO::PARAM_STR);
        $rs->bindParam(':searchjson', json_encode($dta), PDO::PARAM_STR);
        if ($rs->execute()) { 
            //true
           $rspcode = 200;
           $itemsfound = 0;
           $message = "";
           $rtndata = $ident;
        } else { 
           //ERROR WITH DATABASE INSERT
        }
      }
      return array('statusCode' => $rspcode, 'data' => array("MESSAGE" => $message, "ITEMSFOUND" => $itemsfound, "DATA" => $rtndata));   
    }
    
       
}


