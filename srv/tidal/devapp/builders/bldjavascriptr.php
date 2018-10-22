<?php


class javascriptr {

function globalscripts($rqststr) {  

  session_start();
  $sid = session_id();
  $tt = treeTop;
   
$rtnThis = <<<JAVASCR

var byId = function( id ) { return document.getElementById( id ); };
var treeTop = "{$tt}";
var mousex;
var mousey;

var httpage = getXMLHTTPRequest();
var httpageone = getXMLHTTPRequest();
function getXMLHTTPRequest() {
try {
req = new XMLHttpRequest();
} catch(err1) {
        try {
	req = new ActiveXObject("Msxml2.XMLHTTP");
        } catch(err2) {
                try {
                req = new ActiveXObject("Microsoft.XMLHTTP");
                } catch(err3) {
                  req = false;
                }
        }
}
return req;
}

function makebasicauth(user, password) {
  var tok = user + ':' + password;
  var hash = btoa(tok);
  return "Basic " + hash;
}

function universalAJAX(usercred, pwcred, met, url, passedDataJSON, callback) { 
  byId('standardModalBacker').style.display = 'block';
  var mlURL = url;
  var auth = makebasicauth(usercred,pwcred);
  var rtn = new Object();
  httpage.open(met,mlURL,true);
  httpage.setRequestHeader('Authorization', auth); 
  httpage.onreadystatechange = function () {
  if (httpage.readyState === 4) {
     if (httpage.status === 200) { 
       rtn['responseText'] = httpage.responseText;
       rtn['responseCode'] = httpage.status;
       byId('standardModalBacker').style.display = 'none';
       callback(JSON.stringify(rtn));
     } else { 
       rtn['responseText'] = httpage.responseText;
       rtn['responseCode'] = httpage.status;
       byId('standardModalBacker').style.display = 'none';
       callback(JSON.stringify(rtn));
     }
  }
  };
  httpage.send(passedDataJSON);
} 

document.addEventListener('mousemove', function(e) { 
  mousex = e.pageX;
  mousey = e.pageY;
}, false);

document.addEventListener('DOMContentLoaded', function() {  
  byId('standardModalBacker').style.display = 'none';
}, false);

function openOutSidePage(whatAddress) {
    var myRand = parseInt(Math.random()*9999999999999);
    window.open(whatAddress,myRand);
}

function openPageInTab(whichURL) { 
  if (whichURL !== "") {
    window.location.href = whichURL;
  }
}

function navigateSite(whichURL) {
    byId('standardModalBacker').style.display = 'block';
    if (whichURL) {
      window.location.href = treeTop+'/'+whichURL;
    } else {     
      window.location.href = treeTop;
    }
}
 
JAVASCR;
return $rtnThis;    
}

function main($rqststr) {      
  session_start(); 
  $tt = treeTop;
    
$rtnThis = <<<JAVASCR
    
JAVASCR;
return $rtnThis;    
}

function search($rqststr) { 
  
    //GENERATE SINGLE USE USER-CREDENTIALS
    session_start();
    $dta['sid'] = session_id();
    $sss = serverpw; 
    $answer = tidalCommunication('POST','https://dev.chtn.science/data-service/system-posts/public-system-keys',serverident,serverpw, json_encode($dta) );
    if ($answer['responseCode'] === 200) { 
      //IF $answer['responseCode'] !== 200 then the secret key service didn't work!!!!
      $content = json_decode($answer['content'], true);
      $vid = $content['vid'];
      $publicusr = session_id();
      $publicobscurecode = obscurecode;   
      $publicusrpw = cryptservice($publicusr . "::" . $publicobscurecode,'e',true);
    }

    $rtnthis = <<<JAVASCR

String.prototype.lpad = function(padString, length) {
    var str = this;
    while (str.length < length)
        str = padString + str;
    return str;
}

function sendRequest(url, requestedmethod,  passdata) {  
  var publicusr = "publicuser-{$publicusr}";
  var usrident = "{$publicusrpw}";
  universalAJAX(publicusr, usrident,requestedmethod,url, passdata, answerrequest);
}

function answerrequest(rsp) { 
  var ansRsp = JSON.parse(rsp);
  var ansTxt = JSON.parse(ansRsp['responseText']);
  if (ansRsp['responseCode'] === 200) { 
    navigateSite('search-results/'+ansTxt['DATA']);
  } else {
    byId('standardModalBacker').style.display = 'none';
    byId('standardModalDialog').style.display = 'none';
    alert(ansTxt['MESSAGE']);    
  }
}

document.addEventListener('DOMContentLoaded', function() {  

if (byId('btnRequest')) { 
   byId('btnRequest').addEventListener('click', function() {


     if (byId('standardModalBacker')) { 
       byId('standardModalBacker').style.display = 'block';
         if (byId('standardModalDialog')) { 
           byId('standardModalDialog').style.display = 'block';
           //TIMER GOES HERE
           initClock();
         }
     }
     var rsp = sendRequest('https://dev.chtn.science/data-service/submit-search','POST',buildRequest());
   }, false); 
}

}, false);

var milliseconds   = 0; 
var displaySeconds = 0;
function updateClock() {
  if (byId('timerDsp')) {
    milliseconds++;
    if (milliseconds > 9) { 
      displaySeconds++;
      milliseconds = 0;
    }
    byId('timerDsp').innerHTML = displaySeconds.toString().lpad("0",2) + ":" + milliseconds;    
  }
}

function initClock() {
   milliseconds = 0
   displaySeconds = 0;
   var clocktimer = window.setInterval("updateClock()",100);
}

function buildRequest() {
  var dta = new Object();
  document.querySelectorAll('*').forEach(function(node) {
    if (node.id.substr(0,3) === 'fld') {  
      dta[node.id.substr(3)] = node.value.trim();
    }
    if (node.id.substr(0,3) === 'prp') {
     dta[node.id] = (node.checked) ? 1 : 0;
    }
  });
  var passdata = JSON.stringify(dta);
  return passdata;
}



JAVASCR;
return $rtnthis;
}


}

