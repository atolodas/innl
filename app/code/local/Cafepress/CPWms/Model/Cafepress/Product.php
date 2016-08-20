<?php

class Cafepress_CPWms_Model_Cafepress_Product extends Cafepress_CPWms_Model_Cafepress_Abstract
{
    protected $_product = false;
    protected $_serverResultCreateProduct = false;
    const FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT = 'cafepress_create_product';

    public function createNewCafepress()
    {
        $storeId = 0;
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->loadByAttribute('name',self::FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT);
        
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                    ->setStoreId($storeId)
                    ->load($xmlformatModel->getId());
        
        if (!$xmlformatModel->getName()){
            die('No format With name "'.self::FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT.'"!');
        }
        $product= $this->_product;
        
        $xmlformatModel->addVariable('merchandise_id', $product->getCpPtn());

        $this->_getVarModel()->setVar('product',$product->getData());
        $this->_getVarModel()->setVar('store_id',Mage::getStoreConfig('cafepress_common/partner/storename'));
        
        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();
        
        $changeXml = $this->cpFormat($xmlResult, $product);
        $xmlformatModel->setServerResponse($changeXml);
        
        $result = $xmlformatModel->processResponse();
        
        $product->addData(array(
            'cp_create_product_xml' => $result
            ))->save();
        
        $this->_serverResultCreateProduct = $result;
        $_SESSION['cafepress_create_product_xml'] = serialize($result);
        return $result;
    }

    public function createRemoteProduct($product_data)
    {
        $storeId = 0;
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
            ->setStoreId($storeId)
            ->loadByAttribute('name',self::FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT);

        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
            ->setStoreId($storeId)
            ->load($xmlformatModel->getId());

        if (!$xmlformatModel->getName()){
            die('No format With name "'.self::FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT.'"!');
        }

        $xmlformatModel->addVariable('merchandise_id', $product_data['cp_ptn']);

        $this->_getVarModel()->setVar('product',$product_data);
        $this->_getVarModel()->setVar('store_id',Mage::getStoreConfig('cafepress_common/partner/storename'));

        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();

        $changeXml = $this->cpFormat($xmlResult, null, $product_data);
        $xmlformatModel->setServerResponse($changeXml);

        $xml = $xmlformatModel->processResponse();

        $encoded_product_xml = urlencode($xml);

        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
            ->setStoreId($storeId)
            ->loadByAttribute('name','cafepress_save_product');

        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
            ->setStoreId($storeId)
            ->load($xmlformatModel->getId());

        if (!$xmlformatModel->getName()){
            die('No format With name "'.'cafepress_save_product'.'"!');
        }
        $xmlformatModel->addVariables(array(
            'user_token'=> Mage::getModel('cpwms/cafepress_token')->get(),
            'encoded_product_xml'=> $encoded_product_xml,

        ));

        $xmlformatModel->processRequest();
        $result = $xmlformatModel->processResponse();

        $design_id = $product_data['design_id'];
        $image_location = $product_data['image_location'];
        $media_height = $product_data['media_height'];
        $cp_ptn = $product_data['cp_ptn'];

        $sxml = simplexml_load_string($result['all_block']);

//        Mage::log($encoded_product_xml, null, 'lomantik.log');
//        Mage::log($result['all_block'], null, 'lomantik.log');

        $image_location_value = null;
        $image_location_options = Mage::helper('cpwms')->getAttributeOptions('image_location');
        foreach($image_location_options as $key => $option){
            if($option == $image_location){
                $image_location_value = $key;
            }
        }

        $product = Mage::getModel('catalog/product');
        $product->setName((string)$sxml[0]->attributes()->name);
        $product->setWeight(0);
        $product->setDescription((string)$sxml[0]->attributes()->description);
        $product->setShortDescription((string)$sxml[0]->attributes()->shortDescription);
        $product->setAttributeSetId($this->getDefaultProductAttributeSetId());
        $product->setSku((string)$sxml[0]->attributes()->id);
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setPrice((string)$sxml[0]->attributes()->sellPrice);
        $product->setTaxClassId(0);

        if($sxml[0]->color->count() > 0 && $sxml[0]->size->count() > 0){
            $product->setTypeId('configurable');
            $att_color = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','color');
            $att_size = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size');
            $attributes_data = array(
                '0' => $att_color->getData(),
                '1' => $att_size->getData()
            );
            $product->setConfigurableAttributesData($attributes_data);
            $product->setCanSaveConfigurableAttributes(1);
            $product->setStockData(array(
                'is_in_stock' => 1
            ));
        } elseif($sxml[0]->color->count() > 0 && $sxml[0]->size->count() == 0){
            $product->setTypeId('configurable');
            $att_color = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','color');
            $attributes_data = array(
                '0' => $att_color->getData(),
            );
            $product->setConfigurableAttributesData($attributes_data);
            $product->setCanSaveConfigurableAttributes(1);
            $product->setStockData(array(
                'is_in_stock' => 1
            ));
        } elseif($sxml[0]->color->count() == 0 && $sxml[0]->size->count() > 0){
            $product->setTypeId('configurable');
            $att_size = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size');
            $attributes_data = array(
                '0' => $att_size->getData()
            );
            $product->setConfigurableAttributesData($attributes_data);
            $product->setCanSaveConfigurableAttributes(1);
            $product->setStockData(array(
                'is_in_stock' => 1
            ));
        } elseif($sxml[0]->color->count() == 0 && $sxml[0]->size->count() == 0){
            $product->setTypeId('simple');
            $product->setStockData(array(
                'qty'         => 9999,
                'is_in_stock' => 1
            ));
            $product_image = Mage::getModel('cpwms/cafepress_product')->getMaxProductImage($sxml);
            $filepath = Mage::getBaseDir('media').'/'.basename($product_image);
            copy($product_image, $filepath);
            $product->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
            unlink($filepath);
        }

        $product->setDesignId($design_id);
        $product->setImageLocation($image_location_value);
        $product->setMediaHeight($media_height);
        $product->setCpSellprice((string)$sxml[0]->attributes()->sellPrice);
        $product->setCpSaveProductId((string)$sxml[0]->attributes()->id);
        $product->setCpPtn($cp_ptn);
        $product->setCpUserToken(Mage::getModel('cpwms/cafepress_token')->get());
        $product->setCpMerchandiseContent($_SESSION['cp_type_content']);
        $product->setCpCreateProductXml($changeXml);
        $product->setCpSaveProductXml($result['all_block']);
        $product->save();

        return $product;
    }

