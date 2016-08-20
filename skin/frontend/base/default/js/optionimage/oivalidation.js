/* Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * @category   Swms
 * @package    Swms_Optionimage
 * @author     SWMS Systemtechnik Ingenieurgesellschaft mbH
 * @copyright  Copyright (c) 2011 WMS Systemtechnik Ingenieurgesellschaft mbH (http://www.swms.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

*/
function oidropdownValidate(){var a="validate-oidropdown";var b="validate-ajax";var c="Please select one of the options.";var d=jQuery.noConflict();var e=true;var f=false;var g=true;var h=d("."+a);for(var i=0;i<h.length;i++){f=false;var j=h[i].id;var k=j.replace("child","title");var l=document.getElementById(k);var m=document.getElementById("advice-"+b+"-"+j);if(l.style.display=="none"){g=false}l.removeClassName("validation-passed");l.removeClassName("validation-failed");h[i].removeClassName("validation-passed");h[i].removeClassName("validation-failed");var n=h[i].getElementsByTagName("a");if(g){var o=1}else{var o=0}for(;o<n.length;o++){var p=n[o].className;if(p.indexOf("select")!=-1){f=true;break}}if(!f){e=false;l.addClassName("validation-failed");h[i].addClassName("validation-failed");if(!m){var q='<div class="validation-advice" id="advice-'+b+"-"+j+'" style="display:none">'+c+"</div>";Element.insert(h[i],{after:q});var m=document.getElementById("advice-"+b+"-"+j)}m.hide();new Effect.Appear(m,{duration:1})}else{l.addClassName("validation-passed");h[i].addClassName("validation-passed");if(m){m.hide()}}}return e}