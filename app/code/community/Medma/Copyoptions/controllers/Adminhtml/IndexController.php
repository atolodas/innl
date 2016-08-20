<?php
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
class Medma_Copyoptions_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function preDispatch()
	{
		parent::preDispatch();        
		$generalEmail = Mage::getStoreConfig('trans_email/ident_general/email');
    $domainName = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		Mage::dispatchEvent('medma_domain_authentication',
		array('email' => $generalEmail, 'domain_name'=>$domainName,));		
   }
	/** Copy Custom Options to Products **/
	public function copytoproductAction()
	{
		 	$this->loadLayout();
			$this->_setActiveMenu('catalog');		
			$this->getLayout()->getBlock('head')->setTitle($this->__('Copy Custom Options to Products'));  
			$this->renderLayout();
	}
	/** Delete Custom Options From Products **/
	public function deletefromproductAction()
	{
		 	$this->loadLayout();
			$this->_setActiveMenu('catalog');		
			$this->getLayout()->getBlock('head')->setTitle($this->__('Delete Custom Options From Products'));  
			$this->renderLayout();
	}	
	/**	
		copies the custom options from one product to other products
	**/
	public function copypostAction()
	{
		$params					=		$this->getRequest()->getParams();
		$copyto 				= 	$this->getRequest()->getParam('copyto'); 
		$copyoptions 		=		$this->getRequest()->getParam('options'); 
		$catalogModel		=		Mage::getModel('catalog/product');	
		$i=0;	
		/**  scanning selected options of the products **/	
		foreach($copyoptions as $product=>$value)
		{
				$productCopyOptions		=		$catalogModel->load($product);
				$opt									=		$productCopyOptions->getOptions();					
				$newOptionsVal				=		array();							
				foreach($opt as $o)
				{
								if(in_array($o->getId(),$value))
								{
												/** creating array of selected custom options **/
												$optionValues 	= 	$o->getValues();								
												$newOptions[$i]['title']							= 	$o->getTitle();
												$newOptions[$i]['is_require']					= 	$o->getIsRequire();
												$newOptions[$i]['type']								=		$o->getType();
												$newOptions[$i]['sort_order']					=		$o->getSortOrder();									
												$newOptions[$i]['sku']								=		$o->getSku();	
												$newOptions[$i]['max_characters'] 		=		$o->getMaxCharacters();	
												$newOptions[$i]['file_extension']			=		$o->getFileExtension();	
												$newOptions[$i]['image_size_x'] 			= 	$o->getImageSizeX();	
												$newOptions[$i]['image_size_y'] 			=		$o->getImageSizeY();	
												$newOptions[$i]['default_title'] 			= 	$o->getDefaultTitle();	
												$newOptions[$i]['store_title'] 				= 	$o->getStoreTitle();	
												$newOptions[$i]['default_price'] 			= 	$o->getDefaultPrice();	
												$newOptions[$i]['default_price_type']	= 	$o->getDefaultPriceType();	
												$newOptions[$i]['store_price'] 				= 	$o->getStorePrice();	
												$newOptions[$i]['store_price_type'] 	= 	$o->getStorePriceType();	
												$newOptions[$i]['price'] 							= 	$o->getPrice();	
												$newOptions[$i]['price_type'] 				= 	$o->getPriceType();								
												$newOptionsVal	=		array();		
												foreach($optionValues as $val)								
												{
														$copyValues					=		array();						
														$newOptionsValues		=		array(); //array of custom options values
														$newOptionsValues['sku'] 									= 	$val->getSku();
														$newOptionsValues['sort_order'] 					=		$val->getSortOrder();
														$newOptionsValues['default_title'] 				= 	$val->getDefaultTitle();
														$newOptionsValues['store_title']					= 	$val->getStoreTitle();
														$newOptionsValues['title'] 								= 	$val->getTitle();
														$newOptionsValues['default_price'] 				= 	$val->getDefaultPrice();
														$newOptionsValues['default_price_type'] 	= 	$val->getDefaultPriceType();
														$newOptionsValues['store_price'] 					= 	$val->getStorePrice();
														$newOptionsValues['store_price_type'] 		= 	$val->getStorePriceType();
														$newOptionsValues['price'] 								= 	$val->getPrice();
														$newOptionsValues['price_type'] 					= 	$val->getPriceType();
														$newOptionsVal[]		=		($newOptionsValues);	
												} 
												$newOptions[$i]['values']			=		$newOptionsVal;		//custom option values 
												$i++;
						}
				}				
				
		}
		if(count($newOptions)<1)
		{
			echo 1;
			return;
		}
		/** returning array of selected custom options values **/
		$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'application/json',true)->setBody(Zend_Json::encode($newOptions));
		return; 	
	}

	public function copyAllOptionsAction()
	{
			
			$product			=	$this->getRequest()->getParam('product');
			$copyoptions 	=	$this->getRequest()->getParam('productoptionsval'); 
			$newOptions		=	json_decode($copyoptions,true); 
			/** adding array of custom options to the selected product **/
			foreach($newOptions as $_opt)
			{																
					$option_product			=		Mage::getModel('catalog/product')->load($product);	
					$custom_options 		= 	$option_product->getOptions();
					$option_product->setHasOptions(1)->save();
					$opt 								= 	Mage::getModel('catalog/product_option');
					$opt->setProduct($option_product);
					$opt->addOption($_opt);
					$opt->saveOptions(); //saving custom options
					$option_product->save();																
			}
			$this->getResponse()->clearHeaders()->setHeader('Content-Type', 'application/json',true)->setBody(Zend_Json::encode(array()));
			return; 
	}
	public function deleteAction()
	{
		$deleteoptions =	$this->getRequest()->getPost('options'); //getting parameters from the form		
		if(empty($deleteoptions))
		{
			Mage::getSingleton('adminhtml/session')->addError('Select Products to delete custom options.');
			$this->_redirectReferer('');
			return;
		}
		$catalogModel		=		Mage::getModel('catalog/product');	
		foreach($deleteoptions as $product=>$value)
		{
				$productCopyOptions		=		$catalogModel->load($product);
				$opt									=		$productCopyOptions->getOptions();					
				$newOptionsVal				=		array();							
				foreach($opt as $o)
				{
						if(in_array($o->getId(),$value))
						{	
							/** delete the selected custom option **/	
							$o->delete();
						}
				}
		}								
		Mage::getSingleton('adminhtml/session')->addSuccess('Custom Options Deleted Successfully.');
		$this->_redirectReferer('');
	}
	/** redirect based on state **/
	public function redirectAction() 
	{
		$state 	= $this->getRequest()->getParam('state');
		if($state==1)
			Mage::getSingleton('adminhtml/session')->addError($this->__('No Custom Options Selected.'));	
		if($state==2)			
			Mage::getSingleton('adminhtml/session')->addError($this->__('No product selected. Please select product to copy.'));	
		if($state==0)
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Custom options copied successfully.'));	
		$this->_redirectReferer('');
		return;
	}	

}
?>
