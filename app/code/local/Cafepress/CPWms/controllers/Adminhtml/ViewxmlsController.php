<?php

class Cafepress_CPWms_Adminhtml_ViewxmlsController extends Mage_Adminhtml_Controller_Action
{
    public function _construct() {
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/wms')
            ->_addBreadcrumb(Mage::helper('wms')->__('View XMLs'), Mage::helper('cpwms')->__('View XMLs'));

        $this->_title($this->__('cpwms'))->_title($this->__('View XMLs'));
        return $this;
    }

    public function indexAction() {
        $xmlFiles = Mage::getModel('cpwms/xml')->fetchFiles();
        Mage::register('found_inbound_xmls', $xmlFiles['inbound']);
        Mage::register('found_outbound_xmls', $xmlFiles['outbound']);

        $this->loadLayout(array(
            'default',
            'cpwms_viewxmls'
        ));
        $this->_setActiveMenu('sales/wms');

        $this->renderLayout();
    }
}
