var innerAjaxFormsInit = false;
     
jQuery( document ).ready(function() {
  jQuery('.simpleAjax').each(function() { 
      if(jQuery(this).attr('href')) { 
        var href = jQuery(this).attr('href');
        jQuery(this).attr('href', 'javascript:void(0)');
        jQuery(this).attr('onclick', 'simpleAjax("'+href+'")');
      }
  });

  innerAjax();
  jQuery('#pagesLink').trigger('click');

  jQuery(window).bind('keydown', function(event) {
  if (event.ctrlKey || event.metaKey) {
      switch (String.fromCharCode(event.which).toLowerCase()) {
      case 's':
          event.preventDefault();
          jQuery('#edit_form').submit();
          break;
      }
  }
  });
});

function innerAjax() { 
  jQuery('.simpleAjaxInner').each(function() { 
      if(jQuery(this).attr('href')) { 
        var href = jQuery(this).attr('href');
        jQuery(this).attr('href', '#section-inner-content');
        jQuery(this).attr('onclick', 'simpleAjaxInner("'+href+'")');
      }
  });
  jQuery('.simpleAjaxNoAction').each(function() { 
      if(jQuery(this).attr('href')) { 
        var href = jQuery(this).attr('href');
        jQuery(this).attr('href', 'javascript:void(0)');
        jQuery(this).attr('onclick', 'simpleAjaxNoAction("'+href+'")');
      }
  });
}



function ajaxFormSubmit(data, url, el) { 
  if(form.validator.validate()) {
    if(formInProgress == true) return;
    jQuery(el).html("<i class='fa fa-cog fa-spin mtop5 white'></i>");
    new EasyAjax.Request(url, {
    method: 'post',
    action_content: 'root',
    parameters: data.replace(/[\+]/g, " "),
    asynchronous: false,
      onComplete: function (transport)
      { 
          formInProgress = false;
          jQuery(el).html(jQuery(el).attr('title'));
          var data = transport.responseText.evalJSON();
          var content = data.action_content_data['root'];

          var doc = window.open().document;

          var frame = '<div class="tablet-landscape" id="iframelive" style=" height:100%; left: 0; position: fixed;top: 0;    width: 100%;">'+
          '<div id="preview-wrapper">'+
          '<iframe style="border:none;height: 100%;" border="0" id="frame"></iframe></div></div>';
          frame += '<ul id="responsivator" style="display: block;">'+
          '<li id="desktop" class="active"></li>'+
          '<li id="tablet-landscape" class=""></li>'+
          '<li id="tablet-portrait" class=""></li>'+
          '<li id="iphone-landscape" class=""></li>'+
          '<li id="iphone-portrait" class=""></li>'+
          '</ul>'+
          '<link href="'+BASE_URL+'skin/frontend/bootstrapped/default/css/preview-responsive.css?d='+(new Date())+'" type="text/css" rel="stylesheet">'
          +'<script src="'+BASE_URL+'skin/frontend/bootstrapped/default/js/jquery.js"></script>'
          +'<script src="'+BASE_URL+'js/ajax.js" type="text/javascript"></script>';
          doc.open(); doc.write(frame); 


          fdoc = doc.getElementById('frame').contentWindow.document; 
          fdoc.open();
          fdoc.write(content);
          fdoc.close();
          doc.close();
      }
    });
  }
}

var activeDialog;
var dialogObj;
function createDialog(content,id, width, open, closeButton) {
    if (closeButton == undefined) { closeButton = true; }
    else { closeButton = false; }
    if (width == undefined) { 
        width = '300:600';
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
    if (open == true) {
      dialogObj.dialog("open");
      activeDialog = dialogObj;
    }
  return dialogObj;
}

var form;
var ajaxFormObserved = false;
var formInProgress = false;
function ajaxForms() {
    if(ajaxFormObserved == false) {
     // alert('OBSERVING');
    ajaxFormObserved = true;
    jQuery('.ajaxForm').unbind('submit');
    jQuery('.ajaxForm').on('submit', function (e) {
        e.preventDefault();
        if(formInProgress == true) return;
        if(form == undefined || form.validator.validate()) {
            var $this = jQuery(this);
            $this.find('button[type="submit"]').html("<i class='fa fa-cog fa-spin mtop5 white'></i>");
            var params = $this.serialize().replace(/[\+]/g, " ");
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
                      if($this.attr('afterSubmit')) eval($this.attr('afterSubmit'));
                  }
              }
            });
        }
    });
  }
}
var currentRequest = null;
function simpleAjax(url, clean) {
    ajaxFormObserved = false;
    var spiner = "<div class='centered nml nmr' style='margin-top:20%; width:100%; height: 100%'> <i class='fa fa-cog fa-spin' style='font-size:100px !important;'></i></div>";
    jQuery('#section-content').html(spiner);
    if(clean == undefined) jQuery('#section-inner-content').html('');
    currentRequest =  new EasyAjax.Request(url, {
    method: 'post',
    action_content: ['title','content'],
    parameters: {easy_ajax: 1},
    asynchronous: false,
        onComplete: function (transport)
        {
          var data = transport.responseText.evalJSON();
          var block = data.action_content_data['content'];
          jQuery('#section-content').html(block);
          innerAjax();
          if(clean == undefined) ajaxForms();
          if(block) { eval(block); }
        }
    });
}

