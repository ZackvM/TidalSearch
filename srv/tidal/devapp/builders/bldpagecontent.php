<?php

class pagecontent {

public $maximizeBtn = "<i class=\"material-icons\">keyboard_arrow_up</i>";
public $minimizeBtn = "<i class=\"material-icons\">keyboard_arrow_down</i>"; 
public $closeBtn = "<i class=\"material-icons\">close</i>";
public $menuBtn = "<i class=\"material-icons\">menu</i>";
public $checkBtn = "<i class=\"material-icons\">check</i>";

  function generateHeader($whichpage) {
    $tt = treeTop;      
    $rtnThis = <<<STANDARDHEAD
<!-- <META http-equiv="refresh" content="0;URL={$tt}"> //-->
<!-- IDENTIFICATION: {$tt}/{$whichpage} //-->

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="chrome=1">
<meta http-equiv="refresh" content="28800">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script lang=javascript>
var identification = '{$tt}/{$whichpage}';
</script>
STANDARDHEAD;
    return $rtnThis;
  }    

  function home($rqstStr) {      
    $tt = treeTop;      
    $atree = applicationTree; 
    $mainLogo = base64file("{$atree}/objects/graphics/chtn_trans.png","homeTopLogo","png",true); 
    $rtnthis = <<<PAGEHERE

<div id=logoholder><center>{$mainLogo}</div>
<div id=maincontent>
  <table border=0 width=100%>
    <tr>
      <td id=introText>

  <h3>CHTN Transient Inventory Data Access Launchpad (TIDAL)</h3>
  Thank you for using the Cooperative Human Tissue Network's (CHTN) <b> Transient Inventory Data Access Launchpad (TIDAL)</b> Application. The CHTN is a prospective procurement service that assists the scientific community in obtaining biosamples for research. Even though all projects utilizing the CHTN must be prospective procurement in nature, all CHTN divisions have transient inventory on hand. This tool allows CHTN Investigators to conduct searches on the transient inventories held at CHTN locations through the CHTN's federated database.  To receive biosamples from the CHTN you must have an active, accepted protocol with a CHTN division. If you are not already a CHTN Investigator, you can apply to the CHTN by downloading the <a href="https://www.chtn.org/d/chtn-application.pdf" target="_new">application here</a> or by contacting the CHTN (440) 477-5952.
<p>After conducting a TIDAL search, you can request biosample(s) in which you are interested (You must have an active protocol and the biosamples must be verified for use before receipt of any biosamples).  

</td></tr>
<tr><td><center><button onclick="navigateSite('search');" class=smallButton>Get Started!</button></td></tr>
</table>
</div>
PAGEHERE;
    return $rtnthis;
  }

  function search($rqststr) { 
  
      $rtnthis = <<<PAGEHERE

<table border=0 id=tblQueryCriteria>
<tr><td colspan=3>Instructions: </td></tr>
<tr>
  <td class=generalFieldLabel>Site (anatomic)</td>
  <td class=generalFieldLabel>Diagnosis</td>
  <td class=generalFieldLabel>Specimen Category</td>
</tr>
<tr>
  <td><input type=text id=fldSite class=generalInputField></td>
  <td><input type=text id=fldDiagnosis class=generalInputField></td>
  <td><input type=text id=fldSpecimenCategory class=generalInputField></td>
</tr>
<tr><td colspan=3><center>
            <table>
               <tr>
                 <td><input type=checkbox id=prpFFPE value="PB" CHECKED><label for=prpFFPE>FFPE</label></td>
                 <td><input type=checkbox id=prpFIXED value="FIXED" CHECKED><label for=prpFIXED>FIXED</label></td>
                 <td><input type=checkbox id=prpFROZEN value="FROZEN" CHECKED><label for=prpFROZEN>FROZEN</label></td>
               </tr>
            </table>
      </td>
</tr>
<!-- TODO: ADD IN SEARCH ONE DIVISION ONLY //-->
<tr><td colspan=3 align=right><input type=button id=btnRequest value="Search"></td></tr>
</table>

PAGEHERE;
return $rtnthis;
  } 