    protected function getDefaultProductAttributeSetId(){
        if (!$this->productAttributeId){
            $this->productAttributeId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        }
        return $this->productAttributeId;
    }

    public function saveCafepress($xml)
    {
        $product = $this->_product;
        
        $product->addData(array(
                'cp_create_product_xml' => $xml,
            ))
            ->save();
        
        $encoded_product_xml = urlencode($xml);
        
        $storeId = 0;
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->loadByAttribute('name','cafepress_save_product');
        
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                    ->setStoreId($storeId)
                    ->load($xmlformatModel->getId());
        
        if (!$xmlformatModel->getName()){
            die('No format With name "'.'cafepress_save_product'.'"!');
        }
        $xmlformatModel->addVariables(array(
            'user_token'=> $product->getCpUserToken(),
            'encoded_product_xml'=> $encoded_product_xml,
            
        ));
        
        $xmlformatModel->processRequest();
        $result = $xmlformatModel->processResponse();

        if($product->getTypeId() == 'simple'){
            $sxml = simplexml_load_string($result['all_block']);
            $image_url = Mage::getModel('cpwms/cafepress_product')->getMaxProductImage($sxml);
            $filepath = Mage::getBaseDir('media').'/'.basename($image_url);
            copy($image_url, $filepath);
            $product->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
            unlink($filepath);
        }
        
        $product->addData(array(
                'cp_save_product_id' => $result['product_id'],
                'cp_save_product_xml' => $result['all_block'],
            ))
            ->save();
    }
    
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }
    
    public function saveMerchandiseData($merchandiseData)
    {
        $product = $this->_product;
        $product->addData(array(
                'cp_ptn'                    => $merchandiseData['id'],
                'cp_sellprice'              => $merchandiseData['sellPrice'],
                'cp_merchandise_content'    => $merchandiseData['all_block_content'],
            ))
            ->save();;
        
    }
    
    protected function getProduct()
    {
        $productId = $this->_product->getId();
        
        $product = Mage::getModel('catalog/product')->load($productId);
        return $product;
    }
    
    
    public function _getVarModel()
    {
        return Mage::getSingleton('cpwms/xmlformat_format_entity_variable');
    }

    protected function cpFormat($xml, $product = null, $product_data = null)
    {
        $sxml = simplexml_load_string($xml);
        foreach($sxml as $element){
            if($element->getName() == 'color'){
                unset($sxml->color);
                break;
            }
        }
        foreach($sxml as $element){
            if($element->getName() == 'size'){
                unset($sxml->size);
                break;
            }
        }
        foreach($sxml as $element){
            if($element->getName() == 'mediaConfiguration'){
                unset($sxml->mediaConfiguration);
                break;
            }
        }
        $sxml->addChild('mediaConfiguration');
        $element = $sxml->mediaConfiguration;
        if($product){
            $element->addAttribute('height', $product->getMediaHeight());
            $element->addAttribute('name', $product->getAttributeText('image_location'));
            $element->addAttribute('designId', $product->getDesignId());
        } else{
            $element->addAttribute('height', $product_data['media_height']);
            $element->addAttribute('name', $product_data['image_location']);
            $element->addAttribute('designId', $product_data['design_id']);
        }

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($sxml->asXML());
        return $dom->saveXML();
    }

    public function getLocalColorId($server_id){
        $color_attribute_id = Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('attribute_code', 'color')
            ->getFirstItem()
            ->getAttributeId();

        $result = Mage::getModel('eav/entity_attribute_option')->getCollection()
            ->addFieldToFilter('attribute_id', $color_attribute_id)
            ->addFieldToFilter('custom_id', $server_id)
            ->getFirstItem()
            ->getOptionId();
        return $result;
    }

    public function getLocalSizeId($server_id){
        $size_attribute_id = Mage::getModel('eav/entity_attribute')
            ->getCollection()
            ->addFieldToFilter('attribute_code', 'size')
            ->getFirstItem()
            ->getAttributeId();

        $result = Mage::getModel('eav/entity_attribute_option')->getCollection()
            ->addFieldToFilter('attribute_id', $size_attribute_id)
            ->addFieldToFilter('custom_id', $server_id)
            ->getFirstItem()
            ->getOptionId();
        return $result;
    }

    public function getProductImages($sxml, $colorId, $minImageSize = null){
        $product_images = array();
        $maxImageSize = 0;
        for($i = 0; $i < $sxml->productImage->count(); $i++){
            if((string)$sxml->productImage[$i]['colorId'] == $colorId && (string)$sxml->productImage[$i]['imageSize'] > $maxImageSize){
                if($minImageSize == null || (string)$sxml->productImage[$i]['imageSize'] < $minImageSize)
                    $product_images[(string)$sxml->productImage[$i]['perspectiveName']] = (string)$sxml->productImage[$i]['productUrl'];
                $maxImageSize = (string)$sxml->productImage[$i]['imageSize'];
            }
        }
        return $product_images;
    }

    public function getMaxProductImage($sxml){
        $product_image_url = false;
        $max_resolution = 0;
        for($i = 0; $i < $sxml[0]->productImage->count(); $i++){
            $product_image = $sxml[0]->productImage[$i];
            if((string)$product_image['perspectiveName'] == 'Front' && (string)$product_image['imageSize'] > $max_resolution){
                $product_image_url = (string)$product_image['productUrl'];
                $max_resolution = (string)$product_image['imageSize'];
            }
        }
        return $product_image_url;
    }

    public function attachSimpleToConfigurable($configurableProduct, $childProductIds){
        $newids = array();
        $loader = Mage::getResourceModel('catalog/product_type_configurable')->load($configurableProduct->getId());
        $ids = $configurableProduct->getTypeInstance()->getUsedProductIds();
        foreach ($ids as $id){
            $newids[$id] = 1;
        }
        foreach($childProductIds as $id){
            $newids[$id] = 1;
        }
        $loader->saveProducts($configurableProduct, array_keys($newids));
    }

    public function getOptionCustomIds($attribute_code){
        $result = array();
        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attribute_code);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $attributeOptions = $attribute->setStoreId(/*$this->getDefaultStoreId()*/0)->getSource()->getAllOptions();
        foreach($attributeOptions as $option){
            $value = Mage::getModel('eav/entity_attribute_option')->load($option['value'])->getCustomId();
            if($value){
                $result[$option['label']] = $value;
            }
        }
        return $result;
    }

    public function getChildProductCustomSkus($product){
        $result = array();
        $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
        $color_options = $this->getOptionCustomIds('color');
        $size_options = $this->getOptionCustomIds('size');
        foreach($childProducts as $childProduct){
            $sku = str_replace($product->getSku().'_', '', $childProduct->getSku());
            $attributes = explode('_', $sku);
            if(count($attributes) > 1){
                $result[] = $color_options[$attributes[0]].'_'.$size_options[$attributes[1]];
            } else{
                $result[] = $color_options[$attributes[0]];
            }
        }
        return $result;
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
