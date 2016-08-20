/**
 * Copy Custom Options.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Team
 * that is bundled with this package of Medma Infomatix Pvt. Ltd.
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Medma Support does not guarantee correct work of this package
 * on any other Magento edition except Magento COMMUNITY edition.
 * =================================================================
**/ 

/** function to select and deselect checkboxes **/
function selectAllCheckbox(id)
{
	jQuery.noConflict();
	select	=	jQuery('#'+id).val();
	if(select==0){
				jQuery('#'+id).val(1);
       	jQuery('.'+id).attr("checked",true); /** select all the checkboxes **/
  }
  else{
			jQuery('#'+id).val(0);
	   jQuery('.'+id).attr("checked",false); /** deselect all the checkboxes **/
 }
}
function getSelectedProductOptions()
{
		/*serialzing the form data */
		var data = $('custom_options_form').serialize(true);		
		var url = $('custom_options_form').action; 
		redirect = $('redirectState').getValue();
		/** getting the product list **/
		var checkedList = [];
		$$('.copyTo').each(function(ele){
			 if( $(ele).checked )
			 {
				   checkedList.push($(ele).value);
			 }
		});
		/**ajax to get the array of custom options **/
		try {
        new Ajax.Request(url,
				{
					method:'post',
					parameters: data,  
          asynchronous: true,	
					requestHeaders: {Accept: 'application/json'}, 
					onSuccess: function(transport)
					{
						if(transport.responseText==1)
							window.location=redirect+'state/1';	/** no custom option selected  **/	
						else							
							copyOptionsToProducts(transport.responseText,checkedList,0)
					},
					onFailure: function(error_msg)
					{ 
						alert('error, please try again');
						alert(error_msg);
					}
				});
        } 
				catch (e) { alert(e);
        }	
}
function copyOptionsToProducts(options,products,start)
{
	/** recursively calling the function 
	to copy custom options to the products
	one by one  **/
	var url = $('allOptions').value+'product/'+products[start];
	//var data	=	[];
	redirect = $('redirectState').getValue(); //alert(options); return;
	//data['options']=options;
	//data	=	JSON.stringify(data); //alert(data);
	$('productoptionsval').setValue(options);
	var data = $('custom_options_form_request').serialize(true);			
	len=products.length;
	if(len>0){
	try {
        new Ajax.Request(url,
				{
					method:'post',
					parameters: data,  
          asynchronous: true,
					requestHeaders: {Accept: 'application/json'}, 
					onSuccess: function(transport)
					{
						/** recursively calling th function**/
						if(len>start+1)
							copyOptionsToProducts(options,products,start+1);
						else
								window.location=redirect+'state/0';	/** custom options copied successfully  **/	
					},
					onFailure: function(error_msg)
					{ 
						alert('error, please try again');
						alert(error_msg);
					}
				});
        } 
				catch (e) { alert(e);
        }	
	}
	else
		window.location=redirect+'state/2';	/** no product selected  **/	
}

