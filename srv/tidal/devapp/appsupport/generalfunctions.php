<?php

function testerfunction() { 
return "RETURN THIS VALUE";
}

function checkCURL() { 
   if  (in_array  ('curl', get_loaded_extensions())) {
        return "CURL is available on your web server";
    }  else {
        return "CURL is not available on your web server";
    }
}

function checkOnline($domain) {
   $curlInit = curl_init($domain);
   curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
   curl_setopt($curlInit,CURLOPT_HEADER,true);
   curl_setopt($curlInit,CURLOPT_NOBODY,true);
   curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
   //get answer
   $response = curl_exec($curlInit);
   curl_close($curlInit);
   if ($response) return true;
   return false;
}

function getipinformation($ip) { 
    //{ "ip": "174.201.24.165", "hostname": "165.sub-174-201-24.myvzw.com", "city": "Philadelphia", "region": "Pennsylvania", "country": "US", "loc": "40.0343,-75.1213", "postal": "19120", "org": "AS22394 Cellco Partnership DBA Verizon Wireless" }
try { 
    $ws = json_encode(array("ip" => "", "hostname" => "", "city" => "", "region" => "", "country" => "", "loc" => "", "postal" => "", "org" => ""));
    if (checkOnline("http://ip-api.com")) { 
      //$ws = file_get_contents("http://ipinfo.io/{$ip}/json?token=6043850d53cbb6");
      $ws = file_get_contents("http://ip-api.com/json/{$ip}");
    } 
} catch (Exception $x) { 
}
   return $ws;
}

function tidalCommunication($method, $url, $user = "", $apikeyencrypt = "", $data = false) { 
    try {
    $ch = curl_init(); 
    if (FALSE === $ch) { return Exception('failed to initialize'); } 
    switch ($method) { 
      case "POST": 
        curl_setopt($ch, CURLOPT_POST, 1); 
        if ($data) { 
          curl_setopt($ch,CURLOPT_POSTFIELDS, $data); 
        }
      break; 
      case "GET": 
        curl_setopt($ch, CURLOPT_GET, 1); 
      break;
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $apikeyencrypt); //USING BASIC AUTH
    //$headers = array("api-token-user:{$user}","api-token-key:{$apikeyencrypt}");  //AUTHORIZATION HEADERS HERE NOT! USING BASIC AUTH
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (FALSE === $content) { 
      return Exception(curl_error($ch),curl_errno($ch));
    } else {
      return  array('responseCode' => $httpcode, 'content' => $content);
   }
   // return $httpcode . " ... " . $content;
  } catch(Exception $e) { 
    return sprintf('CURL failed with error #%d: %s', $e->getCode(), $e->getMessage()); 
  } 
  
  
}

function base64file($path, $identifier, $expectedObject, $createObject = true, $additionals = "") { 
  $object = NULL;    
  if (!file_exists($path) || !is_file($path)) {
  } else {
      ob_start(); 
      readfile($path);
      $filecontent = base64_encode(ob_get_clean()); 
    if ($createObject) { 
      $mime = mime_content_type($path);
      switch ($expectedObject) { 
        case "image": 
          $object = "<img id=\"{$identifier}\" src=\"data:{$mime};base64,{$filecontent}\" {$additionals}>";
        break;
        case "png":
          $object = "<img id=\"{$identifier}\" src=\"data:image/png;base64,{$filecontent}\" {$additionals}>";
        break;
        case "pdf":
          //NOT YET DONE
            $object = "<object style=\"width: 100%; height: 100%;\" data=\"data:application/pdf;base64,{$filecontent}\" type=\"application/pdf\" class=\"internal\" {$additionals} >  <embed  style=\"width: 100%; height: 100%;\" src=\"data:application/pdf;base64,{$filecontent}\"  type=\"application/pdf\" {$additionals} >";
        break;
        case "favicon": 
          $object = "<link href=\"data:image/x-icon;base64,{$filecontent}\" rel=\"icon\" type=\"image/x-icon\" {$additionals}>";
        break;
        case "js":
          $object = "<script type=\"text/javascript\" src=\"data:text/javascript;base64,{$filecontent}\" {$additionals}></script>";
          break;
        case "bgurl":
          $object = " url('data:{$mime};base64,{$filecontent}') ";
          break; 
        default:
          $object = "<img id=\"{$identifier}\" src=\"data:{$mime};base64,{$filecontent}\" {$additionals}>";
      } 
    } else { 
      $object = $filecontent;
    }
  }
  return $object;
}

function getlorem() { 
  $ws = file_get_contents('https://loripsum.net/api/20/long/headers');
  return $ws;        
}

function generaterandomstring($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function guidv4() {
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function cryptservice( $string, $action = 'e', $usedbkey = false, $passedsid = "") {

    $output = false;
    require( serverkeys . "/serverid.zck");
    if ($usedbkey) { 

        $sid = (trim($passedsid) === "") ? session_id() : $passedsid;

        require(serverkeys . "/sspdo.zck");
        session_start();   
        $sql = "select esecret from sys_ekeys where sessionkey = :sid";
        $rs = $conn->prepare($sql); 
        $rs->execute(array(':sid' => $sid));
        if ($rs->rowCount() < 1) { 
            exit(); 
        } else { 
          $r = $rs->fetch(PDO::FETCH_ASSOC);
        }
        $localsecretkey = $r['esecret']; 
        $secret_key = $localsecretkey;
        $secret_iv = $localsecretkey;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $localsecret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
        if ( $action == 'e' ) {
          $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        if( $action == 'd' ) {
          $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

    } else { 
      $secret_key = $secretkey;
      $secret_iv = $siv;
      $encrypt_method = "AES-256-CBC";
      $key = hash( 'sha256', $secret_key );
      $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
      if ( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
      }
      if( $action == 'd' ) {
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
      }
    }

    return $output;
}



/*
 * 
 
function checkCURL() {
   if  (in_array  ('curl', get_loaded_extensions())) {
        return "CURL is available on your web server";
    }  else {
        return "CURL is not available on your web server";
    }
}

*/
