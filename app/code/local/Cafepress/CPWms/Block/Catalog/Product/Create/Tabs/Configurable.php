<?php

class Mage_Adminhtml_Block_Catalog_Product_Create_Tabs_Configurable extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
//        $product = $this->getProduct();

//        if (!($superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds())) {
            $this->addTab('super_settings', array(
                'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
                'active'    => true
            ));

//        } else {
//            parent::_prepareLayout();
//
//            $this->addTab('configurable', array(
//                'label'     => Mage::helper('catalog')->__('Associated Products'),
//                'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config', 'admin.super.config.product')
//                    ->setProductId($this->getRequest()->getParam('id'))
//                    ->toHtml(),
//            ));
//            $this->bindShadowTabs('configurable', 'customer_options');
//        }
    }
}
