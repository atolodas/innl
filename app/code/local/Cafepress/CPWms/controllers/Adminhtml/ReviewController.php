<?php

class Cafepress_CPWms_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpwms')->__('XML Format Review'), Mage::helper('cpwms')->__('XML Format Review'));

        $this->_title($this->__('cpwms'))->_title($this->__('XML Format Review'));
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
        $this->_title($this->__('cpwms'))->_title($this->__('Review Wms'));

        $this->_initAction()
                ->renderLayout();
    }

    protected function _initXmlformat() {
        $xmlformatId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $xmlformat = Mage::getModel('cpwms/xmlformat')
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
                $this->getLayout()->createBlock('cpwms/adminhtml_review_grid')->toHtml()
        );
    }

}