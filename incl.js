console.log("Incl.js loaded");
//document.getElementsByTagName("body")[0].innerHTML = "HEADER HERE<div align='right'><a href='..' title='Up'><kbd>&#x2303;</kbd></a><a href='/' title='Home'><kbd>&#x1F3E0;</kbd></a></div></a> HEADER END" + document.getElementsByTagName("body")[0].innerHTML;
var SITE  = "Clicketyclick";
var REPO  = "cpr";
var TIME  = "";//2022-11-17T16:19:58";

var HEAD =  TIME+ "<div align='right'>"
// Demo
  + "<button onclick=\"location.href='https://"
    + SITE
    + ".github.io/"
    +REPO
  + "/demo.html'\" title='Demo' type='button' title='Up'>&#x24B9;</button>"
// Releases
  + "<button onclick=\"location.href='https://"
    + SITE
    + ".github.io/"
    +REPO
  + "/releases'\" title='Releases' type='button' title='Up'>&#x24C7;</button>"
// Source
  + "<button onclick=\"location.href='https://github.com/"
    + SITE
    + "/"
    + REPO
  + "'\" type='button' title='Source'>&lt;&gt;</button>"

  + "&nbsp;"
// Up
  + "<button onclick=\"location.href='..'\" type='button' title='Up'>&#x2303;</button>"
// Home
  + "<button onclick=\"location.href='/'\" type='button' title='Home'>&#x1F3E0;</button>"
  + "</div>";

var FOOTER = "<hr>&copy;2022 Clicketyclick.dk";
document.getElementsByTagName("body")[0].innerHTML = HEAD + document.getElementsByTagName("body")[0].innerHTML + FOOTER;

console.log("Incl.js Header inserted");
console.log("Incl.js ended");
