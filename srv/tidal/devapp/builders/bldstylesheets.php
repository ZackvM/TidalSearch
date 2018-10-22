<?php

class stylesheets {

  public $color_white = "255,255,255";
  public $color_black = "0,0,0";
  public $color_grey = "224,224,224";
  public $color_lgrey = "245,245,245";
  public $color_brwgrey = "239,235,233";
  public $color_ddrwgrey = "189,185,183";
  public $color_lamber = "255,248,225";
  public $color_mamber = "204,197,175";
  public $color_mgrey = "160,160,160";
  public $color_dblue = "0,32,113";
  public $color_mblue = "13,70,160";
  public $color_lblue = "84,113,210";
  public $color_lightblue = "209, 219, 255";
  public $color_zgrey = "48,57,71";
  public $color_neongreen = "57,255,20";
  public $color_bred = "237, 35, 0";
  public $color_darkgreen = "0, 112, 13";
  public $color_lightgrey = "239, 239, 239";
  public $color_darkgrey = "145,145,145";
  public $color_zackgrey = "48,57,71";  //#303947 
  public $color_zackcomp = "235,242,255"; //#ebf2ff

function globalstyles() {
    
 $rtnThis = <<<STYLESHEET

@import url(https://fonts.googleapis.com/css?family=Roboto|Material+Icons|Quicksand|Coda+Caption:800|Fira+Sans);
html {margin: 0; height: 100%; width: 100%; font-family: Roboto; font-size: 1.2vh; color: rgba({$this->color_zackgrey},1);}


#divMenuHolder { position: fixed; top: 0; left: 0; width: 100%; box-sizing: border-box;  background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); padding: .3vh 10vw .3vh 10vw;}
#divMenuHolder #tblMenuHolder {width: 80vw; }
#divMenuHolder #tblMenuHolder #dspMenu {font-family: Roboto; font-size: 1.7vh; }
#divMenuHolder #tblMenuHolder #dspMenu .menuItem {width: 7vw; }
#divMenuHolder #tblMenuHolder #dspMenu .menuItem:hover { cursor: pointer; color: rgba({$this->color_neongreen},1); }

#topBarLogo {height: 4vh;}

#standardModalBacker { position: fixed; top: 0; left: 0;  z-index: 100; background: rgba({$this->color_zackgrey},.7); height: 100vh; width: 100vw; display: none; }

.generalFieldLabel { font-size: 1.3vh; font-weight: bold; color: rgba({$this->color_zackgrey},1); }
.generalInputField {width: 25vw; box-sizing: border-box; font-family: Roboto; font-size: 1.8vh;color: rgba({$this->color_zackgrey},1); padding: 1.3vh .5vw 1.3vh .5vw; border: 1px solid rgba({$this->color_mgrey},1);  }
.generalInputField:focus, .generalInputField:active {background: rgba({$this->color_lamber},.5); border: 1px solid rgba({$this->color_dblue},.5);  outline: none;  }


STYLESHEET;

return $rtnThis;
  }

function home() { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; box-sizing: border-box; padding: 7vh 10vw 0 10vw; }

#homeTopLogo { height: 20vh;  }
#logoholder { width: 100%; box-sizing: border-box; padding: 5vh 0 5vh 0; } 

#maincontent {font-size: 1.9vh; color: rgba({$this->color_zackgrey},1); }
#introText { text-align: justify; line-height: 2em; }

button {font-size: 1.9vh; padding: .5vh 0 .5vh 0; }


STYLESHEET;
return $rtnThis;
  }    

  function search() { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; box-sizing: border-box; padding: 7vh 10vw 0 10vw; }

#tblQueryCriteria { }

#standardModalDialog { border: 8px solid rgba({$this->color_lightgrey},1); width: 30vw; height: 18vh; z-index: 101; position: fixed; margin-top: -10vh; top: 50%; margin-left: -15vw; left: 50%;display: none; padding: .2vh .5vw .2vh .5vw; box-sizing: border-box; background: rgba({$this->color_white},1); }


#waitDialog { font-size: 1.6vh; color: rgba({$this->color_zackgrey},1);}
#waitDialog #advisory {font-size: 1.6vh; line-height: 1.7em;  } 
#timerDsp {font-size: 3vh; text-align: center; padding: 1vh 0 1vh 0; } 

STYLESHEET;
      return $rtnThis;
  }

  function searchresults() { 

      $rtnThis = <<<STYLESHEET

body { margin: 0; box-sizing: border-box; padding: 7vh 10vw 0 10vw; }

#bsDisplayTbl {font-family: Roboto; font-size: 1.3vh; border: 1px solid rgba({$this->color_zackgrey},1); border-collapse: collapse; }
#bsDisplayTbl thead {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }
#bsDisplayTbl #bsCounter { padding: .3vh 0 .3vh .2vw; border-bottom: 1px solid rgba({$this->color_white},1); }
#bsDisplayTbl th {padding: .5vh .1vw .5vh .1vw; }
#bsDisplayTbl tbody tr {border-bottom: 1px solid rgba({$this->color_darkgrey},1); height: 6vh;}  
#bsDisplayTbl tbody tr:nth-child(even) {background: rgba({$this->color_lightgrey},1); }
#bsDisplayTbl tbody tr:hover { cursor: pointer; background: rgba({$this->color_lamber},1); }
#bsDisplayTbl tbody td {padding: .5vh .3vw .5vh .3vw; }
#bsDisplayTbl tfoot {background: rgba({$this->color_zackgrey},1); color: rgba({$this->color_white},1); }
#bsDisplayTbl tfoot td {padding: .5vh .3vw .5vh .3vw; }

STYLESHEET;
      return $rtnThis;
  }
  
}

