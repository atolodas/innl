var smarty_vars = {"divinity":{"page":"category","action":"index"},"html_url":"","company":"","cart_behavior":"ajax","theme":"bootstrapped","id":"ACC"};
var saved_blocks = [];
var dialogWindow;

function ajaxPopup(url, block_id, divId, preload,scripts,el)
{
    key = JSON.stringify({url:url, block_id:block_id});
    preload = preload ? preload : 0;
    if(divId != 'undefined') {
        hideAllPopups(block_id);
        key = JSON.stringify({url:url, block_id:block_id});
        var date = new Date('ymdhis');
        if(url.indexOf('?') != -1) { date = '&'+date }
            else { date =  '?'+date;}
        new EasyAjax.Request(url+date, {
            method: 'post',
            action_content: ['title',block_id],
            parameters: {easy_ajax: 1},
            onComplete: function (transport)
            {
                var data = transport.responseText.evalJSON();
                var block = data.action_content_data[block_id];
                block = block.split('\n').join('');
                jQuery('#bigPopup'+divId).html(block);

                if(jQuery(el).attr('data-title')) {
                    jQuery('#bigPopup'+divId).attr('title',jQuery(el).attr('data-title'));
                }

                dialogWindow = jQuery('#bigPopup'+divId).dialog({
                    autoOpen: true,
                    width: 800,
                    modal: true,
                    resizable: false,
                    create: function() {
                        jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                    }
                });

               // dialogWindow.dialog("open");

                observePopups();

                jQuery(".tabs").each(function() {
                    jQuery(this).tabs({ active: 0 });
                });
                jQuery("#tabs").tabs({ active: 0 });

                if(scripts != undefined) {  jQuery.globalEval(scripts+''); }
                observeAjaxForms();
                if(block != undefined) {  jQuery.globalEval(block+''); } // TODO: Varien form in popup not works here
            }
        });
}
}
function centerPopup(el) {
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
    var left = ((width / 2) - (jQuery(el).width()  / 2)) + dualScreenLeft;
    var top = ((height / 2) - (jQuery(el).height() / 2));
    jQuery(el).css("left",left+"px").css("top",top+"px");
}
function uiHide(el) {
    el.hide();
    dialogWindow.dialog( "close" );
    jQuery('.ui-widget-overlay').hide();
}
function uiShow(el) {
    el.show();
}
function hideAllPopups(block_id) {
    $$('.small_popup').each(function(el) {
        if(el.id!='popuped_'+block_id && jQuery(el).css('display')=='block') uiHide(el);
    });
    $$('.open').each(function(el) {
        jQuery(el).removeClass('open');
    });
}
function ajaxSmallPopup(url, block_id, divId, force, preload,button,message)  // too many params. from phtml we send Scipts to param called BUTTON, so scripts will not work for small popup for now. TODO: make them work (like in big popup)
{
    message = message ? message : '';
    force = force ? force : 0;
    preload = preload ? preload : 0;
    button = button ? button : 0;
    if(force==0) hideAllPopups(block_id);
    if(force == 1 || jQuery('#popuped_'+block_id).length == 0)
    {
        new EasyAjax.Request(url, {
            method: 'post',
            action_content: [block_id],
            parameters: {easy_ajax: 1},
            onComplete: function (transport)
            {
                $$('.loader-span').each(function(el) { el.innerHTML=''; });
                if(button)  jQuery(button).removeAttr('disabled');
                if(!transport)
                {
                    document.location.href = document.location.href;
                }
                else
                {
                    var data = transport.responseText.evalJSON();
                    var block = data.action_content_data[block_id];
                    if(jQuery('#popuped_'+block_id).length == 0) {
                        var div = jQuery('<div class="small_popup" style="position:absolute; z-index:10;overflow:visible;"  id="popuped_'+block_id+'"></div>');
                        if(message) div.html("<div class=\"block well\">"+message+"</div>"+block);
                        else div.html(block);
                        if (preload)
                        {
                            jQuery(divId).removeClass('open');
                            div.css('display', 'none');
                        }
                        else
                        {
                            jQuery(divId).addClass('open');
uiShow(div); //div.css('display', 'block');
}
jQuery(divId).after(div);
} else if (force){
    if(message) jQuery('#popuped_'+block_id).html("<div  class=\"block well\">"+message+"</div>"+block);
    else div.html(block);
    uiShow(jQuery('#popuped_'+block_id));
    eval(block);
}
}
}
});
}
else {
    if(!force && jQuery('#popuped_'+block_id).css('display') == 'block')
    {
        jQuery(divId).removeClass('open');
        uiHide(jQuery('#popuped_'+block_id));
    }
    else if(jQuery('#popuped_'+block_id).css('display') == 'none')
    {
        $$('.small_popup').each(function(el) { if(jQuery(el).css('display')=='block') { uiHide(el); } });
        jQuery(divId).addClass('open');
        uiShow(jQuery('#popuped_'+block_id));
    }
}
jQuery(document).bind( 'click', uiHideDropDowns );
}
function doubleAjax(fUrl, sUrl, block_id, divId, parameters,button) {
    parameters['easy_ajax'] = 1;
    jQuery(button).attr('disabled', 'disabled');
    var div = jQuery('<span class="loader-span"></span>');
div.html('<img src="'+smarty_vars.html_url+'skin/frontend/base/default/images/dp_popup/ajax-loader.gif"  />'); // harcode here too ) TODO: remove
jQuery(button).before(div);
new EasyAjax.Request(fUrl, {
    method: 'post',
    action_content: [block_id],
    parameters: parameters,
    onComplete: function (transport) {
        var data = transport.responseText.evalJSON();
        var messages = data.messages[0];
        var message = '';
        Object.keys(messages).forEach(function (key) {
            if(key=='code') message = messages[key].split('.')[0];
        });
ajaxSmallPopup(sUrl, block_id,divId,1,0,button, message); // here we need to send a message that will appear in popup
}
});
}
function uiHideDropDowns( e ) {
    if ( e && jQuery(e.target).parents('.dropdown').length > 0 )
        return;
    if ( e && jQuery(e.target).parents('.links').length > 0 )
        return;
    $$('.small_popup').each(function(el) {
        if(jQuery(el).css('display')=='block') {  uiHide(el); }
    });
    $$('.open').each(function(el) {
        jQuery(el).removeClass('open');
    });
    jQuery(document).unbind( 'click', uiHideDropDowns );
}
jQuery(document).bind( 'click', uiHideDropDowns );
function dialog(id, width, open, closeButton) {
    if (closeButton == undefined) { closeButton = true; }
    else { closeButton = false; }
    if (width == undefined) {
        width = '600:90%';
    }
    width = width.replace('px', '');
    if(jQuery('#'+id).width() != undefined && jQuery('#'+id).width() > 300) {
        width = jQuery('#'+id).width();
        jQuery('#'+id).css('width','auto');
        var minWidth = width;
        var maxWidth = width;
    } else {
        var minWidth = '';
        var maxWidth = '';
        var widths = width.split(':');
        if (widths[0] != 'undefined') minWidth = widths[0];
        if (widths[1] != 'undefined') maxWidth = widths[1];
    }
    var winWidth = jQuery(window).width();
    if (minWidth >= winWidth) {
        width = '90%';
        dialogObj = jQuery("#" + id).dialog({
            autoOpen: false,
            modal: true,
            width: width,
            resizable: false,
            stack: false,
            create: function () {
                jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                if (closeButton) jQuery(".ui-dialog-titlebar-close").html('<i class="ui-dialog-titlebar-close fa fa-close"></i>');
            },
            buttons: []
        });
    } else {
        dialogObj = jQuery("#" + id).dialog({
            autoOpen: false,
            modal: true,
            minWidth: minWidth,
            maxWidth: maxWidth,
            resizable: false,
            stack: false,
            create: function () {
                jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                if (closeButton) jQuery(".ui-dialog-titlebar-close").html('<i class="ui-dialog-titlebar-close fa fa-close"></i>');
            },
            buttons: []
        });
    }
    if (open == true || open == undefined) {
        if(activeDialog!=undefined) {
            activeDialog.dialog("close");
        }
        dialogObj.dialog("open");
        activeDialog = dialogObj;
    }
    return dialogObj;
}
jQuery(document).ready(function(){
    observeAjaxForms();
});
function observeAjaxForms() {
    jQuery('.ajaxForm').unbind('submit');
    jQuery('.ajaxForm').on('submit', function (e) {
        e.preventDefault();
        if(formInProgress == true) return;
        if(form == undefined || form.validator.validate()) {
            var $this = jQuery(this);
            $this.find('button[type="submit"]').html("<i class='fa fa-cog fa-spin mtop5 white'></i>");
            var params = $this.serialize().replace(/[\+]/g, " ");
            jQuery('input').attr('disabled','disabled');
            jQuery('button').attr('disabled','disabled');
            formInProgress = true;
            new EasyAjax.Request($this.attr('action'), {
                method: 'post',
                action_content: '',
                parameters: params,
                asynchronous: false,
                onComplete: function (transport)
                {
                    formInProgress = false;
                    var data = transport.responseText.evalJSON();
                    $this.find('button[type="submit"]').html($this.find('button[type="submit"]').attr('title'));
                    jQuery('input').removeAttr('disabled');
                    jQuery('button').removeAttr('disabled');
                    checkMessages(data);
                    var errors = 0;
                    if (data.messages) {
                        for(i=0;i<data.messages.length;i++) {
                            if(data.messages[i].type == 'error') {
                                errors++;
                            }
                        }
                    }
                    if(errors == 0) {
                        if($this.attr('afterSubmit')) eval($this.attr('aftersubmit'));
                    }
                }
            });
}
});
}
