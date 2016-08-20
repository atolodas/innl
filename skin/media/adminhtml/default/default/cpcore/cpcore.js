function selectElement(el){
    $$('.cp_type_element').each(function(element){
        element.removeClassName('selected');
    });
    $(el).addClassName('selected');
    var value = el.getElementsByClassName('cp_type_id')[0].value;
    $('product_type').value = value;
    var content = el.getElementsByClassName('cp_type_content')[0].value;
    $('merchant_content').value = content;
}

function selectImage(el){
    $$('.cp_product_image').each(function(element){
        element.removeClassName('selected');
    });
    $(el).addClassName('selected');
}

function uploadCpImage(appKey, userToken){
    var file = document.getElementById("cpFile1");
    new Ajax.Request(url, {
        parameters: {"cpFile1": file.value, "appKey":appKey, "userToken":userToken, "folder":""},
        method: "POST",
        onComplete: function (response){
            if (response.responseText){
                alert(response.responseText);
            }
        }
    });
}

function leaveOldClick(element){
    var file_input = document.getElementById('newCpFile');
    if(element.checked){
        file_input.disabled = true;
    } else{
        file_input.disabled = false;
    }
}

function createAssociatedSimples(){
    var result = [];
    var checkboxes = document.getElementsByClassName('cp_checkbox');
    for(var key in checkboxes){
        if(checkboxes[key].checked){
            result.push(checkboxes[key].name);
        }
    }

    var weight = document.getElementById('cp_product_weight').value;
    var url = BASE+'cpcore/catalog_product/createAssociated';
    var parameters = {"configurable":current_product, "weight":weight, "simples":Object.toJSON(result)};

    new Ajax.Request(url, {
        parameters: parameters,
        method: 'POST',
        onSuccess: function(){ javascript:location.reload(true); }
    });
}

function selectPrint(el){
    $$('.cp_create_print').each(function(element){
        element.removeClassName('selected');
    });
    $(el).addClassName('selected');
    var value = el.getElementsByClassName('cp_print_id')[0].value;
    $('selected_print').value = value;
}

function setNewPrint(element){
    var file_input = document.getElementById('new_print');
    if(element.checked){
        file_input.disabled = false;
        $$('.cp_create_print').each(function(element){
            element.removeClassName('selected');
        });
    } else{
        file_input.disabled = true;
    }
}

function redirectToPage(element){
    var page = element.value;
    if(page < 0){
        page = 0;
    }
    if(page > cp_max_page){
        page = cp_max_page;
    }
        var reg = /\/page\/(\d+)[\/]*/;
        var result_url = document.URL.replace(reg, "/page/" + page + "/");
        if(result_url == document.URL){
            result_url = document.URL + "page/" + page + "/";
        }
    window.location = result_url;
}

function toggleCheckboxes(element){
    var checkboxes = document.getElementsByClassName('cp_product_checkboxes');
    for(var i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = element.checked;
    }
}