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
function oidropdownValidate() {
    var ele = 'validate-oidropdown';
    var name =  "validate-ajax";
    var errorMsg = "Please select one of the options.";

    var $j = jQuery.noConflict();
    var ret = true;
    var isValid = false;
    var isDropdown = true;
    var elem = $j('.'+ele)
    for(var i=0;i<elem.length;i++) {
        isValid = false;
        var elemID = elem[i].id;
        var tilteID = elemID.replace('child','title');
        var tilte = document.getElementById(tilteID);
        var advice2 = document.getElementById('advice-' + name + '-' + elemID);

        if(tilte.style.display == "none") {
            isDropdown = false;
        }

        tilte.removeClassName("validation-passed");
        tilte.removeClassName("validation-failed");
        elem[i].removeClassName("validation-passed");
        elem[i].removeClassName("validation-failed");

        var listelem = elem[i].getElementsByTagName('a')

        if(isDropdown) {
            var k=1;
        }
        else {
            var k=0;
        }
        for(;k<listelem.length;k++) {
            var strClassname = listelem[k].className;
            if(strClassname.indexOf("select") !=-1) {
                isValid = true; 
                break;
            }
        }
        if(!isValid) {
            ret = false;
            tilte.addClassName("validation-failed");
            elem[i].addClassName("validation-failed");
            if(!advice2) {
                var advice = '<div class="validation-advice" id="advice-' + name + '-' + elemID +'" style="display:none">' + errorMsg + '</div>'
                Element.insert(elem[i], {'after': advice});
                var advice2 = document.getElementById('advice-' + name + '-' + elemID);
            }
            advice2.hide();
            new Effect.Appear(advice2, {duration : 1 });

        }
        else {
            tilte.addClassName("validation-passed");
            elem[i].addClassName("validation-passed");
            if(advice2) {
                advice2.hide();
            }
        }
    }
    return ret;          
}