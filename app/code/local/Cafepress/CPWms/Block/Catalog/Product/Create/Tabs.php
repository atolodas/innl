<?php

class Cafepress_CPWms_Block_Catalog_Product_Create_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'adminhtml/catalog_product_create_tab_attributes';
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_create_form');
        $this->setTitle(Mage::helper('cpwms')->__('Create Cafe Press Product'));
    }

    protected function _prepareLayout()
    {
        $activeTab = $this->getRequest()->getParam('action');
        $activeTab = $activeTab?$activeTab:'setprint';

        switch($activeTab){
            case 'setprint':
                $this->addTab('setprint', array(
                    'label'     => Mage::helper('cpwms')->__('CP Print'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/catalog_product_create_tab_print')->toHtml()),
                    'active'    => $activeTab == 'setprint'?true:false
                ));
                break;
            case 'merchandise':
                $categories = Mage::getModel('merchandise/merchandise')->getCategories();
                $first = true;
                foreach($categories as $category_id => $category){
                    $result = $this->createProductsHtml($category_id, $first);

                    if($result['count'] > 0){
                        $this->addTab('merchandise_'.$category_id, array(
                            'label'     => Mage::helper('cpwms')->__($category),
                            'content'   => $result['html']
                        ));
                        if($first){
                            $first = false;
                        }
                    }
                }
                break;
            case 'setparams':
                Mage::register('cp_design_id', $this->getRequest()->getParam('design_id'));
                $this->addTab('setparams', array(
                    'label'     => Mage::helper('cpwms')->__('Product Data'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/catalog_product_create_tab_setparams')->toHtml()),
                    'active'    => $activeTab == 'setparams'?true:false
                ));
                break;
            case 'createremote':
                Mage::register('cp_design_id', $this->getRequest()->getParam('design_id'));

                $data = $this->getRequest()->getParams();
                $product = Mage::getModel('cpwms/cafepress_product')->createRemoteProduct($data);

                if($product->getTypeId() == 'configurable'){
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'admin/catalog_product/edit/id/'.$product->getId().'/tab/product_info_tabs_cp_simples_creation/');
                } else{
                    Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'admin/catalog_product/edit/id/'.$product->getId().'/');
                }

//                $this->addTab('createremote', array(
//                    'label'     => Mage::helper('cpwms')->__('Product Data'),
//                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/catalog_product_create_tab_createremote')->toHtml()),
//                    'active'    => $activeTab == 'createremote'?true:false
//                ));

                break;
        }

        return parent::_prepareLayout();
    }

    protected function createProductsHtml($category_id, $first){
        $_SESSION['cafepress_merchandise_collection'] = serialize(Mage::getModel('merchandise/merchandise')->getFormattedData());

        $html = '';
        $product_types = Mage::getModel('merchandise/merchandise')->getCollection()->addFieldToFilter('category_id', $category_id);
        $count = 0;
        $col_max = 5;
        $col = 0;

        if($first){
            $html .= '<input id="product_type" type="hidden" name="product_type">';
            $html .= '<input id="merchant_content" type="hidden" name="merchant_content">';
            $html .= '<input id="merchant_content_all" type="hidden" name="merchant_content_all">';
        }

        $html .= '<div class="cafepress_imagegrid_wrapper"><table class="cafepress_imagegrid" cellspacing="10"><tr>';
        foreach($product_types as $product_type){
            if($product_type->getImageUrl() != "" && file_exists(Mage::getBaseDir('media').DS.'cafepress/images/'.basename($product_type->getImageUrl()))){

                $html .= '<td width="150"><div class="cp_type_element" onclick="selectElement(this)">
                    <img width="150" height="150" src="'.$product_type->getImageUrl().'"><br/>'.$product_type->getName().'
                    <input class="cp_type_id" type="hidden" value="'.$product_type->getTypeId().'">
                    <input class="cp_type_content" type="hidden" value="'.htmlspecialchars($product_type->getContent()).'">
                    </div></td>';
                if($col < $col_max){
                    $col++;
                } else{
                    $html .= '</tr>';
                    $col = 0;
                }
                $count++;
            }
        }
        $html .= '</table></div>';
        return array('html' => $html, 'count' => $count);
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
