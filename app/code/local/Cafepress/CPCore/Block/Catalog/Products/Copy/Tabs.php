<?php

class Cafepress_CPCore_Block_Catalog_Products_Copy_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_products_copy_tab_attributes';
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('products_copy_form');
        $this->setTitle(Mage::helper('cpcore')->__('Copy Cafe Press Products'));
    }

    protected function _prepareLayout()
    {
        $activeTab = $this->getRequest()->getParam('action');
//        $activeTab = $activeTab?$activeTab:'select_section';
        $activeTab = $activeTab?$activeTab:'select_store';

        switch($activeTab){
            case 'select_store':
                $this->addTab('select_store', array(
                    'label'     => Mage::helper('cpcore')->__('Select Store'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_products_copy_tab_selectstore')->toHtml()),
                    'active'    => $activeTab == 'select_store'?true:false
                ));
                break;
            case 'select_section':
                $this->addTab('select_section', array(
                    'label'     => Mage::helper('cpcore')->__('Select Section'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_products_copy_tab_selectsection')->toHtml()),
                    'active'    => $activeTab == 'select_section'?true:false
                ));
                break;
            case 'select_products':
                $this->addTab('select_products', array(
                    'label'     => Mage::helper('cpcore')->__('Select Products'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_products_copy_tab_selectproducts')->toHtml()),
                    'active'    => $activeTab == 'select_products'?true:false
                ));
                break;
            case 'products_copied':
//                Mage::log($_SESSION['cp_store_products'], null, 'lomantik.log');
                $copy_log = Mage::getModel('cpcore/cafepress_product')->copyRemoteProduct($_SESSION['cp_copy_data']);
                $_SESSION['cp_copy_log'] = $copy_log;

                $this->addTab('products_copied', array(
                    'label'     => Mage::helper('cpcore')->__('Products Copied'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/catalog_products_copy_tab_productscopied')->toHtml()),
                    'active'    => $activeTab == 'products_copied'?true:false
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