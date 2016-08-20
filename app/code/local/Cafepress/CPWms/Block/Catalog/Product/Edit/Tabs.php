<?php

class Cafepress_CPWms_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(Mage::helper('cpwms')->__('Sync Cafe Press Product'));
    }

    protected function _prepareLayout()
    {
        $userToken = $this->getRequest()->getParam('token');
        $activeTab = $this->getRequest()->getParam('action');
        
        $product = $this->getProduct();
        
//        $tabToken = $isToken?true:false;
        
        /*Select Tab*/
        $activeTab = $activeTab?$activeTab:'token';
//        if (!$isToken){
//            $activeTab = 'token';
//        } elseif (!$product->getDesignId()) {
//            $activeTab = 'uploadimage';
//        } else {
//            $activeTab = 'selectmerchant';
//        }
////        $activeTab = 'uploadimage';
//        Mage::register('cpwms_activ_tab', $activeTab);
//        Zend_Debug::dump($activeTab);
        
        if ($activeTab=='token'){
            $this->addTab('token', array(
                'label'     => Mage::helper('cpwms')->__('CP Image'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('cpwms/catalog_product_edit_tab_token')->toHtml()),
                'active'    => $activeTab == 'token'?true:false
            ));
        } elseif ($activeTab=='uploadimage'){
            $this->addTab('upload', array(
                'label'     => Mage::helper('cpwms')->__('Upload Image'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('cpwms/catalog_product_edit_tab_uploadimage')->toHtml()),
//                'active'    => $activeTab == 'token'?true:false
            ));
            
//            if (!($setId = $product->getAttributeSetId())) {
//                $setId = $this->getRequest()->getParam('set', null);
//            }
//
//            if ($setId) {
//                $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
//                    ->setAttributeSetFilter($setId)
//                    ->setSortOrder()
//                    ->load();
//
//                foreach ($groupCollection as $group) {
//                    if ($group->getAttributeGroupName() != 'Images'){
//                        continue;
//                    }
//
//                    $attributes = $product->getAttributes($group->getId(), true);
//                    // do not add groups without attributes
//
//                    foreach ($attributes as $key => $attribute) {
//                        if( !$attribute->getIsVisible() ) {
//                            unset($attributes[$key]);
//                        }
//                    }
//
//                    if (count($attributes)==0) {
//                        continue;
//                    }
//
//                    $this->addTab('group_'.$group->getId(), array(
//                        'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
//                        'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
//    //                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('adminhtml/catalog_product_helper_form_gallery',
//                            'adminhtml.catalog.product.edit.tab.attributes')->setGroup($group)
//                                ->setGroupAttributes($attributes)
//                                ->toHtml()),
//                        'active'    => $activeTab == 'uploadimage'?true:false
//                    ));
//                }
//            }
        } elseif ($activeTab=='selectmerchant'){
            $categories = Mage::getModel('merchandise/merchandise')->getCategories();
            $first = true;
            foreach($categories as $category_id => $category){
                $result = $this->createProductsHtml($category_id, $first);

                if($result['count'] > 0){
                    $this->addTab('selectmerchandise_'.$category_id, array(
                        'label'     => Mage::helper('cpwms')->__($category),
                        'content'   => $result['html']
                    ));
                    if($first){
                        $first = false;
                    }
                }
            }
        } elseif ($activeTab=='createproduct'){
            $this->addTab('createproduct', array(
                'label'     => Mage::helper('cpwms')->__('Create Product'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('cpwms/catalog_product_edit_tab_createproduct')->toHtml()),
                'active'    => true
            ));
            
        } elseif ($activeTab=='productcreated'){
            $this->addTab('productcreated', array(
                'label'     => Mage::helper('cpwms')->__('Product Was Created'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('cpwms/catalog_product_edit_tab_productcreated')->toHtml()),
                'active'    => true
            ));
            
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
    
    public function getUserToken()
    {
        return Mage::register('cafepress_user_token', $userToken);
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
