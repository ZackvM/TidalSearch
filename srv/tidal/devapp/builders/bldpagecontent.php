<?php

class pagecontent {

public $maximizeBtn = "<i class=\"material-icons\">keyboard_arrow_up</i>";
public $minimizeBtn = "<i class=\"material-icons\">keyboard_arrow_down</i>"; 
public $closeBtn = "<i class=\"material-icons\">close</i>";
public $menuBtn = "<i class=\"material-icons\">menu</i>";
public $checkBtn = "<i class=\"material-icons\">check</i>";

  function generateHeader($whichpage) {
    $at = genAppFiles;
    $tt = treeTop;      
    //  $jsscript =  base64file( "{$at}/publicobj/extjslib/Barrett.js" , "", "js", true);
    //  $jsscript .= "\n" . base64file( "{$at}/publicobj/extjslib/BigInt.js" , "", "js");
    //  $jsscript .= "\n" . base64file( "{$at}/publicobj/extjslib/RSA.js" , "", "js");
    //  //$jsscript .= "\n" . base64file( "{$at}/publicobj/extjslib/tea.js" , "", "js");
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
    $rtnthis = <<<PAGEHERE

This is the Introductory page for the CHTN Transient Inventory Search (TIDAL).  
<p>
<a href="{$tt}/search">Click here</a> to perform Search!
PAGEHERE;
    return $rtnthis;
  }

  function search($rqststr) { 
  
      $rtnthis = <<<PAGEHERE

<table border=1>
<tr><td>Site (anatomic)</td><td>Diagnosis</td><td>Specimen Category</td></tr>
<tr><td><input type=text id=fldSite></td><td><input type=text id=fldDiagnosis></td><td><input type=text id=fldSpecimenCategory></td></tr>
<tr><td colspan=3>
            <table>
               <tr>
                 <td><input type=checkbox id=prpFFPE value="PB" CHECKED><label for=prpFFPE>FFPE</label></td>
                 <td><input type=checkbox id=prpFIXED value="FIXED" CHECKED><label for=prpFIXED>FIXED</label></td>
                 <td><input type=checkbox id=prpFROZEN value="FROZEN" CHECKED><label for=prpFROZEN>FROZEN</label></td>
               </tr>
            </table>
      </td>
</tr>
<tr><td colspan=3 align=right><input type=button id=btnRequest value="Search"></td></tr>
</table>

PAGEHERE;
return $rtnthis;
  } 

  function searchresults($rqststr) { 
      if (trim($rqststr[2]) === "") { 
        $rtnthis = "<h1>Search ID NOT SPECIFIED</h1>";
      } else { 
        //SEARCH ID FOUND HERE
        //CHECK THAT ID EXISTS
        $idchk = array('searchid' => $rqststr[2]);
        $chkAnswer = tidalCommunication('POST','https://dev.chtn.science/data-service/system-posts/search-id-exists',serverident,serverpw, json_encode($idchk) ); 
        if ((int)$chkAnswer['responseCode'] === 200) { 

            $getStuff = json_encode(tidalCommunication('POST','https://dev.chtn.science/data-service/system-posts/master-tidal-search',serverident,serverpw, json_encode($idchk)));
           
$rtnthis = <<<PAGEHERE
{$rqststr[2]}
<hr>
{$getStuff}  
PAGEHERE;

        } else { 
          $rtnthis = "<h1>Search ID NOT FOUND.  RESPECIFY SEARCH CRITERIA <a href=\"javascript: void(0);\" onclick=\"navigateSite('search');\">Click here</a></h1>";
        }
      }

    return $rtnthis;

  }

}

