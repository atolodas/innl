<?php

class Mage_Adminhtml_Block_Catalog_Product_Create_Tabs_Grouped extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addTab('super', array(
            'label'     => Mage::helper('catalog')->__('Associated Products'),
            'url'       => $this->getUrl('*/*/superGroup', array('_current'=>true)),
            'class'     => 'ajax',
        ));
    }
}
