jQuery(document).ready(function () {

    jQuery('.icon-edit').on('click',function(){
        jQuery(this).parent().find('input').toggle();
        jQuery(this).parent().find('.field-name').toggle();
        return false;
    });

    jQuery('.reassign-button').on('click',function(){
        jQuery(this).parent().find('input').toggle();
        jQuery(this).parent().find('.field-name').toggle();
        if(!jQuery('.reassign-button').hasClass('assign')) {
            jQuery('.reassign-button').addClass('assign').html('Assign');
        } else {
            jQuery('.reassign-button').removeClass('assign').html('Reassign');
        }
        return false;
    });

    var progressbar = jQuery( "#progressbar" );
    var progressLabel = jQuery('.progress-label');

    progressbar.progressbar({
        value: 0,
        change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
        },
        complete: function() {
        }
    });

    jQuery('.next-page').on('click',function(){
        jQuery('#loader').fadeIn();
        progressTimer = setTimeout( progress, 0 );

        return false;
    });

    function progress() {
        var val = progressbar.progressbar( "value" ) || 0;
        var curr_precent = Math.floor(100/(jQuery('.step-tab .step:last-child').index() + 1))*(jQuery('.step-tab .curr-step').index() + 1) + 2;
        if (val <= curr_precent) {
            val ++;
        }

        progressbar.progressbar( "value", val);

        if ( val <= curr_precent ) {
            progressTimer = setTimeout( progress, 50 );
        } else {
            changeStep(jQuery('.step-tab .curr-step').next());
            jQuery('#loader').fadeOut();
            clearTimeout( progressTimer );
        }

    }

    jQuery('.prev-page').on('click',function(){
        changeStep(jQuery('.step-tab .curr-step').prev());
        return false;
    });

    jQuery('.step').on('click',function(){
        if(jQuery(this).hasClass('filled-step')) {
            changeStep(jQuery(this));
        }
        return false;
    });

    function changeStep(curr_step){
        jQuery('#' + jQuery('.curr-step').attr('data-step')).hide();
        jQuery('#' + curr_step.attr('data-step')).fadeIn();
        jQuery('.curr-step').attr('class', 'step filled-step');
        curr_step.addClass('curr-step');
    }

    jQuery('.step-content').hide();
    jQuery('#' + jQuery('.curr-step').attr('data-step')).show();


    jQuery('#add_user_group').on('click',function(){
        setTimeout(function() {
            jQuery('.assign-new .custom-select').trigger('refresh');
        }, 1)
        jQuery('.assign-new').show();
        return false;
    });
    jQuery('#assign_save').on('click',function(){
        jQuery('.assign-new').hide();
        if (jQuery('.assign-new input').val()) {
            var html = '<tr><td>' + jQuery('.assign-new input').val() + '</td><td>' + jQuery('.assign-new select').val() + '</td><td><a class="btn btn-small w100" href="#">Delete</a></td></tr>'
            jQuery('.assign-new').before(html);
        }
        jQuery('.assign-new input').val('');
        jQuery('.assign-new select').val('');
        return false;
    });
    jQuery('#assign_cancel').on('click',function(){
        jQuery('.assign-new').hide();
        jQuery('.assign-new input').val('');
        jQuery('.assign-new select').val('');
        return false;
    });

    jQuery('#assign_school').on('click',function(){
        jQuery('.assign-school-new').show();
        return false;
    });
    jQuery('#assign_school_save').on('click',function(){
        jQuery('.assign-school-new').hide();
        if (jQuery('#assign_school_id').val() && jQuery('#assign_school_name').val()) {
            var html = '<tr><td>' + jQuery('#assign_school_id').val() + '</td><td>' + jQuery('#assign_school_name').val() + '</td><td><a class="btn btn-small w100" href="#">Delete</a></td></tr>'
            jQuery('.assign-school-new').before(html);
        }
        jQuery('.assign-school-new input').val('');
        return false;
    });
    jQuery('#assign_school_cancel').on('click',function(){
        jQuery('.assign-school-new').hide();
        jQuery('.assign-school-new input').val('');
        return false;
    });

    jQuery('#add_group').on('click',function(){
        jQuery('.add_new_group').show();
        return false;
    });
    jQuery('#group_save').on('click',function(){
        jQuery('.add_new_group').hide();
        if (jQuery('#add_group_id').val() && jQuery('#add_group_name').val()) {
            var html = '<tr><td class="tac">' + jQuery('#add_group_id').val() + '</td><td class="tac">' + jQuery('#add_group_name').val() + '</td><td><a class="btn btn-action btn-small mr5px" href="#">View group</a><a class="btn btn-small" href="#">Delete from group</a></td></tr>'
            jQuery('.add_new_group').before(html);
        }
        jQuery('.add_new_group input').val('');
        return false;
    });
    jQuery('#group_cancel').on('click',function(){
        jQuery('.add_new_group').hide();
        jQuery('.add_new_group input').val('');
        return false;
    });


    jQuery('.custom-select').styler();

    jQuery('.table-scroll').tableScroll({height:165});

    jQuery( ".datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "img/calendar.gif",
        buttonImageOnly: true
    });

    add_user_dialog = jQuery( "#add-user-form" ).dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Create and Send Activation link',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( "#add-user" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        add_user_dialog.dialog( "open" );
    });

    edit_user_dialog = jQuery( "#edit-user-form" ).dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Delete user',
                'class':'btn btn-action2',
                'click': function() {
                }
            },
            {
                'text': 'Save',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( ".edit-user" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        edit_user_dialog.dialog( "open" );
    });

    settings_dialog = jQuery( "#settings-form" ).dialog({
        autoOpen: false,
        width: 400,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Save',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( ".settings-button" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        settings_dialog.dialog( "open" );
    });

    register_school_dialog = jQuery( "#register-school-form" ).dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Register school',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( "#register-school" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        register_school_dialog.dialog( "open" );
    });

    activities_dialog = jQuery( "#activities-form" ).dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Save',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( ".activities-button" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        activities_dialog.dialog( "open" );
    });

    register_pupil_dialog = jQuery( "#register-pupil-form" ).dialog({
        autoOpen: false,
        width: 350,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Register pupil',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( "#register-pupil" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        register_pupil_dialog.dialog( "open" );
    });

    add_group_dialog = jQuery( "#add-group-form" ).dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Cancel',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            },
            {
                'text': 'Save',
                'class':'btn btn-action',
                'click': function() {
                }
            }
        ]
    });

    jQuery( "#add-group" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        add_group_dialog.dialog( "open" ).find(".ui-dialog-title").remove();

    });

    nurses_profile_dialog = jQuery( "#nurses-profile-form" ).dialog({
        autoOpen: false,
        width: 550,
        modal: true,
        resizable: false,
        create: function() {
            jQuery(".ui-dialog .btn").removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
        },
        buttons: [
            {
                'text': 'Close',
                'class':'btn',
                'click': function() {
                    jQuery(this).dialog( "close" );
                }
            }
        ]
    });

    jQuery( ".nurses-profile" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        nurses_profile_dialog.dialog( "open" ).find(".ui-dialog-title").remove();

    });


//    var error_dialog = jQuery( "#dialog-message" ).dialog({
//        modal: true,
//        buttons: {
//        },
//        dialogClass: "error-dialog",
//        hide: {effect: 'fade', duration: 150},
//        show: {effect: 'fade', duration: 250}
//    });

    jQuery( ".error-messages" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        error_dialog.dialog( "open" );

    });

//    var success_dialog = jQuery( "#dialog-message-success" ).dialog({
//        modal: true,
//        buttons: {
//        },
//        dialogClass: "success-dialog",
//        hide: {effect: 'fade', duration: 150},
//        show: {effect: 'fade', duration: 250},
//        create: function() {
//            setTimeout(function() {
//                jQuery("#dialog-message-success").dialog('close')
//            }, 4000);
//        }
//    });

    jQuery( ".success-messages" ).button().removeClass('ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only').on( "click", function() {
        success_dialog.dialog( "open" );
        setTimeout(function() {
            jQuery("#dialog-message-success").dialog('close')
        }, 4000);

    });

    jQuery( "#tabs" ).tabs();
});