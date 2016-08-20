<?php

class Cafepress_CPCore_Adminhtml_ViewxmlsController extends Mage_Adminhtml_Controller_Action
{
    public function _construct() {
        parent::_construct();
    }

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/cpcore')
            ->_addBreadcrumb(Mage::helper('cpcore')->__('View XMLs'), Mage::helper('cpcore')->__('View XMLs'));

        $this->_title($this->__('cpcore'))->_title($this->__('View XMLs'));
        return $this;
    }

    public function indexAction() {
        $xmlFiles = Mage::getModel('cpcore/xml')->fetchFiles();
        Mage::register('found_inbound_xmls', $xmlFiles['inbound']);
        Mage::register('found_outbound_xmls', $xmlFiles['outbound']);

        $this->loadLayout(array(
            'default',
            'cpcore_viewxmls'
        ));
        $this->_setActiveMenu('sales/cpcore');

        $this->renderLayout();
    }
}
