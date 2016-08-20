<?php

class Cafepress_CPCore_Model_Cafepress_Product extends Cafepress_CPCore_Model_Cafepress
{
    protected $_product = false;
    protected $_serverResultCreateProduct = false;
    protected $_serverResultRequestCreateProduct = false;
    
    const FORMAT_NAME_CAFEPRESS_CREATE_PRODUCTS  = 'cafepress_create_product';
    const FORMAT_NAME_CAFEPRESS_SAVE_PRODUCT    = 'cafepress_save_product';

    public function createNewCafepress($saveDataToProduct=true)
    {
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::FORMAT_NAME_CAFEPRESS_CREATE_PRODUCTS);
        
        $product= $this->_product;
        $productData = $product->getData();
        $xmlformatModel->addVariable('merchandise_id', $product->getCpPtn());

        $this->_getVarModel()->setVar('product',$product->getData());
        $this->_getVarModel()->setVar('store_id',Mage::getStoreConfig('cafepress_common/partner/storename'));
        
        if (isset($productData['cp_section_id'])){
            $this->_getVarModel()->setVar('section_id',$productData['cp_section_id']);
        }
        
        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();
        
        $changeXml = $this->cpFormat($xmlResult, $product);
        
        $xmlformatModel->setServerResponse($changeXml);
        $result = $xmlformatModel->processResponse();
        
        Mage::log('XML create_product:', null, 'debug_createproduct_cp.log');
        Mage::log($result, null, 'debug_createproduct_cp.log');
        
        if ($saveDataToProduct){
            $product->addData(array(
                            'cp_create_product_xml' => $result
                        ))
                    ->save();
        }
        
        $this->_serverResultCreateProduct = $result;
        $_SESSION['cafepress_create_product_xml'] = serialize($result);
        return $result;
    }

    public function saveCafepress($xml, $saveDataToProduct=true)
    {
        if ($saveDataToProduct){
            $product = $this->_product;
            $product->addData(array(
                    'cp_create_product_xml' => $xml,
                ))
                ->save();
        }
        
//        Mage::log($xml, null, 'debug.log');
        /*
        $xml = '<?xml version="1.0"?>
<product id="0" sellPrice="22.5000" description="The white t-shirt is a timeless classic that offers a clean, simple, and durable look. This high quality white t-shirt is comfortable and preshrunk so that it keeps its shape wash after wash. With a wide range of size choices from Small to 4XL, men and women of all shapes and sizes will find their perfect fit. This white t-shirt is not only fashionable with jeans, but also is perfect for lounging around in your sweats or pajamas.&lt;ul&gt;&lt;li&gt;100% preshrunk cotton&lt;/li&gt;&lt;li&gt;6.1 oz&lt;/li&gt;&lt;/ul&gt;" storeId="" sectionId="0" defaultOrientation="Normal" defaultPerspective="Front" sortPriority="0" name="White T-Shirt" merchandiseId="674">
  <mediaConfiguration height="10" name="FrontCenter" cpDesignId="69537825" designId="69537825"/>
  <mediaConfiguration height="10" name="BackCenter" cpDesignId="69537825" designId="69537825"/>
</product>';
         * 
         */
        
        /*
        $xml = '<?xml version="1.0"?>
<product id="0" sellPrice="231.0000" description="For stylish weekend comfort anytime, guys will want to live in our Fitted T by American Apparel. Made of ultra-fine, combed ring-spun cotton, that gets softer with each washing. Lightweight for summer comfort or winter layering. Grab attention with this vintage fit that loves to hug skin. (Size up for a looser fit).&#13;&#10;&lt;ul&gt;&lt;li&gt;4.3 oz. 100% ultra-fine combed organic ring-spun jersey&lt;/li&gt;&#13;&#10;&lt;li&gt;Vintage fit (size up for a looser fit)&lt;/li&gt;&#13;&#10;&lt;li&gt;Made in the USA, by American Apparel&lt;/li&gt;&lt;/ul&gt;" storeId="tiffestival" sectionId="0" defaultOrientation="Normal" defaultPerspective="Front" sortPriority="0" name="Mens Custom Fitted T-Shirt" merchandiseId="159">
  <mediaConfiguration height="10" name="FrontCenter" cpDesignId="69537825" designId="69537825"/>
</product>';
         * 
         */
        
//        Mage::log('--->', null, 'debug_save.log');
//        Mage::log($xml, null, 'debug_save.log');
        
        $encoded_product_xml = urlencode($xml);
        
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::FORMAT_NAME_CAFEPRESS_SAVE_PRODUCT);
        
        $xmlformatModel->addVariables(array(
            'user_token'=> Mage::getModel('cpcore/cafepress_token')->get(),
            'encoded_product_xml'=> $encoded_product_xml,
        ));
        
        $xmlformatModel->processRequest();
        $result = $xmlformatModel->processResponse();
        
