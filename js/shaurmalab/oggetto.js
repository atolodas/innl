function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function assignCustomerToObject(url1,url2,divId) {

    var div = jQuery('div[id*=bigPopup].ui-dialog-content').filter(':visible');
    jQuery(div).html(loaderImg);

    new EasyAjax.Request(url1, {
        method: 'post',
        action_content: '',
        parameters: {easy_ajax: 1},
        onComplete: function (transport)
        {
            objectGrid1JsObject.reload();
            new EasyAjax.Request(url2, {
                method: 'post',
                action_content: 'content',
                parameters: {easy_ajax: 1},
                onComplete: function (transport)
                {
                    var data = transport.responseText.evalJSON();
                    var block = data.action_content_data['content'];

                    jQuery(div).html(block);
                    jQuery("#tabs").tabs({ active: 0 });
                    observePopups();
                    if(scripts) { eval(scripts); }
                    if(block) { eval(block); }
                    jQuery(div).css('z-index',110);

                    jQuery('select').styler();
                    jQuery('select').trigger('refresh');


                }
            });
        }
    });

}

function assignCustomer(id,pid,refreshUrl) {
    values[pid].push(id);
    $('assigned_uid').value = values[pid].join(',');
    jQuery('#add_new'+pid).find('input').value = '';
    jQuery('#add_new'+pid).hide();
    jQuery('#'+pid+'uid'+id).show();



}

function unassignCustomer(id,pid) {

    for(i=0; i<values[pid].length; i++ ) {
        if(values[pid][i] == id) {
            values[pid].splice(i,1);
        }
    }
    $('assigned_uid').value = values[pid].join(',');
}

function assignObject(id,pid,field) {
    obj_values[pid].push(id);
    jQuery('input[name*='+field+'_by]').val(obj_values[pid].join(','));
    jQuery('#add_new'+pid).find('input').value = '';
    jQuery('#add_new'+pid).hide();
    jQuery('#'+pid+'uid'+id).show();

}

function unassignObject(id,pid, field) {

    for(i=0; i<obj_values[pid].length; i++ ) {
        if(obj_values[pid][i] == id) {
            obj_values[pid].splice(i,1);
        }
    }
    jQuery('input[name*='+field+'_by]').val(obj_values[pid].join(','));
}

var formatQueryString = function(queryString, exclude) {
    var params = {}, queries, temp, i, l, query_string=[];

    // Split into key/value pairs
    queries = queryString.split("&");

    // Convert the array of strings into an object
    for ( i = 0, l = queries.length; i < l; i++ ) {
        temp = queries[i].split('=');
        var is_exclude = false;

        jQuery.each(exclude, function(key, el){
            if (temp[0].indexOf(el) > -1) {
                is_exclude = true;
            }
        });

        if (typeof temp[1] != 'undefined' && !is_exclude){
            query_string.push(temp[0] + '=' + temp[1]);
        }
    }

    return query_string;
};

jQuery(document).ready(function(){
    jQuery('select').styler();
    jQuery('.table-scroll').tableScroll({height:165});
});



function fadeBlock(id) {
    jQuery(id).fadeOut( "slow" );
}
