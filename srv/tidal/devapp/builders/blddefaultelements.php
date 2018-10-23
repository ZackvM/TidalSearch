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
  $innerDialog = dialoggenerator($whichpage);
  $thisModDialog = "<div id=standardModalDialog>{$innerDialog}</div>";
  return $thisModDialog;
}    

function menubuilder($whichpage) { 
  $menu = menugetter($whichpage);         
  return "<div id=divMenuHolder>{$menu}</div>";
}

}

function dialoggenerator($whichpage) { 

    switch($whichpage) { 
      case 'search':
          $rtnthis = <<<DIALOGGEN

<table border=0 id=waitDialog>
<tr>
  <td id=advisory>This function will now search multiple datasources for your query criteria.  While this could take up to a minute to complete - in most cases it will be completed in less than 20 seconds.  Please be patient.</td></tr>
<tr>
  <td><div id=timerDsp></div></td></tr>
</table>

DIALOGGEN;
          break;
      default: 
          $rtnthis = <<<DIALOGGEN

DIALOGGEN;
    }
    return $rtnthis;
}

function menugetter($whichpage) { 

    $atree = applicationTree; 
    $tt = treeTop;
    $thinLogo = base64file("{$atree}/objects/graphics/chtnlogowhite.png","topBarLogo","png",true); 

    switch($whichpage) { 
      case 'uniquepage':
          $rtnthis = "SEARCH MENU";
          break;
      default: 
          $rtnthis = <<<MENUPAGE
<table border=0 id=tblMenuHolder>
  <tr>
    <td>{$thinLogo}</td>
    <td valign=bottom align=right>
       <table border=0 id=dspMenu>
         <tr>
           <td class=menuItem><a href="{$tt}">Home</a></td>
           <td class=menuItem><a href="{$tt}/search">Search</td>
           <td class=menuItem>Contact CHTN</td>
           <td class=menuItem>About Search</td>
         </tr>
       </table>
    </td>
  </tr>
</table>
MENUPAGE;
    }
    return $rtnthis;
}



