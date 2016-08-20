<?php

class Cafepress_CPCore_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        Mage::app()->setCurrentStore($storeId);
        $this->loadLayout()
                ->_setActiveMenu('catalog/cpcore')
                ->_addBreadcrumb(Mage::helper('cpcore')->__('Cafe Press Product Manager'), Mage::helper('cpcore')->__('Cafe Press Product Manager'));

        $this->_title($this->__('cpcore'))->_title($this->__('Cafe Press Product Manager'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('cpcore/catalog_product'))
            ->renderLayout();
    }
    
    public function synchAction()
    {
        //@TODO: Synchranize
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('cpcore/catalog_product'))
            ->renderLayout();    
    }
    
    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $productReg = Mage::registry('product');
        $storeId = (int) $this->getRequest()->getParam('store', false);
        
        Mage::app()->setCurrentStore($storeId);
        
        if ($productReg){
            return $productReg;
        }
        $productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', $storeId));

        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $product->isConfigurable() &&
            (!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
            $product->getTypeInstance()->setUsedProductAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        // Required attributes of simple product for configurable creation
        if ($this->getRequest()->getParam('popup')
            && $requiredAttributes = $this->getRequest()->getParam('required')) {
            $requiredAttributes = explode(",", $requiredAttributes);
            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getId(), $requiredAttributes)) {
                    $attribute->setIsRequired(1);
                }
            }
        }

        if ($this->getRequest()->getParam('popup')
            && $this->getRequest()->getParam('product')
            && !is_array($this->getRequest()->getParam('product'))
            && $this->getRequest()->getParam('id', false) === false) {

            $configProduct = Mage::getModel('catalog/product')
                ->setStoreId(0)
                ->load($this->getRequest()->getParam('product'))
                ->setTypeId($this->getRequest()->getParam('type'));

            /* @var $configProduct Mage_Catalog_Model_Product */
            $data = array();
            foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {

                /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                if(!$attribute->getIsUnique()
                    && $attribute->getFrontend()->getInputType()!='gallery'
                    && $attribute->getAttributeCode() != 'required_options'
                    && $attribute->getAttributeCode() != 'has_options'
                    && $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
                    $data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
                }
            }

            $product->addData($data)
                ->setWebsiteIds($configProduct->getWebsiteIds());
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }
    
    /**
     * Create new product page
     */
    public function newAction()
    {
        $product = $this->_initProduct();
        $this->_initAction()->renderLayout();
    }
    
    public function synchronizeAction ()
    {
        $productId = $this->getRequest()->getParam('id');
        Mage::register('current_product_id', $productId);
        
        $this->_initAction()->renderLayout();
    }

    public function createTokenAction()
    {
        $product = $this->_initProduct();
        $userToken = Mage::getSingleton('cpcore/cafepress_token')->createToken();
        
        $product->setCpUserToken($userToken)->save();
        $this->getResponse()->setRedirect($this->getUrl('*/*/synchronize', array('token'=>$userToken,'id'=>$product->getId())));
    }
    
    public function continueAction()
    {
        $userToken = $this->getRequest()->getParam('user_token');
        if (!$userToken){
            $userToken = $this->getRequest()->getParam('token');
        }
        $action = $this->getRequest()->getParam('action');
        $product = $this->_initProduct();
        $data = $this->getRequest()->getPost();
        
        $actionNext = false;
        if($action) {
            switch ($action){
                case 'uploadimage':{
                    if ($userToken){
                        $actionNext = 'selectmerchant';
                    }
                } break;
                case 'selectmerchant':{
                    $merchandiseData = Mage::getModel('cpcore/cafepress_merchandise')->getDataById($data['product_type']);
                    Mage::getModel('cpcore/cafepress_product')->setProduct($product)->saveMerchandiseData($merchandiseData);
                    $actionNext = 'createproduct';
                } break;
                case 'selectmerchantback':{
//                    $merchandiseData = Mage::getModel('cpcore/cafepress_merchandise')->getDataById($data['product_type']);
//                    Mage::getModel('cpcore/cafepress_product')->setProduct($product)->setMerchandiseData($merchandiseData);
                    $actionNext = 'createproduct';
                } break;
                case 'createproduct':{
//                    Mage::getModel('cpcore/cafepress_product')->setProduct($product)->saveCafepress($data['create_product_xml1']);
                    $xml = Mage::helper('cpcore')->getXmlByXmlData($data['create_product_xml']);
                    Mage::getModel('cpcore/cafepress_product')->setProduct($product)->saveCafepress($xml);
                    $actionNext = 'productcreated';
                } break;
            }
        } else{
            if(!isset($data['leaveOld'])){
                $tmp_file_path = Mage::getBaseDir('media').'/'.$_FILES['newCpFile']['name'];
                move_uploaded_file($_FILES['newCpFile']['tmp_name'], $tmp_file_path);

                $attributes = $product->getTypeInstance()->getSetAttributes();
                $gallery = $attributes['media_gallery'];
                $galleryData = $product->getMediaGallery();
                foreach($galleryData['images'] as $image){
                    if($image['file'] == $product->getCpImage()){
                        $gallery->getBackend()->removeImage($product, $image['file']);
                    }
                }

                $product->addImageToMediaGallery($tmp_file_path, 'cp_image', false, false);
                $design_id = Mage::getModel('cpcore/cafepress_token')->uploadImage($tmp_file_path);
                $product->setCpDesignId($design_id);
                $product->setCpUserToken(Mage::getModel('cpcore/cafepress_token')->get());
                $product->save();
                unlink($tmp_file_path);
            }
            $actionNext = 'selectmerchant';
        }
        
        if (!$actionNext){
            if (!$userToken){
                $actionNext = 'token';
            } elseif (!$product->getCpDesignId()){
                $actionNext = 'uploadimage';
            } elseif (!$product->getCpPtn()){
                $actionNext = 'selectmerchant';
            } else {
                $actionNext = 'selectmerchant';
//                $actionNext = 'token';
            }
        }
        
        switch ($actionNext){
            case 'createproduct':{
                $merchandiseData = Mage::getModel('cpcore/cafepress_product')->setProduct($product)->createNewCafepress();
            }break;
        }
        
        Mage::register('cpcore_activ_tab',$actionNext);
        
        $this->getResponse()->setRedirect($this->getUrl('*/*/synchronize', array(
            'id'        => $product->getId(),
            'token'     => $userToken,
            'action'    => $actionNext
            )));
    }
    
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $this->getResponse()->setBody($response->toJson());
        return;

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            /* @var $product Mage_Catalog_Model_Product */
            $product = Mage::getModel('catalog/product');
            $product->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $product->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            if ($productId = $this->getRequest()->getParam('id')) {
                $product->load($productId);
            }

            $dateFields = array();
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $productData = $this->_filterDates($productData, $dateFields);

            $product->addData($productData);
            $product->validate();
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $product->validate())) {
//                foreach ($errors as $code => $error) {
//                    if ($error === true) {
//                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is invalid.', $product->getResource()->getAttribute($code)->getFrontend()->getLabel()));
//                    }
//                    else {
//                        Mage::throwException($error);
//                    }
//                }
//            }
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
    
    public function gridAction()
    {
//        $this->loadLayout();
//        $this->renderLayout();
        $this->getResponse()->setBody(
        $this->getLayout()->createBlock('cpcore/catalog_product_grid')->toHtml());
    }

    public function createAssociatedAction(){
        $configurable = Mage::getModel('catalog/product')->load($this->getRequest()->getPost('configurable'));
        $weight = $this->getRequest()->getPost('weight');
        $simples = json_decode($this->getRequest()->getPost('simples'));
        $sxml = simplexml_load_string($configurable->getCpSaveProductXml());
        $productIds = array();

        foreach($simples as $simple_params){
            $params = explode('_', $simple_params);
            $color_id = Mage::getModel('cpcore/cafepress_product')->getLocalColorId($params[0]);
            if(count($params) > 1){
                $size_id = Mage::getModel('cpcore/cafepress_product')->getLocalSizeId($params[1]);
            } else{
                $size_id = false;
            }
            $color_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color')->setStoreId(/*$this->getDefaultStoreId()*/0);
            $size_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'size')->setStoreId(/*$this->getDefaultStoreId()*/0);
            if($size_id){
                $new_sku = $configurable->getSku().'_'.$color_attribute->getSource()->getOptionText($color_id).'_'.$size_attribute->getSource()->getOptionText($size_id);
            } else{
                $new_sku = $configurable->getSku().'_'.$color_attribute->getSource()->getOptionText($color_id);
            }
            $product_images = Mage::getModel('cpcore/cafepress_product')->getProductImages($sxml, $params[0]);
            $filepath = Mage::getBaseDir('media').'/'.basename($product_images['Front']);
            copy($product_images['Front'], $filepath);

            $product = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('sku', $new_sku)->getFirstItem();
            if(!$product){
                $product = Mage::getModel('catalog/product');
            }
            $product->setSku($new_sku);
            $product->setName($configurable->getName());
            $product->setDescription($configurable->getDescription());
            $product->setShortDescription($configurable->getShortDescription());
            $product->setPrice($configurable->getPrice());
            $product->setTypeId('simple');
            $product->setAttributeSetId($configurable->getAttributeSetId());
            $product->setCategoryIds($configurable->getCategoryIds());
            $product->setWeight($weight);
            $product->setTaxClassId($configurable->getTaxClassId());
            $product->setStatus($configurable->getStatus());
            $product->setWebsiteIds($configurable->getWebsiteIds());
            $product->setVisibility(1);
            $product->setStockData(array(
                'manage_stock' => 0,
                'use_config_manage_stock' => 0,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1,
                'use_config_enable_qty_increments' => 1
            ));
            $product->setColor($color_id);
            if($size_id){
                $product->setSize($size_id);
            }
            $product->setIsImported($configurable->getIsImported());
            $product->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
            unlink($filepath);
            $product->save();
            $productIds[] = $product->getId();
        }
        Mage::getModel('cpcore/cafepress_product')->attachSimpleToConfigurable($configurable, $productIds);
    }

    protected function getDefaultStoreId(){
        $result = false;
        $websites = Mage::app()->getWebsites();
        foreach($websites as $website){
            if($website->getIsDefault()){
                $stores = Mage::app()->getStores();
                foreach($stores as $store){
                    if($store['website_id'] == $website['website_id']){
                        $result = $store['store_id'];
                    }
                }
            }
        }
        return $result;
    }
}
