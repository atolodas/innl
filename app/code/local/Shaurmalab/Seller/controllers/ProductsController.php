<?php
class Shaurmalab_Seller_ProductsController extends Mage_Core_Controller_Front_Action {
	
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

	public function indexAction() {
		 if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }
		$this->loadLayout();
		$this->_initLayoutMessages('score/session');
		$this->_initLayoutMessages('core/session');
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	public function editAction() {
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }

		$this->loadLayout();
		$this->_initLayoutMessages('score/session');
		$this->_initLayoutMessages('core/session');
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	public function gridAction() { 
		if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }

	    $this->loadLayout();
        $this->renderLayout();
    }


    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Manage Products'));

        $productId  = (int) $this->getRequest()->getParam('id');
        

        if (!$productId) {

            $product    = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load(0);
            $_productDataDef = array(
            'type_id'           => 'simple',
            'attribute_set_id'  => 4, 
            'weight'            => '0', 
            'website_ids'       => array(Mage::app()->getStore(true)->getWebsite()->getId()),
            'stock_data'        => array(
                                    'is_in_stock' => 1,
                                    'qty' => 99999
                                ),
            'tax_class_id' => 0
            
            );
            $product->addData($_productDataDef);

            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }

        } else { 
            $product    = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId());
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

       

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

   /**
     * Validate product
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

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

            /* set restrictions for date ranges */
            $resource = $product->getResource();
            $resource->getAttribute('special_from_date')
                ->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')
                ->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')
                ->setMaxValue($product->getCustomDesignTo());

            $product->validate();

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

    /**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {
        $product     = $this->_initProduct();
        $productData = $this->getRequest()->getPost('product');
        if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }

        

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }


        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect']))
        // && $product->getOrigData('url_key') != $product->getData('url_key')
        {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        if(!$product->getOwner()) { $product->setOwner($this->_getSession()->getCustomer()->getId()); }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize product categories
         */
        $categoryIds = '';
        if($this->getRequest()->getPost('category_ids')) $categoryIds = implode(',',$this->getRequest()->getPost('category_ids'));

        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $product->setCategoryIds($categoryIds);
        }
        

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
                && !$product->getConfigurableReadonly()
        );

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            array('product' => $product, 'request' => $this->getRequest())
        );

        return $product;
    }

    /**
     * Filter product stock data
     *
     * @param array $stockData
     * @return null
     */
    protected function _filterStockData(&$stockData)
    {
        if (is_null($stockData)) {
            return;
        }
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }

    public function categoriesJsonAction()
    {
        $product = $this->_initProduct();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Save product action
     */
    public function saveAction()
    {
      
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id'));

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);

            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
                $redirectBack = true;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        } else {
           $this->_redirect('*/*/edit', array(
                'id'    => $productId,
            ));
        }
    }

    public function deleteProductAction() { 
        try { 
            $id      = $this->getRequest()->getParam('id');
            if($id) { 
                $product = Mage::getModel('catalog/product')->load($id);
                $name = $product->getName();
                if($product->getOwner() == $this->_getSession()->getCustomer()->getId()) {
                    if($product->delete()) { 
                        $this->_getSession()->addSuccess($this->__("Product '%s' deleted",$name));
                    } else { 
                        $this->_getSession()->addError($this->__('Something went wrong'));
                    }
                } else { 
                    $this->_getSession()->addError($this->__('Sorry, but you can not delete this product'));
                }
            }
        } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
        }
          $this->_redirect('*/*/');
    }

    public function exportAction() {
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $setId = 4; // Products Main Set
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();
            $attributes = array();
            $available = Mage::helper('seller')->getAvailableAttributes();
            /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
            foreach ($groups as $node) {
                $nodeChildren = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->setAttributeGroupFilter($node->getId())
                    ->addVisibleFilter()
                    // ->checkConfigurableOggettos()
                    ->load();

                if ($nodeChildren->getSize() > 0) {
                    foreach ($nodeChildren->getItems() as $child) {
                        /* @var $child Mage_Eav_Model_Entity_Attribute */
                        if(in_array($child->getAttributeCode(), $available)) $attributes[] = $child->getAttributeCode();
                    }
                }
            }


            $collection = Mage::getModel('catalog/product')->getCollection()
                                                         ->addAttributeToFilter('owner', $this->_getSession()->getCustomer()->getId())
                                                         ->addAttributeToSelect($attributes, 'left')
            ;

            $res = Mage::getSingleton('core/resource');
            $eav = Mage::getModel('eav/config');
            $nameattr = $eav->getAttribute('catalog_category', 'name');
            $nametable = $res->getTableName('catalog/category') . '_' . $nameattr->getBackendType();
            $nameattrid = $nameattr->getAttributeId();

            $collection
            ->joinTable('catalog/category_product',
            'product_id=entity_id', array('single_category_id' => 'category_id'),
            null, 'left')
            ->groupByAttribute('entity_id')
            ->joinTable($nametable,
            "entity_id=single_category_id", array('single_category_name' => 'value'),
            "{$nametable}.attribute_id=$nameattrid", 'left')
            ->getSelect()->columns(array('category_ids' => new Zend_Db_Expr("IFNULL(GROUP_CONCAT(`$nametable`.`value` SEPARATOR ','), '')")));


            $attributes[] = 'category_ids';
            $df = fopen(Mage::getBaseDir() . DS . 'var' .DS.'export'  . DS . 'exportProducts.csv', 'w');
            fputcsv($df, $attributes);
            foreach ($collection as $row) {
                $arr = array();
                foreach ($attributes as $a) {
                    if($row->getAttributeText($a))    $arr[] = $row->getAttributeText($a);
                    else  $arr[] = $row->getData($a);
                }
                fputcsv($df, $arr);
            }
            fclose($df);
            $contentType = 'application/octet-stream';
            $content = file_get_contents(Mage::getBaseDir() . DS . 'var' .DS.'export' . DS . 'exportProducts.csv');
            $this->getResponse()
                 ->setHttpResponseCode(200)
                 ->setHeader('Pragma', 'public', true)
                 ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Content-type', $contentType, true)
                 ->setHeader('Content-Length', strlen($content) ? strlen($content) : 0, true)
                 ->setHeader('Content-Disposition', 'attachment; filename="exportProducts.csv', true)
                 ->setHeader('Last-Modified', date('r'), true);

            $this->getResponse()->setBody($content);

        } else {
            echo "<div style='background-color: #901;margin-left: -10px;padding: 5px 10px;color: #fff;'>Something went wrong!</div>";
        }
    }

    public function getImportExampleAction() { 
        $attributes = 'name,description,short_description,sku,status,visibility,price,special_price,meta_title,meta_keyword,meta_description,media_gallery,category_ids';
                
        $data = array(
                array('Новый продукт','Если колонка SKU пустая - будет создан новый продукт','','','Включено','Каталог, поиск','111.00','100','','','','',''),
                array('Продукт 1','Полное описание продукта. Если заполненно поле SKU, при импорте мы будем искать продукт с таким же артикулом и записывать данные в него. Если продукт не найден, будет создан новый продукт.','Короткое описание продукта','product1','Включено','Каталог, поиск','999.00','888','META заголовок','META,ключи,ключевые,слова','META описание','',''),
                array('Продукт2','Описание2','Пустые поля НЕ сотрут существующие данные (например мета-данные в данном продукте)','product2','Выключено','Каталог, поиск','100.0000','','','','','',''),
                array('Продукт3','Это продукт с картинкой. При импорте картинка будет добавлена в его галерею и станет главной. Если прежде вы уже импортировали эту картинку, оставьте поле media_gallery пустым','','product3','Включено','Каталог, поиск','1000','999.9','','','',(Mage::getBaseUrl()).'media/catalog/product/7/1/71cd4a3a47b6fbb5ff31edb14758ab52.jpg',''),
                array('Продукт 4','Поле SKU может содержать только латинские символы и знаки \'-\' и \'_\'. остальные знаки будут преобразованы, если вы случайно их импортируете.','','product_sku-1','Включено','Каталог, поиск','11.00','123.00','','','','',''),
                array('Продукт5','Если в каком-то поле есть пробелы, его значение должно быть заключено в двойные кавычки. Но многие редакторы делают это автоматически','','','Включено','Каталог, поиск','111.00','12.00','','','','',''),
                array('Продукт6','Поля status и visibility можно не использовать совсем, если все ваши продукты должны быть всегда видимыми и отображаться как в каталоге, так и в поиске','','','Включено','Каталог, поиск','6','5','','','','',''),
                array('Продукт7','Чтобы новый продукт появился в каталоге, поле category_ids должно быть заполненно и в нём должны быть перечисленны названия Существующих категорий (без опечаток).','','','Включено','Каталог, поиск','100.0000','','','','','','Подарки,Распродажа, Сувениры'),
                array('Продукт8','Если вы импортируете поле category_ids для существующего продукта, его категории будут переопределены новыми (если список новых категорий не пуст)','','tovar8','Включено','Каталог, поиск','100.0000','','','','','','Подарки,Распродажа, Сувениры')
        );

         $df = fopen(Mage::getBaseDir() . DS . 'var' .DS.'export'  . DS . 'importProductsExample.csv', 'w');
            fputcsv($df, explode(',',$attributes));
            foreach ($data as $row) {
                fputcsv($df, $row);
            }
            fclose($df);
            $contentType = 'application/octet-stream';
            $content = file_get_contents(Mage::getBaseDir() . DS . 'var' .DS.'export' . DS . 'importProductsExample.csv');
            $this->getResponse()
                 ->setHttpResponseCode(200)
                 ->setHeader('Pragma', 'public', true)
                 ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Content-type', $contentType, true)
                 ->setHeader('Content-Length', strlen($content) ? strlen($content) : 0, true)
                 ->setHeader('Content-Disposition', 'attachment; filename="importProductsExample.csv', true)
                 ->setHeader('Last-Modified', date('r'), true);

            $this->getResponse()->setBody($content);
    }

    public function importAction() {

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $setId = 4;

            if (isset($_FILES['file'])) {
                 echo
                "<div style='background-color: #009094;margin-left: -10px;padding: 5px 10px;color: #fff;'> 
                Import started. </div>";
                flush();
                ob_flush();
                $file = $_FILES['file'];
                $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'attachment/csv');
                if (in_array($file['type'], $mimes)) {
                    if (isset($file['name']) && $file['name'] != '') {
                        try {
                            $content = file_get_contents($file['tmp_name']);
                            $content = preg_split('/\r\n|\r|\n/', $content);
                            $attributes = mageParseCsv($content[0]);
                            unset($content[0]);
                            foreach ($content as $k => $v) {
                                if (count(mageParseCsv($v)) != count($attributes)) {unset($content[$k]);}
                            }
                            echo
                            "<div style='background-color: #009094;margin-left: -10px;padding: 5px 10px;color: #fff;'>" .
                            count($content) . ' ' . 'Products to be imported </div><br/>';
                            flush();
                            ob_flush();

                            $existedProducts = Mage::getModel('catalog/product')->getCollection()
                            ->addAttributeToFilter('attribute_set_id', $setId)
                            ->addAttributeToFilter('owner', $this->_getSession()->getCustomer()->getId())
                            ->addAttributeToSelect('sku');

                            $existed = array();
                            foreach ($existedProducts as $object) {
                                $existed[$object->getId()] = $object->getSku();
                            }
                            foreach ($content as $line) {
                                $line = mageParseCsv($line);

                                
                                $defaultData = array(
                                    'owner' => $this->_getSession()->getCustomer()->getId(),
                                    'status' => '1',
                                    'visibility' => '4',
                                    'type_id'           => 'simple',
                                    'attribute_set_id'  => 4, 
                                    'weight'            => '0', 
                                    'website_ids'       => array(Mage::app()->getStore(true)->getWebsite()->getId()),
                                    'store_id'          => Mage::app()->getStore()->getId(),
                                    'stock_data'        => array(
                                                            'is_in_stock' => 1,
                                                            'qty' => 99999
                                                            ),
                                    'tax_class_id' => 0
                                );
                             

                               foreach ($line as $k=>$attribute) {
                                   if(!$attribute) { unset($line[$k]); } 
                                   else { 
                                        $defaultData[$attributes[$k]] = $attribute;
                                   }
                               }



                                foreach ($defaultData as $k => $v) {
                                    if (is_string($defaultData[$k])) {
                                        $defaultData[$k] = Mage::helper('core')->stripTags(preg_replace('#<script(.*?)>(.*?)</script>#is', '', html_entity_decode($defaultData[$k])));
                                    }
                                }

                                $object = Mage::getModel('catalog/product');
                                
                                if(isset($defaultData['sku']) && $defaultData['sku'] ) {
                                    $object->load($object->getIdBySku($defaultData['sku']));
                                    if($object->getId() != 0) unset($defaultData['website_ids']);
                                    unset($defaultData['stock_data']);
                                }

                                if((!isset($defaultData['name']) || !$defaultData['name']) && $object->getId()==0) { 
                                     echo
                                        "<div style='background-color: #009094;margin-left: -10px;padding: 5px 10px;color: #fff;'>
                                        Skipping product without name.</div>";
                                        continue;
                                }

                                if(isset($defaultData['status']) && $defaultData['status']) { 
                                    $statusAttribute = $object->getResource()->getAttribute('status');
                                    $options = $statusAttribute->getSource()->getAllOptions(false);
                                    foreach ($options as $option) {
                                        if($defaultData['status'] == $option['label']) $defaultData['status'] = $option['value'];
                                    }
                                    if(!is_integer($defaultData['status'])) unset($defaultData['status']);
                                }

                                if(isset($defaultData['visibility']) && $defaultData['visibility']) { 
                                    $visibilityAttribute = $object->getResource()->getAttribute('visibility');
                                    $options = $visibilityAttribute->getSource()->getAllOptions(false);
                                    foreach ($options as $option) {
                                        if($defaultData['visibility'] == $option['label']) $defaultData['visibility'] = $option['value'];
                                    }
                                    if(!is_integer($defaultData['visibility'])) unset($defaultData['visibility']);
                                
                                }

                                try {

                                    if(isset($defaultData['media_gallery']) && $defaultData['media_gallery']) { 
                                        $images = explode(',',$defaultData['media_gallery']);
                                        foreach ($images as $image) {
                                           $object = $object->addImageToGalleryByUrl($image);
                                        }
                                        unset($defaultData['media_gallery']);
                                    }

                                    if((!isset($defaultData['sku']) || !$defaultData['sku']) && isset($defaultData['name'])) { 
                                        $defaultData['sku'] = Mage::helper('score/oggetto_url')->format($defaultData['name']);
                                        if(in_array($defaultData['sku'], $existed)) $defaultData['sku'].='-'.(count(array_search($defaultData['sku'], $existed))+1);
                                    }

                                    $defaultData['url_key'] = $defaultData['sku'];
                                    $defaultData['url_path'] = $defaultData['url_key'].'.html';

                                    if(isset($defaultData['category_ids']) && $defaultData['category_ids']) { 
                                        $categories = Mage::helper('catalog/category')->getStoreCategories(true, true, true); 
                                        $cats = array();
                                        foreach ($categories as $cat) {
                                          $cats[$cat->getId()] = trim($cat->getName());
                                        }
                                        $productCategories =  explode(',',$defaultData['category_ids']);
                                        $realCategories = array();
                                        foreach ($productCategories as $categoryName) {
                                            $categoryName = trim($categoryName);
                                            if($key = array_search($categoryName, $cats)) $realCategories[] = $key; 
                                        }
                                        $defaultData['category_ids'] = $realCategories;
                                        if(empty($defaultData['category_ids'])) unset($defaultData['category_ids']);
                                    } 

                                    if (isset($defaultData['sku']) && in_array($defaultData['sku'], $existed)) {
                                        foreach ($defaultData as $k => $v) {
                                            //if(!is_array($v)) $defaultData[$k] =  $defaultData->htmlEscape($v);
                                        }

                                        $object->addData($defaultData)->save();
                                        echo
                                        "<div style='background-color: #009094;margin-left: -10px;padding: 5px 10px;color: #fff;'> 
                                        Product '" . @$defaultData['name'] . ' '. @$defaultData['sku']. "' has been updated. </div>";
                                    } else {
                                        $object->addData($defaultData)->save();
                                        echo
                                        "<div style='background-color: #009094;margin-left: -10px;padding: 5px 10px;color: #fff;'>
                                        Product '" . $object->getName() . ' '. $object->getSku()."' has been created. </div>";

                                    }
                                   
                                } catch (Exception $e) {
                                    echo
                                    "<div style='background-color: #901;margin-left: -10px;padding: 5px 10px;color: #fff;'>
                                    Product '" . @$defaultData['name'] . ' '. @$defaultData['sku']. "' has NOT been created/updated. " . $e->getMessage()
                                    . '</div>';
                                }
                                flush();
                                ob_flush();
                                sleep(1);
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                } else {
                    echo "<div style='background-color: #901;margin-left: -10px;padding: 5px 10px;color: #fff;'>You can import CSV files only</div>";
                }
            } else {
                echo "<div style='background-color: #901;margin-left: -10px;padding: 5px 10px;color: #fff;'>Please choose file to import</div>";
            }
        } else {
            echo "<div style='background-color: #901;margin-left: -10px;padding: 5px 10px;color: #fff;'>Something went wrong!</div>";
        }
    }
}