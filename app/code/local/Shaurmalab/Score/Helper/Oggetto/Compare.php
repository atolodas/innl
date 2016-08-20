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
 * Score Oggetto Compare Helper
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Oggetto_Compare extends Mage_Core_Helper_Url
{
    /**
     * Oggetto Compare Items Collection
     *
     * @var Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    protected $_itemCollection;

    /**
     * Oggetto Comapare Items Collection has items flag
     *
     * @var bool
     */
    protected $_hasItems;

    /**
     * Allow used Flat score oggetto for oggetto compare items collection
     *
     * @var bool
     */
    protected $_allowUsedFlat = true;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Retrieve Score Session instance
     *
     * @return Shaurmalab_Score_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('score/session');
    }

    /**
     * Retrieve compare list url
     *
     * @return string
     */
    public function getListUrl()
    {
        $itemIds = array();
        foreach ($this->getItemCollection() as $item) {
            $itemIds[] = $item->getId();
        }

         $params = array(
            'items' => implode(',', $itemIds),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );

        return $this->_getUrl('score/oggetto_compare', $params);
    }

    /**
     * Get parameters used for build add oggetto to compare list urls
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  array
     */
    protected function _getUrlParams($oggetto)
    {
        return array(
            'oggetto' => $oggetto->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
    }

    /**
     * Retrieve url for adding oggetto to conpare list
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  string
     */
    public function getAddUrl($oggetto)
    {
        return $this->_getUrl('score/oggetto_compare/add', $this->_getUrlParams($oggetto));
    }

    /**
     * Retrive add to wishlist url
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getAddToWishlistUrl($oggetto)
    {
        $beforeCompareUrl = Mage::getSingleton('score/session')->getBeforeCompareUrl();

        $params = array(
            'oggetto' => $oggetto->getId(),
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrive add to cart url
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getAddToCartUrl($oggetto)
    {
        $beforeCompareUrl = $this->_getSingletonModel('score/session')->getBeforeCompareUrl();
        $params = array(
            'oggetto' => $oggetto->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl),
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()
        );

        return $this->_getUrl('checkout/cart/add', $params);
    }

    /**
     * Retrieve remove item from compare list url
     *
     * @param   $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = array(
            'oggetto' => $item->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('score/oggetto_compare/remove', $params);
    }

    /**
     * Retrieve clear compare list url
     *
     * @return string
     */
    public function getClearListUrl()
    {
        $params = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('score/oggetto_compare/clear', $params);
    }

    /**
     * Retrieve compare list items collection
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    public function getItemCollection()
    {
        if (!$this->_itemCollection) {
            $this->_itemCollection = Mage::getResourceModel('score/oggetto_compare_item_collection')
                ->useOggettoItem(true)
                ->setStoreId(Mage::app()->getStore()->getId());

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->_itemCollection->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_itemCollection->setCustomerId($this->_customerId);
            } else {
                $this->_itemCollection->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            Mage::getSingleton('score/oggetto_visibility')
                ->addVisibleInSiteFilterToCollection($this->_itemCollection);

            /* Price data is added to consider item stock status using price index */
            $this->_itemCollection->addPriceData();

            $this->_itemCollection->addAttributeToSelect('name')
                ->addUrlRewrite()
                ->load();

            /* update compare items count */
            $this->_getSession()->setScoreCompareItemsCount(count($this->_itemCollection));
        }

        return $this->_itemCollection;
    }

    /**
     * Calculate cache oggetto compare collection
     *
     * @param  bool $logout
     * @return Shaurmalab_Score_Helper_Oggetto_Compare
     */
    public function calculate($logout = false)
    {
        // first visit
        if (!$this->_getSession()->hasScoreCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            /** @var $collection Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection */
            $collection = Mage::getResourceModel('score/oggetto_compare_item_collection')
                ->useOggettoItem(true);
            if (!$logout && Mage::getSingleton('customer/session')->isLoggedIn()) {
                $collection->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
            } elseif ($this->_customerId) {
                $collection->setCustomerId($this->_customerId);
            } else {
                $collection->setVisitorId(Mage::getSingleton('log/visitor')->getId());
            }

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData();

            Mage::getSingleton('score/oggetto_visibility')
                ->addVisibleInSiteFilterToCollection($collection);

            $count = $collection->getSize();
        }

        $this->_getSession()->setScoreCompareItemsCount($count);

        return $this;
    }

    /**
     * Retrieve count of items in compare list
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_getSession()->hasScoreCompareItemsCount()) {
            $this->calculate();
        }

        return $this->_getSession()->getScoreCompareItemsCount();
    }

    /**
     * Check has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Set is allow used flat (for collection)
     *
     * @param bool $flag
     * @return Shaurmalab_Score_Helper_Oggetto_Compare
     */
    public function setAllowUsedFlat($flag)
    {
        $this->_allowUsedFlat = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve is allow used flat (for collection)
     *
     * @return bool
     */
    public function getAllowUsedFlat()
    {
        return $this->_allowUsedFlat;
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return Shaurmalab_Score_Helper_Oggetto_Compare
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
