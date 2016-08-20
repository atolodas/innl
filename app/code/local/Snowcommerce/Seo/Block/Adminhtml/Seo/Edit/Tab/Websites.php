<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Stores tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Snowcommerce_Seo_Block_Adminhtml_Seo_Edit_Tab_Websites extends Mage_Adminhtml_Block_Store_Switcher
{
    protected $_storeFromHtml;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('seo/websites.phtml');
    }

    /**
     * Retrieve edited product model instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getPage()
    {
    	
        return Mage::registry('seo_data');
    }

    public function getStoreId()
    {
        return $this->getPage()->getStoreId();
    }

    public function getProductId()
    {
        return $this->getPage()->getSeoId();
    }

	public function getWebsites()
    {
    	$websites = explode(',',$this->getPage()->getWebsiteId());
    	if(is_array($websites)) { 
        	return $websites;
    	}
    	else { 
    		return array($websites);
    	}
    }
    
	public function getPageStores()
    {
    	$stores = explode(',',$this->getPage()->getStoreId());
    	if(is_array($stores)) { 
        	return $stores;
    	}
    	else { 
    		return array($stores);
    	}
    }

    public function hasWebsite($websiteId)
    {
    	return in_array($websiteId, $this->getWebsites());
    }
    
	public function hasStore($storeId)
    {
    	return in_array($storeId, $this->getPageStores());
    }

    /**
     * Check websites block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getPage()->getWebsitesReadonly();
    }

    public function getStoreName($storeId)
    {
        return Mage::app()->getStore($storeId)->getName();
    }

    public function getChooseFromStoreHtml($id)
    {
       // if (!$this->_storeFromHtml) {
            //$this->_storeFromHtml = '<input type="checkbox" name="copy_to_stores[__store_identifier__]">';
            //$this->_storeFromHtml.= '<option value="0">'.Mage::helper('catalog')->__('Default Values').'</option>';
//            foreach ($this->getWebsiteCollection() as $_website) {
//                if (!$this->hasWebsite($_website->getId())) {
//                    continue;
//                }
//                $this->_storeFromHtml .= '<optgroup label="' . $_website->getName() . '"></optgroup>';
//                foreach ($this->getGroupCollection($_website) as $_group) {
//                    $this->_storeFromHtml .= '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $_group->getName() . '">';
//                    foreach ($this->getStoreCollection($_group) as $_store) {
//                        $this->_storeFromHtml .= '<option value="' . $_store->getId() . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $_store->getName() . '</option>';
//                    }
//                }
//                $this->_storeFromHtml .= '</optgroup>';
//            }
//            $this->_storeFromHtml.= '</select>';
			  $this->_storeFromHtml= '<input type="checkbox" onclick="makeStr()" class="store_ids" name="store[__store_identifier__]" value="__store_identifier__" />';
       // }
        return str_replace('__store_identifier__', $id, $this->_storeFromHtml);
    }
}
