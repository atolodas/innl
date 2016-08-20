<?php

class Cafepress_CPCore_Block_Adminhtml_Replacer extends Mage_Adminhtml_Block_Widget_Container {

    protected function _prepareLayout() {
        $this->_addButton('add_new', array(
            'label' => Mage::helper('cpcore')->__('Add New replacer'),
            'onclick' => "setLocation('{$this->getUrl('*/*/edit')}')",
            'class' => 'add'
        ));

        $this->setChild('grid', $this->getLayout()
                ->createBlock('cpcore/adminhtml_replacer_grid', 'cpcore_replacer.grid')
                ->setSaveParametersInSession(true));
        return parent::_prepareLayout();
    }

    public function getTabUrl() {
        return $this->getUrl('*/*/replacer', array('_current' => true));
    }

    public function getTabClass() {
        return 'ajax';
    }

    public function getTabLabel() {
        return Mage::helper('cpcore')->__('WMS replacer');
    }

    public function getTabTitle() {
        return Mage::helper('cpcore')->__('WMS replacer');
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
        return Mage::helper('cpcore')->__('WMS replacer');
    }

    public function getHeaderHtml() {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

}
