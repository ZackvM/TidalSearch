# CHTN Tidal Search

BASIC DOCUMENTATION (15 October 2018)
---------------------------------------------------------------------------------------------------------------------------------------------
THIS IS THE CONSUMER APP FOR THE CHTN TRANSIENT INVENTORY (TI).  EACH DIVISION OF THE CHTN OFFERS A RESTful WEBSERVICE END-POINT AT WHICH TO QUERY THAT DIVISION'S TRANSIENT INVENTORY.  THE CONSUMER APP SENDS A STANDARD JSON STRING WITHIN THE BODY OF THE COMMUNICATION AS A REQUEST.  THE JSON STRING IS FORMATTED AS:

```
{"requester": "","requestedDataPage": 0,"requestedSite": "adrenal","requestedDiagnosis": "","requestedCategory": "MALIGNANT","requestedPreparation": ["PB" ]i
```

THE SERVICE SHOULD RETURN A RECORDSET WITH THE FOLLOWING BASIC JSON FORMAT.  SEE THE WEBSERVICE SWAGGER DOCUMENT AT: 
```
https://www.chtnmidwest.work/api-doc/#/default/controller_transientinventory
```

**EACH DIVISIONS WEBSERVICE IS SUPPORTED BY THAT INDIVIDUAL DIVISION.  ANY CHANGES TO A SERVICE SHOULD BE REPORTED TO THE CHTN-EASTERN DIVISION.  THE TI APPLICATION IS PRIMARY SUPPORTED BY THE CHTN-EASTERN DIVISION.**

OTHER DOCUMENTATION FOR THIS PROJECT CAN BE FOUND AT:
```
https://github.com/ZackvM/tidalsearch/wiki
``` 

THIS VERSION OF THE TRANSIENT INVENTORY CONSUMER APPLICATION (TIDAL) COMES WITH A VERY BASIC GUI.  IT IS PROVIDED PRIMARILY TO TEST PHP'S CURL_MULTI_EXEC FOR SYNCHRONOUS QUERIES OF ALL REGISTERED SERVICES WITHIN THE CHTN'S TRANSIENT INVENTORY DOMAINS.  AS OF THE ABOVE DATE, THERE ARE THREE SERVICES WHICH HAVE BEEN CONFIRMED THROUGH THIS SERVICE (EASTERN/WESTERN/MIDWESTERN). EASTERN'S AND WESTERN'S SERVICES ARE LIVE DATA, WHILE THE MIDWESTERN'S DATA IS A SNAP-SHOT. PHP'S CURL_MULTI HAS BEEN TESTED TO PULL DATA FROM ALL THREE SERVICES (INCLUDING PATHOLOGY REPORT TEXT FROM WESTERN).  THE LONGEST PROCESSING TIME WAS FOUND TO BE 25.4 SECS. TIDAL ADDS UP TO 3-5 SECONDS TO PROCESS THE DATA ARRAYS.   

THE APPLICATIONS CODE STRUCTURE IS BELOW:

var/www/dev.chtn.science/public_html 
  (represents the development iteration of the application. The development application can be accessed at https://dev.chtn.science The Apache webserver needs to be configured to direct ALL traffic to the index.php file. [see setting up the server environment at the github page listed above].)  The index.php file uses code in the srv/devapp directtory to build the application.  

var/www/chtn.science/public_html
  (represents the production site for https://www.chtn.science and https://chtn.science  --- it is a direct copy of the development site in structure. The production code can be found in srv/webapp ... see below for a listing of directories and their functions.  

All passwords are listed in directories:  srv/dataconn and srv/devdataconn respectively.   These files contain files for accessing the underlying data source, as well as identifying requests made from THIS server. They are NOT included in this repository!!!! If you are replicating this application on your own server you must create these files.  
 

THE DIRECTORY STRUCTURE 
---------------------------------------------------------------------------------------------------------------------------------------------

/srv/devapp = applicationTree - Can be changed if application files are moved
   +----/srv/devapp/appsupport 
         (Directory of basic php application functions to support the application but can be used in other applications as well) 
   +----/srv/devapp/builders 
         (HTML document builders for the GUI.  There exists classes in the basic files: tidalbuilder.php for web/mtidalbuilder.php for mobile platforms.  Other classes for CSS/JAVASCRIPT/CONTENT/and DEFAULTELEMENTS exist in named php files)
   +----/srv/devdataconn 
         (directory for data connection strings - Only to be used by PHP files under the applicationTree)
   +----/srv/devapp/dataservices/poster
         (directory for the webservice data for both the application and the GUI.  For POST methods. All returns are JSON)
   +----/srv/devapp/dataservices/getter
         (directory for webservices that are GET method.  Not used in this release.  All returns will be JSON).
   +----/srv/devapp/developmentnotes
         (directory just to keep notes on the development process - has noting to do with the application runtime)
   +----/srv/devapp/extlibs
         (External Libraries) 
   +----/srv/devapp/objects
         (Physical files - e.i. graphics/pdfs etc. - There is a function in the appsupport to pull these as base64 strings).     
     
   (THERE ARE DATABASE CONNECTIONS IN GETTER/POSTER/GENERALFUNCTIONS THERE SHOULD BE NO DATABASE CONNECTS ANYWHERE ELSE.  NO DATABASE CONNECTIONS SHOULD EXIST IN THE GUI FILES.  IN THIS WAY MULTIPLE SKINS CAN BE BUILT ON TOP OF THE DATA APPLICATION.)

CHANGE LOG/APPLICATION INTENT 
---------------------------------------------------------------------------------------------------------------------------------------------
 
   - SWITCHED FROM THE USE OF ONE-AT-A-TIME CURL PROCESSING TO PHPs CURL_MULTI_EXEC FOR SYNCHRONOUS PROCESSING (FOR SPEED)
   - BUILT NEW DIRECTORY STRUCTURE TO REFLECT DEVELOPMENT/PRODUCTION ENVIRONMENTS
   - SIMPLIFIED TRACKING TO TRACK BY SESSION CODE AND REQUEST - CAN BE EXPANDED FOR MARKETING EFFORTS.  
   - ADDED DOCUMENTATION AND GIT REPOSITORY
 
OTHER NOTES
---------------------------------------------------------------------------------------------------------------------------------------------
   SEE https://github.com/ZackvM/tidalsearch/wiki FOR MORE INFORMATION ESPECIALLY CONCERNING THE DATA STRUCTURE.  THIS DOCUMENTATION IS ALSO AT THAT WIKI LOCATION.  

   (GENERAL INFORMATIONAL NOTE:  ZACK USES VIM AS AN IDE SO THERE MAY BE .sw* (eg: *.swo) WITH IN THE GIT REPO.  THESE FILES HAVE NOTHING TO DO WITH THE APPLICATION DEVELOPMENT - IGNORE THEM IF THEY "SNEAK IN" *GRIN*) 
 
   ZACHERYV@PENNMEDICINE.UPENN.EDU
