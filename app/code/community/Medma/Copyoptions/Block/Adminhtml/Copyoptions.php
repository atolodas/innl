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
class Medma_Copyoptions_Block_Adminhtml_Copyoptions extends Mage_Adminhtml_Block_Template
{
 	/**
		get product collection which has custom options
 	**/
  public function _getCollectionWithOptions()
  {
		$catalogModel	=	Mage::getModel('catalog/product')
										->getCollection()
										->addAttributeToSelect('name')  
										->addFieldToFilter('has_options',1)
										->setOrder('name', 'ASC');

		return $catalogModel;
  }
	/**
		get product collection with all the products
	**/
	public function _getCollectionWithoutOptions()
  {
		$catalogModel	=	Mage::getModel('catalog/product')
										->getCollection()
										->addAttributeToSelect('name')										
										->setOrder('name', 'ASC');

		return $catalogModel;
  }
	/**
		get copypost action url
	**/
  public function getCopyUrl()
  {
		return Mage::helper('adminhtml')->getUrl('copyoptions/adminhtml_index/copypost');
  }
	/**
		get copy all options action url
	**/
  public function getcopyAllOptionsUrl()
  {
		return Mage::helper('adminhtml')->getUrl('copyoptions/adminhtml_index/copyAllOptions');
  }
	/**
		get redirect state url
	**/
  public function redirectState()
  {
		return Mage::helper('adminhtml')->getUrl('copyoptions/adminhtml_index/redirect');
  }
	/**
		get delete action url
	**/	
  public function getDeleteUrl()
  {
		return Mage::helper('adminhtml')->getUrl('copyoptions/adminhtml_index/delete');
  }
	/** get all the stores in magento **/	
	public function getStores()
  {
			$allStores = Mage::app()->getStores();
			return $allStores;
  }
	/** get all the root categories of stores **/ 
  public function getRootCategories($eachStoreId)
  {
  	$storeId 			= Mage::app()->getStore($eachStoreId)->getId();
		$rootCat 			=	Mage::app()->getStore($storeId)->getRootCategoryId();
		$_categories 	= Mage::getModel('catalog/category')->getCategories($rootCat);
		return $_categories;
  }
	/** get all the categories recursively **/
  public function getCategoriesRecursively($categories)
  {
  	$array='';
		$array= '<ul>';
    foreach($categories as $category) 
    {
        $cat = Mage::getModel('catalog/category')->load($category->getId());
        $count = $cat->getProductCount();
        $array .= '<li><input type="checkbox" name="categories[]" value="'.$category->getId().'" />'.$category->getName();
        if($category->hasChildren()) {
            $children = Mage::getModel('catalog/category')->getCategories($category->getId());
             $array .=  $this->getCategoriesRecursively($children);
            }
         $array .= '</li>';
    }
    return  $array . '</ul>';
  } 
}
