<?php 
/*
 * BASIC DOCUMENTATION (15 October 2018)
 * ---------------------------------------------------------------------------------------------------------------------------------------------
 * THIS IS THE CONSUMER APP FOR THE CHTN TRANSIENT INVENTORY (TI).  EACH DIVISION OF THE CHTN OFFERS A RESTful WEBSERVICE END-POINT AT WHICH TO 
 * QUERY THAT DIVISION'S TRANSIENT INVENTORY.  THE CONSUMER APP SENDS A STANDARD JSON STRING WITHIN THE BODY OF THE COMMUNICATION AS A REQUEST.  
 * THE JSON STRING IS FORMATTED AS:
 *    {"requester": "","requestedDataPage": 0,"requestedSite": "adrenal","requestedDiagnosis": "","requestedCategory": "MALIGNANT","requestedPreparation": ["PB" ]}
 *
 * THE SERVICE SHOULD RETURN A RECORDSET WITH THE FOLLOWING BASIC JSON FORMAT.  SEE THE WEBSERVICE SWAGGER DOCUMENT AT: 
 *    https://www.chtnmidwest.work/api-doc/#/default/controller_transientinventory
 *
 * OTHER DOCUMENTATION FOR THIS PROJECT CAN BE FOUND AT:
 *    https://github.com/ZackvM/tidalsearch/wiki
 *
 * THIS VERSION OF THE TRANSIENT INVENTORY CONSUMER APPLICATION (TIDAL) COMES WITH A VERY BASIC GUI.  IT IS PROVIDED PRIMARILY TO TEST PHP'S CURL_MULTI_EXEC
 * FOR SYNCHRONOUS QUERIES OF ALL REGISTERED SERVICES WITHIN THE CHTN'S TRANSIENT INVENTORY DOMAINS.  AS OF THE ABOVE DATE, THERE ARE THREE SERVICES WHICH 
 * HAVE BEEN CONFIRMED THROUGH THIS SERVICE (EASTERN/WESTERN/MIDWESTERN). EASTERN'S AND WESTERN'S SERVICES ARE LIVE DATA, WHILE THE MIDWESTERN'S DATA 
 * IS A SNAP-SHOT. PHP'S CURL_MULTI HAS BEEN TESTED TO PULL DATA FROM ALL THREE SERVICES (INCLUDING PATHOLOGY REPORT TEXT FROM WESTERN).  
 * THE LONGEST PROCESSING TIME WAS FOUND TO BE 25.4 SECS. TIDAL ADDS UP TO 3-5 SECONDS TO PROCESS THE DATA ARRAYS.   
 *
 * THE APPLICATIONS CODE STRUCTURE IS BELOW:
 *
 * var/www/dev.chtn.science/public_html 
 *    (represents the development iteration of the application. The development application can be accessed at https://dev.chtn.science 
 *    The Apache webserver needs to be configured to direct ALL traffic to the index.php file. [see setting up the server environment at the github page 
 *    listed above].)  The index.php file uses code in the srv/devapp directtory to build the application.  
 *
 * var/www/chtn.science/public_html
 *    (represents the production site for https://www.chtn.science and https://chtn.science  --- it is a direct copy of the development site in structure.
 *    The production code can be found in srv/webapp ... see below for a listing of directories and their functions.  
 *
 *    All passwords are listed in directories:  srv/dataconn and srv/devdataconn respectively.   These files contain files for accessing the underlying 
 *    data source, as well as identifying requests made from THIS server. They are NOT included in this repository!!!! If you are replicating 
 *    this application on your own server you must create these files.  
 *
 *
 * THE DIRECTORY STRUCTURE 
 * ---------------------------------------------------------------------------------------------------------------------------------------------
 *
 * /srv/devapp = applicationTree - Can be changed if application files are moved
 *    +----/srv/devapp/appsupport 
 *         (Directory of basic php application functions to support the application but can be used in other applications as well) 
 *    +----/srv/devapp/builders 
 *         (HTML document builders for the GUI.  There exists classes in the basic files: tidalbuilder.php for web/mtidalbuilder.php for mobile platforms.  
 *         Other classes for CSS/JAVASCRIPT/CONTENT/and DEFAULTELEMENTS exist in named php files
 *    +----/srv/devdataconn 
 *         (directory for data connection strings - Only to be used by PHP files under the applicationTree)
 *    +----/srv/devapp/dataservices/poster
 *         (directory for the webservice data for both the application and the GUI.  For POST methods. All returns are JSON)
 *    +----/srv/devapp/dataservices/getter
 *         (directory for webservices that are GET method.  Not used in this release.  All returns will be JSON).
 *    +----/srv/devapp/developmentnotes
 *         (directory just to keep notes on the development process - has noting to do with the application runtime)
 *    +----/srv/devapp/extlibs
 *         (External Libraries) 
 *    +----/srv/devapp/objects
 *         (Physical files - e.i. graphics/pdfs etc. - There is a function in the appsupport to pull these as base64 strings).     
 *    
 *    (THERE ARE DATABASE CONNECTIONS IN GETTER/POSTER/GENERALFUNCTIONS THERE SHOULD BE NO DATABASE CONNECTS ANYWHERE ELSE.  NO DATABASE
 *    CONNECTIONS SHOULD EXIST IN THE GUI FILES.  IN THIS WAY MULTIPLE SKINS CAN BE BUILT ON TOP OF THE DATA APPLICATION.)
 *
 * CHANGE LOG/APPLICATION INTENT 
 * ---------------------------------------------------------------------------------------------------------------------------------------------
 *
 *   - SWITCHED FROM THE USE OF ONE-AT-A-TIME CURL PROCESSING TO PHPs CURL_MULTI_EXEC FOR SYNCHRONOUS PROCESSING (FOR SPEED)
 *   - BUILT NEW DIRECTORY STRUCTURE TO REFLECT DEVELOPMENT/PRODUCTION ENVIRONMENTS
 *   - SIMPLIFIED TRACKING TO TRACK BY SESSION CODE AND REQUEST - CAN BE EXPANDED FOR MARKETING EFFORTS.  
 *   - ADDED DOCUMENTATION AND GIT REPOSITORY
 *
 *  OTHER NOTES
 * ---------------------------------------------------------------------------------------------------------------------------------------------
 *  SEE https://github.com/ZackvM/tidalsearch/wiki FOR MORE INFORMATION ESPECIALLY CONCERNING THE DATA STRUCTURE.  THIS DOCUMENTATION IS ALSO AT 
 *  THAT WIKI LOCATION.  
 *
 *  (GENERAL INFORMATIONAL NOTE:  ZACK USES VIM AS AN IDE SO THERE MAY BE .sw* (eg: *.swo) WITH IN THE GIT REPO.  THESE FILES HAVE NOTHING TO DO WITH THE 
 *  APPLICATION DEVELOPMENT - IGNORE THEM IF THEY "SNEAK IN" *GRIN*) 
 *
 *  ZACHERYV@PENNMEDICINE.UPENN.EDU
 *
 *
 */

