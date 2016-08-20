<?php

class Cafepress_CPCore_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpcore')->__('XML Format Review'), Mage::helper('cpcore')->__('XML Format Review'));

        $this->_title($this->__('cpcore'))->_title($this->__('XML Format Review'));
        return $this;
    }

    public function _construct() {

        $session = Mage::getSingleton("customer/session");
        $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
        return parent::_construct();
    }

    /**
     * Show grid
     * 
     */
    public function indexAction() {
        $this->_title($this->__('cpcore'))->_title($this->__('Review Wms'));

        $this->_initAction()
                ->renderLayout();
    }

    protected function _initXmlformat() {
        $xmlformatId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $xmlformat = Mage::getModel('cpcore/xmlformat')
                ->setStoreId($storeId);
        if ($xmlformatId != false) {
            $xmlformat->load($xmlformatId);
        }

        Mage::unregister('current_xmlformat');
        Mage::unregister('xmlformat');
        Mage::register('xmlformat', $xmlformat);
        Mage::register('current_xmlformat', $xmlformat);
        return $xmlformat;
    }

    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpcore/adminhtml_review_grid')->toHtml()
        );
    }

}