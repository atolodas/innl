var jscss = '';
var widgets_list = '';
jQuery(document).ready(function() {

    if($('col-left')!= undefined && $('left-mobile')!=undefined) {
        $('left-mobile').innerHTML = '<label class="pull-left pointer btn-navbar" onclick="toggleMobileMenu(\'#col-left\', \'left\')"><i class="fa fa-info-circle f25 white inline pull-left mtop10 mleft10"></i></label>';
    }
});

function toggleMobileMenu(id, dir) {
    var menu = jQuery(id);
    if (menu.data('isOpen')) {
        jQuery(id).css('opacity', 0);
        jQuery(id).css('display', 'none');
        jQuery('body').css(dir, '');
        jQuery('#fade').css('display', 'none');
        jQuery('body').css('overflow', 'visible');
        jQuery('#fade').attr('onclick','').unbind('click');

        menu.data('isOpen', false);
        menu.css('width',0);
    } else {
        jQuery(id).css('opacity', 1);
        if(dir=='left') jQuery(id).css('padding', 0);
        jQuery(id).css('display', 'block');
        jQuery(id).css('padding', '10px');
        jQuery('body').css(dir, '300px');
        jQuery('body').css('overflow', 'hidden');
        jQuery('#fade').css('display', 'block');

        jQuery('#fade').on("click",function() { toggleMobileMenu(id, dir); });

        menu.data('isOpen', true);
        menu.css('width',300);
    }
    return false;
}

// TODO: move that function to other file
var saveAttributeUrl = BASE_URL+ 'score/oggetto/saveAttribute';

function saveAttribute(id,name,value,el) {
    el.disabled = 'true';
    new EasyAjax.Request(saveAttributeUrl+'/id/'+id, {
        method: 'post',
        action_content: '',
        parameters: {easy_ajax: 1,value: value, attribute_code: name},
        onComplete: function (transport)
        {

            el.disabled = !el.disabled;
            var data = transport.responseText.evalJSON();
        }
    });
}

var applyCustomerUrl = BASE_URL + 'score/oggetto/applyCustomer';

function applyCustomerToAttribute(id,name,value,el) {
    el.disabled = 'true';
    new EasyAjax.Request(applyCustomerUrl+'/id/'+id, {
        method: 'post',
        action_content: '',
        parameters: {easy_ajax: 1,value: value, attribute_code: name},
        onComplete: function (transport)
        {
            el.disabled = !el.disabled;
            document.location.href = document.location.href;
        }
    });
}

// customize all inputs (will search for checkboxes and radio buttons)
jQuery(document).ready(function(){
    jQuery('input').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
        increaseArea: '20%' // optional
    });

    jQuery('input').on('ifClicked', function(event){
      eval(jQuery(this).attr('onclick'));
    });

    jQuery("[rel='popover']").tooltip();

});
