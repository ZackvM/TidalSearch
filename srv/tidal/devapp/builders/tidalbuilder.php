<?php

include "bldpagecontent.php";
include "bldjavascriptr.php";
include "bldstylesheets.php";
include "blddefaultelements.php";

class pagebuilder { 

  public $statusCode = 404;		
  public $pagetitle = "";
  public $pagetitleicon = "";
  public $headr = ""; 
  public $stylr = "";
  public $scriptrs = "";
  public $bodycontent = "";
  public $pagecontrols = "";
  public $acctdisplay = "";
  public $menucontent = "";
  public $modalrs = "";
  public $modalrdialogs = "";

  //PAGE NAME MUST BE REGISTERED IN THIS ARRAY - COULD DO A METHOD SEARCH - BUT I LIKE THE CONTROL OF NOT ALLOWING A PAGE THAT IS NOT READY FOR DISPLAY
  private $registeredPages = array('home','search','searchresults');  
  
function __construct() { 		  
  $args = func_get_args();   
   if (trim($args[0]) === "") {	  		
   } else {
     session_start();
       $mobileInd = $args[2];     
       if (in_array($args[0], $this->registeredPages)) {
    
         $pageElements = self::getPageElements($args[0] ,$args[1]);	  
         $this->statusCode = $pageElements['statuscode'];
         $this->pagetitle = $pageElements['tabtitle'];
         $this->pagetitleicon = $pageElements['tabicon'];
         $this->headr = $pageElements['headr'];
         $this->stylr = $pageElements['styleline'];
         $this->scriptrs = $pageElements['scripts'];
         $this->bodycontent = $pageElements['bodycontent'];
         $this->menucontent = $pageElements['menu'];
         $this->modalrs = $pageElements['modalscreen'];
         $this->modalrdialogs = $pageElements['moddialog']; 
     }
   }
}

function getPageElements($whichpage, $rqststr) { 
  session_start();  
  $ss = new stylesheets(); 
  $js = new javascriptr();
  $oe = new defaultpageelements();
  $pc = new pagecontent();
  $elArr = array();
  //HEADER - TAB - ICON ---------------------------------------------
  $elArr['tabtitle']     =   (method_exists($oe,'pagetabs') ? $oe->pagetabs($whichpage) : "");
  $elArr['tabicon']      =   (method_exists($oe,'faviconBldr') ? $oe->faviconBldr($whichpage) : "");
  $elArr['headr']        =   (method_exists($pc,'generateHeader') ? $pc->generateHeader($whichpage) : "");
  //STYLESHEETS ---------------------------------------------------
  $elArr['styleline']    =   (method_exists($ss,'globalstyles') ? $ss->globalstyles() : "");
  $elArr['styleline']   .=   (method_exists($ss,$whichpage) ? $ss->$whichpage() : "");
  //JAVASCRIPT COMPONENTS -------------------------------------------
  $elArr['scripts']      =   (method_exists($js,'globalscripts') ? $js->globalscripts( "", "") : "");
  $elArr['scripts']     .=   (method_exists($js,$whichpage) ? $js->$whichpage($rqststr) : "");
  //PAGE CONTENT ELEMENTS  ------------------------------------
  $elArr['bodycontent'] =   (method_exists($pc,$whichpage) ? $pc->$whichpage($rqststr, $usrmetrics) : "");   
  //CONTROL BARS GET BUILT HERE --------------------------------------------------------------    
  $elArr['menu']         =   (method_exists($oe,'menubuilder') ? $oe->menubuilder($whichpage) : ""); 
  $elArr['modalscreen']  =   (method_exists($oe,'modalbackbuilder') ? $oe->modalbackbuilder($whichpage) : "");
  $elArr['moddialog']    =   (method_exists($oe,'modaldialogbuilder') ? $oe->modaldialogbuilder($whichpage) : "");
//RETURN STATUS - GOOD ---------------------------------------------------------------
  $elArr['statuscode'] = 200;
  return $elArr; 
}

}

