<?php

class Cafepress_CPWms_Block_Adminhtml_Order_Switcher extends Mage_Adminhtml_Block_Template
{
    /**
     * @var array
     */
    protected $_orderIds;

    protected $_orderVarName = 'order';

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cpwms/order/switcher.phtml');
//        $this->setUseConfirm(false);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('All Orders'));
    }

    /**
     * Deprecated
     */
//    public function getWebsiteCollection()
//    {
//        $collection = Mage::getModel('core/website')->getResourceCollection();
//
//        $websiteIds = $this->getWebsiteIds();
//        if (!is_null($websiteIds)) {
//            $collection->addIdFilter($this->getWebsiteIds());
//        }
//
//        return $collection->load();
//    }
//
//    /**
//     * Get websites
//     *
//     * @return array
//     */
//    public function getWebsites()
//    {
//        $websites = Mage::app()->getWebsites();
//        if ($websiteIds = $this->getWebsiteIds()) {
//            foreach ($websites as $websiteId => $website) {
//                if (!in_array($websiteId, $websiteIds)) {
//                    unset($websites[$websiteId]);
//                }
//            }
//        }
//        return $websites;
//    }
//
//    /**
//     * Deprecated
//     */
//    public function getGroupCollection($website)
//    {
//        if (!$website instanceof Mage_Core_Model_Website) {
//            $website = Mage::getModel('core/website')->load($website);
//        }
//        return $website->getGroupCollection();
//    }
//
//    /**
//     * Get store groups for specified website
//     *
//     * @param Mage_Core_Model_Website $website
//     * @return array
//     */
//    public function getStoreGroups($website)
//    {
//        if (!$website instanceof Mage_Core_Model_Website) {
//            $website = Mage::app()->getWebsite($website);
//        }
//        return $website->getGroups();
//    }
//
//    /**
//     * Deprecated
//     */
//    public function getOrderCollection($group)
//    {
//        if (!$group instanceof Mage_Core_Model_Store_Group) {
//            $group = Mage::getModel('core/order_group')->load($group);
//        }
//        $orders = $group->getOrderCollection();
//        $_orderIds = $this->getOrderIds();
//        if (!empty($_storeIds)) {
//            $orders->addIdFilter($_orderIds);
//        }
//        return $orders;
//    }
//
//    /**
//     * Get store views for specified store group
//     *
//     * @param Mage_Core_Model_Store_Group $group
//     * @return array
//     */
    public function getOrders()
    {
        $orders = Mage::getModel('sales/order')->getCollection();
        return $orders;
    }
//
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_orderVarName => null));
    }

//    public function setOrderVarName($varName)
//    {
//        $this->_orderVarName = $varName;
//        return $this;
//    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam($this->_orderVarName);
    }
//
//    public function setOrderIds($storeIds)
//    {
//        $this->_orderIds = $orderIds;
//        return $this;
//    }
//
//    public function getOrderIds()
//    {
//        return $this->_orderIds;
//    }
//
//    public function isShow()
//    {
//        return !Mage::app()->isSingleStoreMode();
//    }
//
//    protected function _toHtml()
//    {
//        if (!Mage::app()->isSingleStoreMode()) {
//            return parent::_toHtml();
//        }
//        return '';
//    }
//
//    /**
//     * Set/Get whether the switcher should show default option
//     *
//     * @param bool $hasDefaultOption
//     * @return bool
//     */
//    public function hasDefaultOption($hasDefaultOption = null)
//    {
//        if (null !== $hasDefaultOption) {
//            $this->_hasDefaultOption = $hasDefaultOption;
//        }
//        return $this->_hasDefaultOption;
//    }
}