//START SESSSION FOR ALL TRACKING 
session_start(); 
//DEFINE APPLICATION PATH PARAMETERS
define("uriPath","dev.chtn.science");                      //DEVELOPMENT VALUE - CHANGE IN PRODUCTION
define("treeTop","https://dev.chtn.science");        //DEVELOPMENT VALUE - CHANGE IN PRODUCTION
define("treeTopSuper","dev.chtn.science");            //DEVELOPMENT VALUE - CHANGE IN PRODUCTION
define("applicationTree","/srv/tidal/devapp");      //DEVELOPMENT PATH
//define("applicationTree","/srv/tidal/webapp");  //PRODUCTION PATH
define("applicationRoot","/srv/tidal");
define("serverkeys","/srv/tidal/devdataconn");        //DEVELOPMENT SERVER PASSWORDS
//define("serverkeys","/srv/tidal/dataconn");          //PRODUCTION SERVER PASSWORDS

//SERVER SECURITY FILE & GENERALFUNCTIONS
require_once(serverkeys . "/serverid.zck");
require_once(applicationTree . "/appsupport/generalfunctions.php");
define("serverident",$serverid);
define("servertrupw", $serverpw);
define("serverpw", cryptservice($serverpw));
define("obscurecode",$publicobscure);

//DEFINE THE REQUEST PARAMETERS
$requesterIP = $_SERVER['REMOTE_ADDR']; 
$method = $_SERVER['REQUEST_METHOD'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$originalRequest = str_replace("-","", strtolower($_SERVER['REQUEST_URI']));
$request = explode("/",str_replace("-","", strtolower($_SERVER['REQUEST_URI']))); 






/****************************
 * TODO:THIS IS FOR TESTING LOGON FUNCTIONALITY ONLY - REMOVE IN PRODUCTION
 * THESE LINES ALLOW TESTING OF LOGGED IN FUNCTALITY WITHOUT HAVING TO HAVE AN ACCOUNT BUILT SECTION
 * REMOVE REMOVE REMOVE!!! 
 *
 */
if ($request[1] === "l3t1tg0") {
   
    $_SESSION['loggedon'] = 1;
    $logArr = json_encode(array('sessid' => session_id(),'acctname' => 'zacheryv@mail.med.upenn.edu'));
    $rsponse = tidalCommunication("POST","https://dev.chtn.science/data-service/system-posts/record-login",serverident,serverpw,$logArr);

    if ((int)$rsponse['responseCode'] === 200) {
      header('location: https://dev.chtn.science');
    }
   exit();
}
/***************************
 *TODO:THIS IS FOR TESTING LOGON FUNCTIONALITY ONLY - REMOVE ABOVE LINES IN PRODUCTION
 */






//BEGIN BUILDING SERVICE
if ($request[1] === "dataservice") { 
    //DATA SERVICES TO GET AND POST DATA NO RELATED TO DATA PAGES

    switch ($method) { 
         case "POST":
            $authuser = $_SERVER['PHP_AUTH_USER']; 
            $authpw = cryptservice( $_SERVER['PHP_AUTH_PW'] , 'd');

            //SYSTEM ID 
            if ($authuser === serverident && $authpw === servertrupw) { 
              $postedData = file_get_contents('php://input');
              $passedPayLoad = "";
              if (trim($postedData) !== "") { 
                $passedPayLoad = trim($postedData);
              } 
              require(applicationTree . "/dataservices/poster/dataposter.php");
              $doer = new dataposters($originalRequest, $passedPayLoad);
              $responseCode = $doer->responseCode; 
              $data = $doer->rtnData;  
              header('Content-type: application/json; charset=utf8');
              header('Access-Control-Allow-Origin: *'); 
              header('Access-Control-Allow-Header: Origin, X-Requested-With, Content-Type, Accept');
              header('Access-Control-Max-Age: 3628800'); 
              header('Access-Control-Allow-Methods: POST');
              http_response_code($responseCode);
              echo $data;
              exit(); 
            }

          //PUBLIC ID
          $authuser = explode("-", $authuser);
          if ($authuser[0] === "publicuser") { 
            //CHECK PASSWORD
              $publicuser = $authuser[1];
              $publicpw = cryptservice( $_SERVER['PHP_AUTH_PW'], 'd', true, $publicuser );  
              if ("{$publicuser}::{$publicobscure}" === "{$publicpw}") {  
                $postedData = file_get_contents('php://input');
                $passedPayLoad = "";
                if (trim($postedData) !== "") { 
                  $passedPayLoad = trim($postedData);
                } 
                require(applicationTree . "/dataservices/poster/publicdataposter.php");
                $doer = new dataposters($originalRequest, $passedPayLoad, $authuser[1]);
                $responseCode = $doer->responseCode; 
                $data = $doer->rtnData;  
                header('Content-type: application/json; charset=utf8');
                header('Access-Control-Allow-Origin: *'); 
                header('Access-Control-Allow-Header: Origin, X-Requested-With, Content-Type, Accept');
                header('Access-Control-Max-Age: 3628800'); 
                header('Access-Control-Allow-Methods: POST');
                http_response_code($responseCode);
                echo $data;
                exit(); 
              } 
          }
          
          http_response_code(401);
          header('HTTP/1.0 401 Unauthorized');
          echo "UNAUTHORIZED REQUEST.  WILL BE TRACKED (IP ADDRESS: {$requesterIP} {$publicpw})";
          //TODO:WRITE UNAUTH TRACKER HERE

         break; 
         case "GET":

            $authuser = $_SERVER['PHP_AUTH_USER']; 
            $authpw = cryptservice( $_SERVER['PHP_AUTH_PW'] , 'd');

            //PUBLIC ID
            $allow = 0;
            if ($authuser === serverident) {
              //Request from the server 
              $allow = 1;
            } else {  
              //CHECK PASSWORD
              $authuser = explode("-", $authuser);
              $publicuser = $authuser[1];
              $publicpw = cryptservice( $_SERVER['PHP_AUTH_PW'], 'd', true, $publicuser ); 
              if ("{$publicuser}::{$publicobscure}" === "{$publicpw}") {
                $allow = 1;
              } 
            }
  
          if (($authuser[0] === "publicuser") || ( $authuser === serverident && $authpw === servertrupw  )) { 
              if ($allow === 1) {  

                require(applicationTree . "/dataservices/getter/publicdatagetter.php");
                $doer = new datagetters($originalRequest, $authuser[1]);
                $responseCode = $doer->responseCode; 
                $data = $doer->rtnData;  
                header('Content-type: application/json; charset=utf8');
                header('Access-Control-Allow-Origin: *'); 
                header('Access-Control-Allow-Header: Origin, X-Requested-With, Content-Type, Accept');
                header('Access-Control-Max-Age: 3628800'); 
                header('Access-Control-Allow-Methods: GET');
                http_response_code($responseCode);
                echo $data;
                exit();


              }
          }
          
          http_response_code(401);
          header('HTTP/1.0 401 Unauthorized');
          echo "UNAUTHORIZED REQUEST.  WILL BE TRACKED (IP ADDRESS: {$requesterIP} {$publicpw})";
          //TODO:WRITE UNAUTH TRACKER HERE

         break;
      default:
          http_response_code(405);
          echo "ONLY GET/POST METHODS ARE ALLOWED IN THIS DATA SERVICE";
          exit();
    }    
    
} else { 

   if ($method === "GET") {    
     require_once(applicationTree . "/extlib/detectmobilelibrary.php");
     //USE IPINFO TO GET ACCESSOR'S SPECIFICS
     $gst = array();
     $ipinfo = json_decode(getipinformation($requesterIP),true);
     //DETECT PLATFORM ON WHICH THE ACCESSOR IS VIEWING SITE
     $detect = new Mobile_Detect(); 
     $mobilePlatform = false; 
     $mobilePrefix = "w";
     if ($detect->isMobile()) { 
      $mobilePlatform = true; 
      $mobilePrefix = "m";
     }
     $gst['session'] = session_id(); 
     $gst['city'] = $ipinfo['city']; 
     $gst['state'] = $ipinfo['region']; 
     $gst['country'] = $ipinfo['country']; 
     $gst['postalcode'] = $ipinfo['postal'];
     $gst['ip'] = $requesterIP;
     $gst['organization'] = $ipinfo['org'];
     $gst['platform'] = $mobilePrefix;
     $gst['request'] = $originalRequest;
     $rsponse = tidalCommunication("POST","https://dev.chtn.science/data-service/system-posts/capture-guest",serverident,serverpw,json_encode($gst));
     if ((int)$rsponse['responseCode'] !== 200) { 
         echo "CAPTURE FUNCTION RETURNED BAD RESPONSE";
         exit();
     }

     //BUILD PAGE
     if ($mobilePrefix === "m") {
         // "Mobile";
         if (file_exists(applicationTree . "/builders/mtidalbuilder.php")) { 
           include (applicationTree . "/builders/mtidalbuilder.php");         
         } else { 
             echo "SYSTEM ERROR:  WE APOLOGIZE FOR THE INCONVIENCE.  PLEASE EMAIL zacheryv@pennmedicine.upenn.edu TO REPORT THE ERROR"; 
             exit();
         }
     } else { 
         //echo "DeskTop";
         if (file_exists(applicationTree . "/builders/tidalbuilder.php")) { 
            include (applicationTree . "/builders/tidalbuilder.php");
         } else { 
             echo "SYSTEM ERROR:  WE APOLOGIZE FOR THE INCONVIENCE.  PLEASE EMAIL zacheryv@pennmedicine.upenn.edu TO REPORT THE ERROR"; 
             exit();             
         }
     }
     $obj = $request[1];    
     if (trim($obj) === "") $obj = "home";

     $pageBld = new pagebuilder($obj, $request);
     if ((int)$pageBld->statusCode <> 200) {
         http_response_code($pageBld->statusCode);
    $rt = <<<RTNTHIS
<!DOCTYPE html>
<html>
<head>
<title>PAGE NOT FOUND</title>
</head>
<body><h1>Requested Page ({$obj}) @ Within Tidal Search  Not Found!</h1>
</body></html>
RTNTHIS;
     } else { 

    http_response_code($pageBld->statusCode);    
    $pgIcon = (trim($pageBld->pagetitleicon) !== "") ? $pageBld->pagetitleicon : "";
    $pgHead = (trim($pageBld->headr) !== "") ? $pageBld->headr : "";
    $pgTitle = (trim($pageBld->pagetitle) !== "") ? "<title>" . $pageBld->pagetitle . "</title>" : "<title>CHTN Eastern</title>";
    $pgStyle = (trim($pageBld->stylr) !== "") ? "<style>" . $pageBld->stylr . "\n</style>" :  "";
    $pgScriptr = (trim($pageBld->scriptrs) !== "") ? "<script lang=javascript>" . $pageBld->scriptrs . "</script>" : "";
    $pgBody = $pageBld->bodycontent;
    $pgMenu = $pageBld->menucontent;
    $pgModal = $pageBld->modalrs;
    $pgDialogs = $pageBld->modalrdialogs;
    $rt = <<<RTNTHIS
<!DOCTYPE html>
<html>
<head>
{$pgIcon}            
{$pgHead}
{$pgTitle}
{$pgStyle}
{$pgScriptr}
</head>
<body>
{$pgBody}
{$pgMenu}
{$pgModal}
{$pgDialogs}
</body>
</html>
RTNTHIS;
     }
     echo $rt;     
} else { 
    http_response_code(405);
    echo "ONLY GET/POST METHODS ARE ALLOWED AT THIS END-POINT";
    exit();
}
}