//        Mage::log('=====', null, 'debug_save.log');
//        Mage::log($result, null, 'debug_save.log');
        
        if($saveDataToProduct){
            $product->addData(array(
                    'cp_save_product_id' => $result['product_id'],
                    'cp_save_product_xml' => $result['all_block'],
                ))
                ->save();
        }
        return $result;
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
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
    }

    protected function cpFormat($xml, $product = null, $product_data = null)
    {
        $sxml = simplexml_load_string($xml);
        $forRemove = array();
        foreach($sxml as $key=> $element){
            $forRemove[] = $element->getName();
        }
        foreach(array_unique($forRemove) as $name){
            unset($sxml->$name);
        }
        $element = $sxml->addChild('mediaConfiguration');
        if($product){
            $product_data = $product->getData();
            $product_data['cp_image_location'] = $product->getAttributeText('cp_image_location');
        }
        $element->addAttribute('height', $product_data['cp_media_height']);
        $element->addAttribute('name', $product_data['cp_image_location']);
//        $element->addAttribute('cpDesignId', $product_data['cp_design_id']);
        $element->addAttribute('designId', $product_data['cp_design_id']);
        
        if (($product->getCpStaticDesignId()) || ($product_data['cp_static_design_id'])){
            $element = $sxml->addChild('mediaConfiguration');
            if($product){
                $product_data = $product->getData();
                $product_data['cp_static_image_location'] = $product->getAttributeText('cp_static_image_location');
            } 
            $element->addAttribute('height', $product_data['cp_static_media_height']);
            $element->addAttribute('name', $product_data['cp_static_image_location']);
//            $element->addAttribute('cpDesignId', $product_data['cp_static_design_id']);
            $element->addAttribute('designId', $product_data['cp_static_design_id']);
//            $element->addAttribute('alignment', 'MiddleCenter');
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
            if((string)$sxml->productImage[$i]['colorId'] == $colorId &&
                (string)$sxml->productImage[$i]['imageSize'] > $maxImageSize){
                if($minImageSize == null || (string)$sxml->productImage[$i]['imageSize'] < $minImageSize)
                $product_images[(string)$sxml->productImage[$i]['perspectiveName']] = (string)$sxml->productImage[$i]['productUrl'];
                $maxImageSize = (string)$sxml->productImage[$i]['imageSize'];
            }
        }
        return $product_images;
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

    public function attachToConfigurable($configurable, $simples, $attribute_codes){
        foreach($simples as $product){
            $absorbed_products[$product->getId()] = array();
            foreach($attribute_codes as $attribute_code){
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attribute_code);
                $absorbed_products[$product->getId()][] = array(
                    'attribute_id' => $attribute->getId(),
                    'label' => $product->getData($attribute_code),
                    'is_percent' => false,
                    'pricing_value' => '3'/*sprintf("%0.2f", $product->getPrice() - $product->getCpSellprice())*/
                );
            }
        }
        $configurable->setConfigurableProductsData($absorbed_products);
//        $configurable->save();
    }

    public function getOptionCustomIds($attribute_code){
        $result = array();
        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attribute_code);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        if($attribute){
            $attributeOptions = $attribute->setStoreId(/*$this->getDefaultStoreId()*/0)->getSource()->getAllOptions();
            foreach($attributeOptions as $option){
                $value = Mage::getModel('eav/entity_attribute_option')->load($option['value'])->getCustomId();
                if($value){
                    $result[$option['label']] = $value;
                }
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
            $result[] = $color_options[$attributes[0]].'_'.$size_options[$attributes[1]];
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
    
    public function createRemoteProduct($product_data)
    {
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::FORMAT_NAME_CAFEPRESS_CREATE_PRODUCTS);

//        Mage::log($xmlformatModel->getStoreId(), null, 'debug.log');
//        Mage::log(Mage::app()->getStore()->getId(), null, 'debug_storis.log');
//        Mage::log(Mage::registry('cpcore_store_id'), null, 'debug_storis.log');
        
        $xmlformatModel->addVariable('merchandise_id', $product_data['cp_ptn']);

        $this->_getVarModel()->setVar('product',$product_data);
        $this->_getVarModel()->setVar('store_id',Mage::getStoreConfig('cafepress_common/partner/storename'));
        
        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();
        
        $changeXml = $this->cpFormat($xmlResult, null, $product_data);
        
        $xmlformatModel->setServerResponse($changeXml);

        $xml = $xmlformatModel->processResponse();
        
        $result = $this->saveCafepress($xml,false);

        $design_id = $product_data['cp_design_id'];
        $image_location = $product_data['cp_image_location'];
        $media_height = $product_data['cp_media_height'];
        $cp_ptn = $product_data['cp_ptn'];

        $sxml = simplexml_load_string($result['all_block']);

        $image_location_options = Mage::helper('cpcore')->getAttributeOptions('cp_image_location');
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
            $product_image = $this->getMaxProductImage($sxml);
            $filepath = Mage::getBaseDir('media').'/'.basename($product_image);
            copy($product_image, $filepath);
            $product->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
            unlink($filepath);
        }

        $product->setCpDesignId($design_id);
        $product->setCpImageLocation($image_location_value);
        $product->setCpMediaHeight($media_height);
        $product->setCpSellprice((string)$sxml[0]->attributes()->sellPrice);
        $product->setCpSaveProductId((string)$sxml[0]->attributes()->id);
        $product->setCpPtn($cp_ptn);
        $product->setCpUserToken(Mage::getModel('cpcore/cafepress_token')->get());
        $product->setCpMerchandiseContent($_SESSION['cp_type_content']);
        $product->setCpCreateProductXml($changeXml);
        $product->setCpSaveProductXml($result['all_block']);
        $product->save();

        return $product;
    }

    public function copyRemoteProduct($products_data){
//        Mage::log($products_data, null, 'lomantik.log');
//        Mage::log($_SESSION['cp_store_products'], null, 'lomantik.log');

        $log = '';
        $log_index = 1;
        foreach($products_data['products'] as $product_sku => $product_select_data){
            if(isset($product_select_data['enabled'])){
                $product_data = $_SESSION['cp_store_products'][$product_sku];

                $perspectives = array();
                foreach($product_data['perspectives'] as $perspective){
                    $perspectives[$perspective['name']] = $perspective['pixelHeight'];
                }
                $product_images = array();
                foreach($product_data['product_images'] as $image){
                    if(array_key_exists($image['perspectiveName'], $perspectives) && $image['imageSize'] == $perspectives[$image['perspectiveName']]){
                        if(!isset($product_images[$image['colorId']])){
                            $product_images[$image['colorId']] = array();
                        }
                        $product_images[$image['colorId']][$image['perspectiveName']] = $image['productUrl'];
                    }
                }

                $color_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'color')->setStoreId(0);
                $size_attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'size')->setStoreId(0);
                $color_exists = false;
                $size_exists = false;

                $prices = array();
                if(isset($product_select_data['color']) && $product_select_data['color'] != ''){
                    $colors = array($product_select_data['color']);
                    $color_exists = true;
                } else{
                    $colors = array();
                    if(count($product_data['colors']) > 0 && isset($products_data['use_color'])){
                        foreach($product_data['colors'] as $internal_color){
                            if($internal_color['allowed'] == 'true'){
                                $colors[] = $internal_color['id'];
                            }
                        }
                        $color_exists = true;
                    } else{
                        $colors[] = null;
                    }
                }
                if(isset($product_select_data['size']) && $product_select_data['size'] != ''){
                    $sizes = array($product_select_data['size']);
                    $size_exists = true;
                } else{
                    $sizes = array();
                    if(count($product_data['sizes']) > 0  && isset($products_data['use_size'])){
                        foreach($product_data['sizes'] as $internal_size){
                            $sizes[] = $internal_size['id'];
                            $prices[$internal_size['id']] = $internal_size['priceDifference'];
                        }
                        $size_exists = true;
                    } else{
                        $sizes[] = null;
                    }
                }

//                $simple_ids = array();
                $simples = array();

                foreach($colors as $color){
                    foreach($sizes as $size){
                        if($color === null){
                            unset($color);
                        }
                        if($size === null){
                            unset($size);
                        }
                        $new_sku = $product_sku;
                        $color_id = false;
                        if(isset($color)){
                            $color_id = Mage::getModel('cpcore/cafepress_product')->getLocalColorId($color);
                            $new_sku .= '_'.$color_attribute->getSource()->getOptionText($color_id);
                        }
                        $size_id = false;
                        if(isset($size)){
                            $size_id = Mage::getModel('cpcore/cafepress_product')->getLocalSizeId($size);
                            $new_sku .= '_'.$size_attribute->getSource()->getOptionText($size_id);
                        }
                        $product = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('sku', $new_sku)->getFirstItem();
                        if(count($product->getData()) == 0){
                            $product = Mage::getModel('catalog/product');
                        } else{
                            $product = Mage::getModel('catalog/product')->load($product->getId());
                        }
                        $product->setSku($new_sku);
                        $product->setName($product_data['name']);
                        $product->setDescription($product_data['description']);
                        $product->setShortDescription($product_data['shortDescription']);
                        $product->setPrice($product_data['sellPrice']);
                        if(isset($size) && isset($prices[$size])){
                            $product->setPrice($product->getPrice() + $prices[$size]);
                        }
                        $product->setTypeId('simple');
                        $product->setAttributeSetId($products_data['attribute_set']);
                        if(isset($product_data['category'])){
                            $product->setCategoryIds(array($product_data['category']));
                        } else{
                            $product->setCategoryIds(array($this->createCategory($product_data['categoryCaption'])));
                        }
                        $product->setWeight(0);
                        $product->setTaxClassId(0);
                        $product->setStatus(1);
                        $product->setWebsiteIds(array($products_data['website']));
                        if(isset($products_data['simple_to_configurable'])){
                            $product->setVisibility(1);
                        } else{
                            $product->setVisibility(4);
                        }
                        $product->setStockData(array(
                            'manage_stock' => 0,
                            'use_config_manage_stock' => 0,
                            'use_config_min_sale_qty' => 1,
                            'use_config_max_sale_qty' => 1,
                            'use_config_enable_qty_increments' => 1
                        ));
                        if($color_id){
                            $product->setColor($color_id);
                        }
                        if($size_id){
                            $product->setSize($size_id);
                        }
                        $product->setCpDesignId($product_data['media_configuration'][0]['designId']);
                        $product->setCpMediaHeight($product_data['media_configuration'][0]['height']);
                        $product->setCpSellprice($product_data['sellPrice']);
                        $product->setCpSaveProductId($product_sku);
                        $product->setCpPtn($product_data['merchandiseId']);
                        $product->setCpImageLocation($this->getLocationId($product_data['media_configuration'][0]['name']));
                        $product->setIsImported();

                        if(isset($color)){
                            $main_image = true;
                            foreach($product_images[$color] as $product_image){
                                $filepath = Mage::getBaseDir('media').'/'.basename($product_image);
                                if(copy($product_image, $filepath)){
                                    if($main_image){
                                        $product->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
                                        $main_image = false;
                                    } else{
                                        $product->addImageToMediaGallery($filepath, array(), false, false);
                                    }
                                    unlink($filepath);
                                }
                            }
                        }

                        $product->save();
//                        $simple_ids[] = $product->getId();
                        $simples[] = $product;
                        $log .= $log_index.'. Sipmle product #'.$product->getSku().' created.'."\r\n";
                        $log_index++;
                    }
                }

                if(isset($products_data['simple_to_configurable'])){
                    $configurable = Mage::getModel('catalog/product');
                    $configurable->setName($product_data['name']);
                    $configurable->setWeight(0);
                    $configurable->setDescription($product_data['description']);
                    $configurable->setShortDescription($product_data['shortDescription']);
                    $configurable->setAttributeSetId($products_data['attribute_set']);
                    $configurable->setSku($product_sku);
                    $configurable->setStatus(1);
                    $configurable->setVisibility(4);
                    $configurable->setPrice($product_data['sellPrice']);
                    $configurable->setTaxClassId(0);
                    $configurable->setWebsiteIds(array($products_data['website']));
                    if(isset($product_data['category'])){
                        $configurable->setCategoryIds(array($product_data['category']));
                    } else{
                        $configurable->setCategoryIds(array($this->createCategory($product_data['categoryCaption'])));
                    }
                    $configurable->setTypeId('configurable');
                    $att_color = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','color');
                    $att_size = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','size');
                    $attribute_codes = array();
                    $attributes_data = array();
                    if($color_exists && $size_exists){
                        $attributes_data = array(
                            '0' => $att_color->getData(),
                            '1' => $att_size->getData()
                        );
                        $attribute_codes[] = 'color';
                        $attribute_codes[] = 'size';
                    } elseif($color_exists && !$size_exists){
                        $attributes_data = array(
                            '0' => $att_color->getData()
                        );
                        $attribute_codes[] = 'color';
                    } elseif(!$color_exists && $size_exists){
                        $attributes_data = array(
                            '0' => $att_size->getData()
                        );
                        $attribute_codes[] = 'size';
                    }
                    $configurable->setConfigurableAttributesData($attributes_data);
                    $configurable->setCanSaveConfigurableAttributes(1);
                    $configurable->setStockData(array(
                        'manage_stock' => 1,
                        'use_config_manage_stock' => 0,
                        'use_config_enable_qty_increments' => 1,
                        'is_in_stock' => 1
                    ));

                    $configurable->setCpDesignId($product_data['media_configuration'][0]['designId']);
                    $configurable->setCpImageLocation($this->getLocationId($product_data['media_configuration'][0]['name']));
                    $configurable->setCpMediaHeight($product_data['media_configuration'][0]['height']);
                    $configurable->setCpSellprice($product_data['sellPrice']);
                    $configurable->setCpSaveProductId($product_sku);
                    $configurable->setCpPtn($product_data['merchandiseId']);
                    $this->attachToConfigurable($configurable, $simples, $attribute_codes);
                    $configurable->save();
                    $log .= $log_index.'. Configurable product #'.$configurable->getSku().' created.'."\r\n";
                }
            }
        }

        return $log;
    }
    
    protected function getDefaultProductAttributeSetId(){
        if (!$this->productAttributeId){
            $this->productAttributeId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        }
        return $this->productAttributeId;
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

    public function getStoreProducts($page_number = null){
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi('get_store_products');
//        $xmlformatModel = Mage::getModel('cpcore/xmlformat_format_transformer')->getModelformatByName('get_store_products');
        if(!$page_number){
            $xmlformatModel->addVar('page_number', 0);
        } else{
            $xmlformatModel->addVar('page_number', $page_number);
        }
        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();
        $xmlformatModel->setServerResponse($xmlResult);
        $xmlResult = $xmlformatModel->processResponse();
//        $xmlformatModel->processRequest();
//        $xmlResult = $xmlformatModel->processResponse();
        return $xmlResult;
    }

    public function getLocationId($name){
        $result = false;
        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','cp_image_location');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        $attributeOptions = $attribute ->getSource()->getAllOptions();
        foreach($attributeOptions as $option){
            if($option['label'] == $name){
                $result = $option['value'];
            }
        }
        return $result;
    }

    public function getProductAttributeSets(){
        $result = array();
        $entityTypeId = Mage::getModel('eav/config')->getEntityType('catalog_product')->getEntityTypeId();
        $attribute_sets = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('entity_type_id', $entityTypeId);
        foreach($attribute_sets as $key => $attribute_set){
            $result[$key] = $attribute_set['attribute_set_name'];
        }
        return $result;
    }

    public function getWebsites(){
        $result = array();
        $websites = Mage::app()->getWebsites();
        foreach($websites as $website){
            $result[$website->getWebsiteId()] = $website->getName();
        }
        return $result;
    }

    public function createCategory($name){
        $parentCategory = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('level', '1')->getFirstItem();
        $category = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('name', $name)->getFirstItem();
        if(count($category->getData()) == 0){
            try{
                $category = Mage::getModel('catalog/category');
                $category->setName($name);
                $category->setIsActive(1);
                $category->setDisplayMode('PRODUCTS');
                $category->setIsAnchor(0);
                $category->setPath($parentCategory->getPath());
                $category->setThumbnailImageUrl($parentCategory->getThumbnailImageUrl());
                $category->save();
            } catch (Exception $e){
            }
        }
        return $category->getId();
    }

    public function getStoreProductsCount(){
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi('get_store_products_count');
//        $xmlformatModel = Mage::getModel('cpcore/xmlformat_format_transformer')->getModelformatByName('get_store_products_count');
        $xmlformatModel->processRequest();
        $xmlResult = $xmlformatModel->getServerResponse();
//        $xmlformatModel->setServerResponse($xmlResult);
//        $xmlResult = $xmlformatModel->processResponse();
//        $xmlformatModel->processRequest();
//        $xmlResult = $xmlformatModel->processResponse();
        $sxml = simplexml_load_string($xmlResult);
        return (string)$sxml;
    }
}
