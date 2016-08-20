/* 
 * Keywords for Wms-hint
 */

var WmsHintKeywords = Class.create();
WmsHintKeywords.prototype = {
    initialize: function(config){
//        this.addressFormId      = config.addressFormId;
//        this.addressFormClass   = config.addressFormClass;
//        this.popupDivId         = config.popupDivId;
    },
    
    isEnabled: function(){
        return true;
    }
    
    
    
};

WmsHintKeywords = new WmsHintKeywords({
   popupDivId: 'popup_window_content',
   addressFormClass: 'address-change-form'
});