function simpleAjaxNoAction(url) {
    new EasyAjax.Request(url, {
    method: 'post',
    action_content: ['content'],
    parameters: {easy_ajax: 1},
    onComplete: function (transport)
    {
        var data = transport.responseText.evalJSON();
        checkMessages(data);
    }
    });
}

function simpleAjaxInner(url) { 
    ajaxFormObserved = false;
    var spiner = "<div class='centered nml nmr' style='margin-top:10%; width:100%; height: 100%'> <i class='fa fa-cog fa-spin' style='font-size:100px !important;'></i></div>";
    jQuery('#section-inner-content').html(spiner);
    currentRequest = new EasyAjax.Request(url, {
      method: 'post',
      asynchronous: false,
      action_content: ['content'],
      parameters: {easy_ajax: 1},
      onComplete: function (transport)
      {
          var data = transport.responseText.evalJSON();
          var block = data.action_content_data['content'];
          checkMessages(data);
          jQuery('#section-inner-content').html(block);
          ajaxForms();
          observePopups();
          if(block) { eval(block); }
      }
    });
}

function checkMessages(data) { 
  var errors = 0;
  if (data.messages) {
    for(i=0;i<data.messages.length;i++) {
          if(data.messages[i].type == 'error') {
              errors++;
          }
    }
  }
  var str = '';
  if(errors > 0) {
      for(i=0;i<data.messages.length;i++) {
          if(data.messages[i].type == 'error') {
              str+= data.messages[i].code + "<br/>";
          }
      }

      if(str) { 
        jQuery('#dialog-message-add').find('span.content').html(str);
        confirmDialog = jQuery('#dialog-message-add').dialog({
            autoOpen: false,
            modal: true,
            buttons: {},
            width: 400,
            dialogClass: 'error-dialog bg-red',
            create: function () {
                jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                jQuery(".ui-dialog-titlebar-close").html('<i class="ui-dialog-titlebar-close white fa fa-close"></i>');
            },
          }); 
            
        confirmDialog.dialog('open').css({'min-height':'15px'});
      }
  } else {
      for(i=0;i<data.messages.length;i++) {
          if(data.messages[i].type == 'success') {
              str+= data.messages[i].code + "<br/>";
          }
      }

      if(str) { 
        jQuery('#dialog-message-success-add').find('span.content').html(str);
        confirmDialogSuccess = jQuery('#dialog-message-success-add').dialog({
            autoOpen: false,
            modal: true,
            buttons: {},
            width: 400,
            dialogClass: 'success-dialog success-dialog2 bg-green',
            create: function () {
                jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                jQuery(".ui-dialog-titlebar-close").html('<i class="ui-dialog-titlebar-close white fa fa-close"></i>');
            },
          }); 
            
        confirmDialogSuccess.dialog('open').css({'min-height':'15px','padding':'0px'});
      }

  }

  if(str!='') { 
      new EasyAjax.Request(document.location.href, {
          method: 'post',
          action_content: '',
          parameters: {easy_ajax: 1},
          onComplete: function (transport) {}
      });
  }
}

var min_height = 600;
    jQuery(function(){
        resizeFrame();
    });
    function resizeFrame()
    {
        var main_iframe = jQuery('#preview');
        var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight;
        var height = y;
        
        if (height < min_height)
            height = min_height;
        jQuery('#preview').height(height);
        jQuery('#iframelive').height(height);
    }
    jQuery(window).resize(function() {
        resizeFrame();
    }).load(function() {
        resizeFrame();
    });

  jQuery(document).ready(function() {
    if(!(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/Opera Mini/))) {
      var frame = document.getElementById('preview');
       jQuery('#iframelive').removeClass().addClass('desktop');
       jQuery('#desktop').removeClass().addClass('active');
       jQuery('ul#responsivator').show();
       jQuery('ul#responsivator li').click(function () {
         jQuery('ul#responsivator li').removeClass();
         jQuery(this).addClass('active');
         jQuery('#iframelive').removeClass().addClass(jQuery(this).attr('id'));
        //for reloading frame and scroll to top
        jQuery('#frame').src = jQuery('#frame').src;
      });
    }
    });