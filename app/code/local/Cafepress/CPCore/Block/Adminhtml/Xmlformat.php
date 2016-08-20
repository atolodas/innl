<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat extends Mage_Adminhtml_Block_Widget_Container {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('cpcore/xmlformats.phtml');
    }

    protected function _prepareLayout() {
        ##TODO INL: REMOVE FOR REMOTE FORMAT
        $this->_addButton('add_new', array(
            'label' => Mage::helper('cpcore')->__('Add Format'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class' => 'add'
        ));

        $this->setChild('grid', $this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_grid', 'xmlformat.grid')->setSaveParametersInSession(true));
        return parent::_prepareLayout();
    }

    public function getTabUrl() {
        return $this->getUrl('*/*/xmlformats', array('_current' => true));
    }

    public function getTabClass() {
        return 'ajax';
    }

    public function getTabLabel() {
        return Mage::helper('cpcore')->__('Xml Formats');
    }

    public function getTabTitle() {
        return Mage::helper('cpcore')->__('Xml Formats');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getHeaderWidth() {
        return 'width:50%;';
    }

    public function getHeaderText() {
        return Mage::helper('cpcore')->__('Xml Formats');
    }

    public function getHeaderHtml() {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode() {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

}
