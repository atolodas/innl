<?php

class Cafepress_CPCore_Block_Catalog_Shop_Copy_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_shop_copy_tab_attributes';
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('shop_copy_form');
        $this->setTitle(Mage::helper('cpcore')->__('Copy Cafe Press Shop'));
    }

    protected function _prepareLayout()
    {
        $activeTab = $this->getRequest()->getParam('action');
        $activeTab = $activeTab?$activeTab:'accounts_data';

        switch($activeTab){
            case 'accounts_data':
                $this->addTab('accounts_data', array(
                    'label'     => Mage::helper('cpcore')->__('Accounts Data'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_shop_copy_tab_accountsdata')->toHtml()),
                    'active'    => $activeTab == 'accounts_data'?true:false
                ));
                break;
            case 'select_stores':
                $this->addTab('select_stores', array(
                    'label'     => Mage::helper('cpcore')->__('Select Shops'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_shop_copy_tab_selectstores')->toHtml()),
                    'active'    => $activeTab == 'select_stores'?true:false
                ));
                break;
            case 'copy_log':
                $this->addTab('copy_log', array(
                    'label'     => Mage::helper('cpcore')->__('Copy Log'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_shop_copy_tab_copylog')->toHtml()),
                    'active'    => $activeTab == 'copy_log'?true:false
                ));
                break;
        }

        return parent::_prepareLayout();
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }

    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    public function getProduct()
    {
        $product = Mage::registry('product');
        if (!$product){
            $productId = $this->getRequest()->getParam('id');
            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getRequest()->getParam('store', 0))->load($productId);
            Mage::register('product', $product);
        }

        return $product;
    }
}