  function searchresults($rqststr) { 
      if (trim($rqststr[2]) === "") { 
        $rtnthis = "<h1>Search ID NOT SPECIFIED</h1>";
      } else { 
        $idchk = array('searchid' => $rqststr[2]);
        $chkAnswer = tidalCommunication('POST','https://dev.chtn.science/data-service/system-posts/search-id-exists',serverident,serverpw, json_encode($idchk) ); 
        if ((int)$chkAnswer['responseCode'] === 200) { 
            $getStuff = tidalCommunication('POST','https://dev.chtn.science/data-service/system-posts/master-tidal-search',serverident,serverpw, json_encode($idchk));

            //FORMAT RETURN HERE
            if ((int)$getStuff['responseCode'] === 200) {
              $displayThis = "";
              $content = json_decode($getStuff['content'], true);
              $arrToDisplay = array();
              foreach($content['rtn'] as $key => $value) {
                $rtnData = json_decode($value, true);   
                foreach($rtnData as $ky => $vl) {
                   //if (strtolower( trim( $ky ) ) === 'recordsfound') {   
                   //    $displayThis .= "<br>{$ky} => {$vl}"; 
                   //}
                   if (strtolower( trim( $ky ) )  === 'returndata') {
                       foreach ($vl as $rtnKy => $rtnVl) {
                          //***************************************
                          //TODO:  MAKE THIS DYNAMIC!!!!  
                          //FOR NOW ITS HARD CODED
                          //**************************************
                          switch ($rtnVl['divisioncode']) { 
                            case 'EST':
                              $arrToDisplay[] = array("divisionallabel" => "ED{$rtnVl['divisionallabel']}","divisioncode" => $rtnVl['divisioncode'],"site" => $rtnVl['site'],"diagnosis" => $rtnVl['diagnosis'],"category" => $rtnVl['specimencategory'],"metssite" => $rtnVl['metastatic'],"proctype" => $rtnVl['proceduretype'],"hrpost" => $rtnVl['hourspost'],"preparation" => $rtnVl['preparation'],"metric" => $rtnVl['metric'],"phiage" => $rtnVl['age'],"phirace" => $rtnVl['race'],"phisex" => $rtnVl['sex'],"chemo" => $rtnVl['chemotherapy'],"rad" => $rtnVl['radiation'],"transinvvalue" => $rtnVl['transinvvalue'],"additionallabel" => $rtnVl['additionallabel'],"additionaldata" => $rtnVl['additionaldata'],"pathologyrptreference" => $rtnVl['pathologyurl']);
                                break;
                            case 'WD':
                                $arrToDisplay[] = array("divisionallabel" => "{$rtnVl['divisionallabel']}","divisioncode" => $rtnVl['divisioncode'],"site" => $rtnVl['site'],"diagnosis" => $rtnVl['diagnosis'],"category" => $rtnVl['specimencategory'],"metssite" => $rtnVl['metastatic'],"proctype" => $rtnVl['proceduretype'],"hrpost" => $rtnVl['hourspost'],"preparation" => $rtnVl['preparation'],"metric" => $rtnVl['metric'],"phiage" => $rtnVl['age'],"phirace" => $rtnVl['race'],"phisex" => $rtnVl['sex'],"chemo" => $rtnVl['chemotherapy'],"rad" => $rtnVl['radiation'],"transinvvalue" => $rtnVl['transinvvalue'],"additionallabel" => $rtnVl['additionallabel'],"additionaldata" => $rtnVl['additionaldata'],"pathologyrptreference" => $rtnVl['pathologyurl']);
                                break;
                            case 'MDW':
                                $arrToDisplay[] = array("divisionallabel" => "{$rtnVl['divisionallabel']}","divisioncode" => $rtnVl['divisioncode'],"site" => $rtnVl['site'],"diagnosis" => $rtnVl['diagnosis'],"category" => $rtnVl['specimencategory'],"metssite" => $rtnVl['metastatic'],"proctype" => $rtnVl['proceduretype'],"hrpost" => $rtnVl['hourspost'],"preparation" => $rtnVl['preparation'],"metric" => $rtnVl['metric'],"phiage" => $rtnVl['age'],"phirace" => $rtnVl['race'],"phisex" => $rtnVl['sex'],"chemo" => $rtnVl['chemotherapy'],"rad" => $rtnVl['radiation'],"transinvvalue" => $rtnVl['transinvvalue'],"additionallabel" => $rtnVl['additionallabel'],"additionaldata" => $rtnVl['additionaldata'],"pathologyrptreference" => $rtnVl['pathologyurl']);
                                break;
                            default:
                                //NO DEFAULT!  
                          }
                      } 
                   }
                } 
              }
            }


            session_start(); 
            if ($_SESSION['loggedon'] === 1) { 
              $allowalldata = 1;
            } else { 
              $allowalldata = 0;
            }

            $foundBS = 0;
            foreach (shuffle_assoc($arrToDisplay) as $rtnArrKey => $rtnArr) { 
  
$ars = (trim($rtnArr['phiage']) === "") ? "-" : $rtnArr['phiage'];
$ars .= (trim($rtnArr['phirace']) === "") ? "/-" : "/{$rtnArr['phirace']}";
$ars .= (trim($rtnArr['phisex']) === "") ? "/-" : "/{$rtnArr['phisex']}";

$cxrx = (trim($rtnArr['chemo']) === "") ? "-" : "/{$rtnArr['chemo']}";
$cxrx .= (trim($rtnArr['rad']) === "") ? "/-" : "/{$rtnArr['rad']}";

if ($allowalldata === 1) { 
    //DISPLAY PATHOLOGY REPORTS
} else { 
    //DISPLAY DIVISIONAL CONTACTS
}

$displayTblInner .= <<<ROWMAKER
<tr>
  <td>&nbsp;</td>
  <td valign=top>{$rtnArr['divisionallabel']}</td>
  <td valign=top>{$rtnArr['site']}</td>
  <td valign=top>{$rtnArr['diagnosis']}</td>
  <td valign=top>{$rtnArr['category']}</td>
  <td valign=top>{$rtnArr['metssite']}</td>
  <td valign=top>{$rtnArr['proctype']}</td>
  <td valign=top>{$rtnArr['preparation']}</td>
  <td valign=top>{$rtnArr['metric']}</td>
  <td valign=top>{$ars}</td>
  <td valign=top>{$cxrx}</td>
</tr>
ROWMAKER;
$foundBS++;
            }


            $displayThis = "<table border=0 id=bsDisplayTbl>";
            $displayThis .= "<thead><tr><td colspan=13 id=bsCounter>Biosamples Found: {$foundBS}</td></tr>";
            $displayThis .= "<tr><th>&nbsp;</th><th align=left>Division #</th><th align=left>Site</th><th align=left>Diagnosis</th><th align=left>Category</th><th align=left>Metstatic</th><th align=left>Procedure</th><th align=left>Preparation</th><th align=left>Metric</th><th align=left>A-R-S</th><th align=left>Chemo/Rad</th></tr></thead>";
            $displayThis .= $displayTblInner;
            $displayThis .= "<tfoot><tr><td colspan=13>Biosamples Found: {$foundBS}</td></tr></tfoot>";
            $displayThis .= "</table>";



$rtnthis = <<<PAGEHERE
{$displayThis} 

<p>
PAGEHERE;

        } else { 
          $rtnthis = "<h1>Search ID NOT FOUND.  RESPECIFY SEARCH CRITERIA <a href=\"javascript: void(0);\" onclick=\"navigateSite('search');\">Click here</a></h1>";
        }
      }

    return $rtnthis;

  }

}

