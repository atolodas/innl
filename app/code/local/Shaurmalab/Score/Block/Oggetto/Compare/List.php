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
 * @category    Mage
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score oggettos compare block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Compare_List extends Shaurmalab_Score_Block_Oggetto_Compare_Abstract
{
    /**
     * Oggetto Compare items collection
     *
     * @var Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    protected $_items;

    /**
     * Compare Oggettos comparable attributes cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = false;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * Retrieve url for adding oggetto to wishlist with params
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getAddToWishlistUrl($oggetto)
    {
        $continueUrl    = Mage::helper('core')->urlEncode($this->getUrl('customer/account'));
        $urlParamName   = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $params = array(
            $urlParamName   => $continueUrl
        );

        return $this->helper('wishlist')->getAddUrlWithParams($oggetto, $params);
    }

    /**
     * Preparing layout
     *
     * @return Shaurmalab_Score_Block_Oggetto_Compare_List
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('score')->__('Oggettos Comparison List') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Oggetto Compare items collection
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            Mage::helper('score/oggetto_compare')->setAllowUsedFlat(false);

            $this->_items = Mage::getResourceModel('score/oggetto_compare_item_collection')
                ->useOggettoItem(true)
                ->setStoreId(Mage::app()->getStore()->getId());

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_items->setCustomerId($this->_customerId);
            } else {
                $this->_items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            $this->_items
                ->addAttributeToSelect(Mage::getSingleton('score/config')->getOggettoAttributes())
                ->loadComparableAttributes()
                ->addMinimalPrice()
                ->addTaxPercents();

            Mage::getSingleton('score/oggetto_visibility')
                ->addVisibleInSiteFilterToCollection($this->_items);
        }

        return $this->_items;
    }

    /**
     * Retrieve Oggetto Compare Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
        }

        return $this->_attributes;
    }

    /**
     * Retrieve Oggetto Attribute Value
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Shaurmalab_Score_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getOggettoAttributeValue($oggetto, $attribute)
    {
        if (!$oggetto->hasData($attribute->getAttributeCode())) {
            return Mage::helper('score')->__('N/A');
        }

        if ($attribute->getSourceModel()
            || in_array($attribute->getFrontendInput(), array('select','boolean','multiselect'))
        ) {
            //$value = $attribute->getSource()->getOptionText($oggetto->getData($attribute->getAttributeCode()));
            $value = $attribute->getFrontend()->getValue($oggetto);
        } else {
            $value = $oggetto->getData($attribute->getAttributeCode());
        }
        return ((string)$value == '') ? Mage::helper('score')->__('No') : $value;
    }

    /**
     * Retrieve Print URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true, 'print'=>1));
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return Shaurmalab_Score_Block_Oggetto_Compare_List
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
