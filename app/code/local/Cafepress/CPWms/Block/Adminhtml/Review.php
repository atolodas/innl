<?php

class Cafepress_CPWms_Block_Adminhtml_Review extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('cpwms/review.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('grid', $this->getLayout()
                ->createBlock('cpwms/adminhtml_review_grid', 'wms_review.grid')
                ->setSaveParametersInSession(true));
        return parent::_prepareLayout();
    }

    public function getTabUrl() {
        return $this->getUrl('*/*/xmlformats', array('_current' => true));
    }

    public function getTabClass() {
        return 'ajax';
    }

    public function getTabLabel() {
        return Mage::helper('cpwms')->__('Xml Formats Review');
    }

    public function getTabTitle() {
        return Mage::helper('cpwms')->__('Xml Formats Review');
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
        return Mage::helper('cpwms')->__('Xml Formats Review');
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
