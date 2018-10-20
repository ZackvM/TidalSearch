<?php

class defaultpageelements {
        
function faviconBldr($whichpage) {  
    $at = applicationTree;
    $favi = base64file("{$at}/objects/graphics/icons/chtnblue.ico", "favicon", "favicon", true);
    return $favi;
}

function pagetabs($whichpage) { 
  $thisTab = "CHTN Transient Inventory Search";
  switch($whichpage) { 
    case 'main':
      $thisTab = "CHTN Transient Inventory Search";
      break;
    default: 
      $thisTab = "Search the CHTN Network's Transient Inventory"; 
    break; 
  }
  return $thisTab;
}
    
function modalbackbuilder($whichpage) {
  $thisModBack = "";
  switch ($whichpage) {     
    default: 
      $thisModBack = "<div id=standardModalBacker></div>";    
  }                
  return $thisModBack;
}

function modaldialogbuilder($whichpage) {
  $thisModDialog = "<div id=standardModalDialog></div>";
  return $thisModDialog;
}    

function menubuilder($whichpage) { 
  $menu = menugetter($whichpage);         
  return "<div id=divMenuHolder>{$menu}</div>";
}

}

function menugetter($whichpage) { 
   
    switch($whichpage) { 
      case 'home': 
          $rtnthis = "HOME MENU";
      break;
      default: 
         $rtnthis = "DEFAULT MENU GOES HERE";
    }
    return $rtnthis;
}



