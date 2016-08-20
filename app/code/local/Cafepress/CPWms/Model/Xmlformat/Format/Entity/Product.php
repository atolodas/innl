<?php
 
class Cafepress_CPWms_Model_Xmlformat_Format_Entity_Product extends Cafepress_CPWms_Model_Abstract
{
    private $_product = false;
    private $_productData = array();
    private $_productAttribute = array();
    private $_productImages = array();
    private $_productsIdUpdate = array();
    private $_onlyUpdate = false;
    
    private $_productDataDef = array(
            'sku'               => false,
            'status'            => '1', //available
            'type_id'           => 'simple',//'configurable',//'simple'//false, 
            'attribute_set_id'  => false, 
            'weight'            => '0', 
            'website_ids'       => false, 
            'stock_data'        => array(
                                    'is_in_stock' => 1,
                                    'qty' => 99999
                                ),
//            'cart_method'       => 11
        );
        
   private $_productAttributeDef = array(
            'entity_type'   => 'catalog_product',
            'image'         => false,
            'parent'        => array(),
            'qty'           => 0,
        );
    
    private $_groupDefault =    array(
                                    'name'          => 'Kit',
                                    'type'          => '',
                                    'description'   => '',
                                    'image'         => '',
                                    'parent'        => '',
                                    'id'            => false,
                                    'root_category_id' => '2',
                                );
    private $_groups = array();
    private $_groupCounter = 0;
    
    private $createCategory = false;
    private $_siteId = false;
    private $_layredAttributes = array();

    public function resetProduct()
    {
        $this->_product = false;
        $this->_productData = $this->_productDataDef;
        
        $this->_productAttribute =  $this->_productAttributeDef;
        $this->_groups = array();
        $this->_groupCounter = 0;
        $this->_layredAttributes = array();
        
        
        return $this;
    }

    public function setProductBySku($sku)
    {
        $this->_product = Mage::getModel('catalog/product')->load(Mage::getModel('catalog/product')->getIdBySku($sku));
        if (!$this->_product->getId()){
            $this->_product = Mage::getModel('catalog/product');
        }
        $this->setPData('sku',$sku);
        return $this;
    }
    
    public function getProduct()
    {
        return $this->_product;
    }


    public function getProductBySku($sku)
    {
        $this->_product = Mage::getModel('catalog/product')->load($this->_getProductIdBySky($sku));
        return $this->_product;
    }
    
    public function _getProductIdBySky($sku)
    {
        return Mage::getModel('catalog/product')->getIdBySku($sku);
    }
    
    public function _getProduct()
    {
        return $this->_product;
    }


    public function setSiteId($id)
    {
        $this->_siteId = $id; 
        return $this;
    }
    
    public function getSiteId()
    {
//        $this->_siteId = 'elayda'; //@todo!!!
        return $this->_siteId;
    }

    public function setQty($qty)
    {
        $this->_productData['stock_data']['qty'] = $qty;
        $this->_productAttributeDef['qty'] = $qty;
        return $this;
    }
    
    public function setAvailable($qty)
    {
        $this->_productData['status'] = ($qty=='YES')?1:2;
        return $this;
    }

    public function setPData($name,$value)
    {
        $parts = explode('.', trim($name), 2);

        if (2 === count($parts)) {
            list($arrName, $arrName2) = $parts;
            $this->_productData[$arrName][$arrName2] = trim((string)$value);
        } else {
            $this->_productData[trim((string)$name)] = trim((string)$value);
        }
//        $this->_productData[trim((string)$name)] = trim($value);
        return $this;
    }
    
    public function getPData($attributeName = false)
    {
        if (!$attributeName){
            return $this->_productData;
        }
        $parts = explode('.', trim($attributeName), 2);

        if (2 === count($parts)) {
            list($arrName, $arrName2) = $parts;
            if (isset($this->_productData[$arrName][$arrName2])){
                return $this->_productData[$arrName][$arrName2];
            }
        } else {
            if (isset($this->_productData[$attributeName])){
                return $this->_productData[$attributeName];
            }
        }
        return NULL;
    }
    
    public function delPData($attributeName)
    {
        if (isset($this->_productData[$attributeName])){
            unset ($this->_productData[$attributeName]);
        }
    }




    public function setPProperty($name,$value)
    {
        $parts = explode('.', trim($name), 2);

        if (2 === count($parts)) {
            list($arrName, $arrName2) = $parts;
            $this->_productAttribute[$arrName][$arrName2] = trim($value);
        } else {
            $this->_productAttribute[trim((string)$name)] = trim($value);
        }
                
        return $this;
    }
    
    public function getPProperty($attributeName = false)
    {
        if (!$attributeName){
            return $this->_productAttribute;
        }
        
        $parts = explode('.', trim($attributeName), 2);

        if (2 === count($parts)) {
            list($arrName, $arrName2) = $parts;
            if (isset($this->_productAttribute[$arrName][$arrName2])){
                return $this->_productAttribute[$arrName][$arrName2];
            }
        } else {
            if (isset($this->_productAttribute[$attributeName])){
                return $this->_productAttribute[$attributeName];
            }
        }
        return NULL;
    }
    
    public function addImage($imageURL){
        $this->_productImages[] = trim($imageURL);
    }


    public function newGroup()
    {
        $this->_groups[$this->_groupCounter] = $this->_groupDefault;
        $this->_groupCounter++;
    }
    
    public function setGroupData($name,$value)
    {
        $this->_groups[count($this->_groups)-1][trim((string)$name)] = trim((string)$value);
    }

    public function getGroups()
    {
        return $this->_groups;
    }
    
    public function addToCategories($groupIds)
    {
        $parts = explode(',', trim($groupIds));
        
        foreach ($parts as $id){
            $this->newGroup();
            $this->setGroupData('id',$id);
        }
    }
    
    protected function reorganizeProductData()
    {
//                $product->setMinSaleQty(0);
//                $product->setMaxSaleQty(0);
//                $product->setStatus($data['status']);
//                $product->setCost($data['cost']);
//                $product->setStoreId($id);
//                $product->setVendorSku(@$data['vendor_sku']);
        $dataIn = $this->getPData();
        $atttributeIn = $this->getPProperty();
        foreach($dataIn as $key=>$val){
            switch ($key){
                case 'website_ids':{
                    if (!$val){
                        $this->setPData('website_ids',array(Mage::app()->getWebsite()->getId()));
                    }
                } break;
                case 'attribute_set_id':{
                    if (!$val){
                        $this->setPData('attribute_set_id',Mage::getModel('catalog/product')->getDefaultAttributeSetId());
                    }
                } break;
                case 'type_id':{
                    if ($val){
                        $this->setPData('type_id',strtolower($val));
                    }
                } break;
                case 'stock_data':{
                    
                    if ($val['qty']>0){
                        $this->setPData('stock_data.is_in_stock',1);
                    } else {
                        $this->setPData('stock_data.is_in_stock',0);
                    }
                } break;
                case 'special_price':{
                    
                    if ($val=='null'){
                        $this->delPData('special_price');
                    }
                } break;
            }
        }
        
//        Mage::log('*!!!!*',null,'debug.log');
//        Mage::log($this->getPData(),null,'debug.log');
        
    }

    public function saveProduct() 
    {
        $storeId = $this->getStoreId();
        $this->reorganizeProductData();
        try {
            $storeIds = array();
            foreach(Mage::app()->getStores() as $store) {
                if (Mage::getStoreConfig('common/partner/id', $store->getId()) == $this->getSiteId()){
                    $storeIds[] = $store->getId();
                }
            }

            $productId = $this->_getProductIdBySky($this->getPData('sku'));
            if (!$this->_getProduct()){
                $product =  Mage::getModel('catalog/product')->load($productId);
            } else {
                $product = $this->_getProduct();
            }
            
            if ($this->_onlyUpdate && !$productId){
                return;
            }
            
            $productData = $this->getPData();
            
            $layered = array();
            foreach($productData as $key=>$val) {
                unset($productData[$key]);
                $key = trim($key);
                if(!is_array($val)) {
                $val = trim($val);
                }
                if (in_array($key, $this->_layredAttributes)){
                    $layered[$key] = $val;
                }
                $optionId = '';
                $optionId = $this->checkAttribute($key,$val,@$layered[$key]?$layered[$key]:0);
                $productData[$key] = $optionId?$optionId:$val;
            } 
            $productData['stock_data'] = $this->getPData('stock_data');
            
            if (!$productId){
                //Add New Product
                try {

                    if ($productData['type_id'] == 'configurable'){
                        
                    } elseif ($productData['type_id'] == 'grouped'){

                    } elseif ($productData['type_id'] == 'simple'){
                        $product->setData($productData);
                    }
                    try {
                        $product->setWebsiteIDs($storeIds); 
                        //$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);

                        $product->save();

                        echo 'Product #'.$product->getId().' with sku  "'.$product->getSku().'" imported <br/>';
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        echo "<br/><br/>";
                    }

                } catch (Exception $e) {
                    Mage::log("Product ".$product->getSku().' NOT imported in store '.$storeId.' '.$e->getMessage(),null,'product.log');
                    echo "Product ".$product->getSku()." NOT imported  in store {$storeId} {$e->getMessage()} <br/>";
                }
//                }
            } else {
                
                //Update Product Data
//                Mage::log('*UPDATE PRODUCT*',null,'debug.log');
                unset($productData['type_id']);
                unset($productData['attribute_set_id']);
                
                try{
                    $product->addData($productData);
                    try {
                        $product->setWebsiteIDs($storeIds); 
                        //$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);

                        $product->save();
                        
                        echo 'Product #'.$product->getId().' with sku  "'.$product->getSku().'" was updated <br/>';
                        Mage::log('Product #'.$product->getId().' with sku  "'.$product->getSku().'" was updated',null,'product.log');
                    } catch (Exception $e) {
                        echo $e->getMessage();
                        echo "<br/><br/>";
                    }
                    
                } catch (Exception $e) {
                    Mage::log("Product ".$product->getSku().' NOT updated in store '.$storeId.' '.$e->getMessage(),null,'product.log');
                    echo "Product ".$product->getSku()." NOT updated  in store {$storeId} {$e->getMessage()} <br/>";
                }
            }
            Mage::getModel('cataloginventory/stock_item_api')->update($product->getId(), $productData);
                       
            try {
                if (count($this->_productImages)>0){
                    $this->addImportImages($this->_productImages,$product->getId());
                    
                    echo 'Product #'.$product->getId().' : images was added <br/>';
                    Mage::log('Product #'.$product->getId().' : images was added',null,'product.log');
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                echo "<br/><br/>";
            }

            try {
               if ($this->getGroups()!=array()){
                    $this->addProductToCategories($this->getGroups(),$product);
                    
                    echo 'Product #'.$product->getId().' was added in groups <br/>';
                    Mage::log('Product #'.$product->getId().' was added in groups',null,'product.log');
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                echo "<br/><br/>";
            }


            try {
               if ($this->getPProperty('parent')!=array()){
                    $this->addToProducts(
                        array(array(
                            $this->getPProperty('parent.sku')=> $this->getPProperty('parent.attribute')
                            )
                        ),$product);
                    
                    echo 'Product #'.$product->getId().' was added in parent product<br/>';
                    Mage::log('Product #'.$product->getId().' was added in parent product',null,'product.log');
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                echo "<br/><br/>";
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
            Mage::log("Inventory updating error: ".$e->getMessage(),null,'product.log');
            return false;
        }
    }
 
    public function addToProducts($parents,$product) {
     try {
        foreach($parents as $parent) {
            foreach($parent as $p=>$attribute) {
            Mage::log('Assign '.$product->getSku().' to product '.$p.' based on '.$attribute,null,'parents.log');
            $id = Mage::getModel('catalog/product')->getIdBySku($p);
            $parent = Mage::getModel('catalog/product')->load($id);

            if($parent->getSku()) {
                if($parent->getTypeId() =='grouped') {
                    $links = array();
                    $parent->getLinkInstance()->useGroupedLinks();
                    $attributesG = array();
                    foreach ($parent->getGroupedLinkCollection() as $_link) {
                        $links[$_link->getLinkedProductId()] = $_link->toArray($attributesG);
                    }
                    $links[$product->getId()] = array('position'  => 0);
                    $parent->setGroupedLinkData($links)->save();
                } elseif($parent->getTypeId() == 'configurable') {
                    $childrens_data = $parent->getConfigurableProductsData();
                    $attributes = $attribute;
                   foreach(explode(',',$attributes) as $attribute) {
                     $attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product',$attribute);
                     $attr_values = $this->__getAttList($attribute->getAttributeCode());
                            $a = array('attribute_id'=>$attribute->getId(),
                            'label'=>$product->getAttributeText($attribute->getAttributeCode()),
                            'value_index'=>$attr_values[$product->getAttributeText($attribute->getAttributeCode())],
                            'is_percent'=>0,
                            'pricing_value'=>0
                            );
                            $values[$attribute->getAttributeCode()] = $a;
                            $childrens_data[$product->getId()] = $a;
                   }
                        // Debug:
        //                print_r($attr_values);
        //                echo $attribute->getAttributeCode(). ' !! '.$product->getAttributeText($attribute->getAttributeCode());
        //                print_r($childrens_data);
        //                print_r(array_merge($attribute->getData(), array('label' => '', 'values' => $values)));
        //                  die;
                        $parent->setConfigurableProductsData(array_unique($childrens_data));
                        $tmp = array();
                            foreach($values as $key=>$val) {
                                $attr =  Mage::getModel('eav/config')->getAttribute('catalog_product',$key);
                                $tmp[] = array_merge($attr->getData(), array('label' => '', 'values' => array($val)));
                            }
                        $parent->setConfigurableAttributesData(
                                $tmp
                                );
                        $parent->setCanSaveConfigurableAttributes(true);
                        try { $parent->save(); } catch (Exception $e) { Mage::log($e->getMessage()); }

                    }
                } else {
                    if(isset($_SESSION['addTo'])) {
                        $parentsArr = unserialize($_SESSION['addTo']);
                        $parentsArr[$p] = array($product->getSku()=>$attribute);
                        $_SESSION['addTo'] = serialize($parentsArr);

                    } else {
                        $parentsArr = array($p=>array($product->getSku()=>$attribute));
                        $_SESSION['addTo'] = serialize($parentsArr);
                    }
                }
            }
        }
     } catch (Exception $e) {
         echo $e->getMessage();
     }
 }
 
    public function addToProductsAfter() {
        try {
        $parents = unserialize($_SESSION['addTo']);
        if (is_array($parents)){
            $model = Mage::getModel('catalog/product');

            foreach($parents as $parent=>$childs) {
                $id = Mage::getModel('catalog/product')->getIdBySku($parent);
                $parent = Mage::getModel('catalog/product')->load($id);
                if($parent->getSku()) {
                    if($parent->getTypeId() == 'grouped') {
                        foreach($childs as $child=>$attribute) {
                            $model = Mage::getModel('catalog/product');
                            $links[$model->getIdBySku($child)] = array('position'=> 0);
                            if($parent->setGroupedLinkData($links)->save()) {
                                unset($parents[$parent]);
                            }
                        }
                    } elseif($parent->getTypeId() =='configurable') {


                foreach($childs as $child=>$attributes) {
                    $child = $model->getIdBySku($child);
                    $product = Mage::getModel('catalog/product')->load($child);

                    foreach(explode(',',$attributes) as $attribute) {
                     $attr =  Mage::getModel('eav/config')->getAttribute('catalog_product',$attribute);
                     $attr_values = $this->__getAttList($attr->getAttributeCode());
                     $childrens_data = $parent->getConfigurableProductsData();
                            $a = array('attribute_id'=>$attr->getId(),
                                'label'=>$product->getAttributeText($attribute),
                                'value_index'=>$attr_values[$product->getAttributeText($attr->getAttributeCode())],
                                'is_percent'=>0,
                                'pricing_value'=>0
                                );
                            $values[$attr->getAttributeCode()] = $a;
                            $childrens_data[$child][] = $a;

                    }
                            // Debug:
//                    print_r($attr_values);
//                    echo $attr->getAttributeCode(). ' !! '.$product->getAttributeText($attr->getAttributeCode());
//                    print_r($childrens_data);
//                    print_r(array_merge($attr->getData(), array('label' => '', 'values' => $values)));
//                      die;
                            $parent->setConfigurableProductsData($childrens_data);
                            $tmp = array();
                            foreach($values as $key=>$val) {
                                $attr =  Mage::getModel('eav/config')->getAttribute('catalog_product',$key);
                                $tmp[] = array_merge($attr->getData(), array('label' => '', 'values' => array($val)));
                            }
                            $parent->setConfigurableAttributesData(
                                    $tmp
                                    );
                            $parent->setCanSaveConfigurableAttributes(true);
                            try { if($parent->save()) {  unset($parents[$parent]); } } catch (Exception $e) { Mage::log($e->getMessage()); }
                     }
                }
            }
        }
    }
        Mage::log('Parent assigned after files import : '.implode('-',array_keys($parents)));
         $_SESSION['addTo'] = serialize($parents);
     } catch (Exception $e) {
         echo $e->getMessage();
     }
 }
 

 //************************************************************************************//
    public function checkAttribute($code,$value,$layered=0) {
        $process = 0;
        if(Mage::registry('attributes')) {
            $attributes =   unserialize(Mage::registry('attributes'));
            if(in_array($code,$attributes)) {
                $process = 0;
            } else {
                $attributes[] = $code;
                $process = 1;
            }
        } else {
            $attributes = array($code);

            $process = 1;
        }
        Mage::unregister('attributes');
        Mage::register('attributes',serialize($attributes));
        if($process==1) {
            $attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product',$code);
//     if(!$attribute->getId()) {
//         $data = array('attribute_code' => $code,
//                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//                        'input' => $layered?'select':'text',
//                        'type' => $layered?'int':'text',
//                        'unique' => false,
//                        'required' => false,
//                        'configurable' => true,
//                        'searchable' =>true,
//                        'filterable' => $layered?true:false,
//                        'is_filterable_in_search' => $layered?true:false,
//                        'label' => $code,
//                        'comparable'        => true,
//                        'visible_on_front'  => false,
//                        'user_defined'      => true,
//                        'apply_to'          => '',
//                        'backend'                   => '',
//                        'frontend'                  => '',
//                        'class'                     => '',
//                        'source'                    => '',
//             );
//         $entityType = 'catalog_product';
//         $setup =  Mage::getModel('eav/entity_setup');
//         $entityTypeId     = $setup->getEntityTypeId($entityType);
//         $setup->addAttribute($entityTypeId, $code, $data);
//         $attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
//         $attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
//
//            $setup->addAttributeToGroup(
//                $entityTypeId,
//                $attributeSetId,
//                $attributeGroupId,
//                $code,
//                '10'
//            );
// 
//     }
        }
        $attribute =  Mage::getModel('eav/config')->getAttribute('catalog_product',$code);
        if($attribute->usesSource()) {
            if(Mage::registry($code.'_options')) {
                $opts = unserialize(Mage::registry($code.'_options'));
            } else {
                $options = $attribute->getFrontend()->getSelectOptions();
                $opts = array();
                foreach ($options as $option) {
                    if (Mage::helper('core/string')->strlen($option['value'])) {
                        $opts[$option['value']] = $option['label'];
                    }
                }
                Mage::register($code.'_options',serialize($opts));
            }
            if(!in_array($value,$opts) && !in_array($value,array_keys($opts)) && $value!='' && $attribute->getId() && $layered) {
                $o = Mage::getModel('eav/entity_attribute_option')->setAttributeId($attribute->getId())->setStoreId(0)->save();
                $optionId = $o->getId();
                $option = array();
                $option['attribute_id'] = $attribute->getId();
                $option['value'][$o->getId()][0] = $value;
                $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                $setup->addAttributeOption($option);
                $opts[$optionId] = $value;
                Mage::unregister($code.'_options');
                Mage::register($code.'_options',serialize($opts));
                return $optionId;
            } else {
                return array_search($value, $opts);
            }
        }
    }
    
    private function __getAttList($option) {
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->addFieldToFilter('attribute_code', $option)
            ->load(false);
        $attribute = $attributes->getFirstItem();
        $attribute->setSourceModel('eav/entity_attribute_source_table');
        $atts = $attribute->getSource()->getAllOptions(false);
        $result = array();
        foreach($atts as $tmp) {
            $result[$tmp['label']] = $tmp['value'];
        }
        return $result;
    }
    
    public function isLayered()
    {
        $attributeName = strtolower($this->_getVarModel()->getVar('wms_block_name'));
        $this->_layredAttributes[] = $attributeName;
    }

    public function addImportImages($importImages,$productId) {
        $product = Mage::getModel('catalog/product')->load($productId);
        $galery = Mage::getModel('catalog/product_attribute_media_api')->items($product->getId());
        $labels = array();
        foreach($galery as $im) {
            $labels[$im['file']] = $im['label'];
        }
        if(!is_dir(Mage::getBaseDir().'/media/importedImages/')) {
            mkdir(Mage::getBaseDir().'/media/importedImages/');
            chmod(Mage::getBaseDir().'/media/importedImages/',0777);
        }
        try {
            $i=0;
//            $server = trim(Mage::getStoreConfig('ftp/options/address'));
//            $folder = trim(Mage::getStoreConfig('ftp/options/outbound'));

//            $login = trim(Mage::getStoreConfig('ftp/options/login'));
//            $password = trim(Mage::getStoreConfig('ftp/options/password'));

//            if(!$server || !$login || !$password) {
//                Mage::log('Can\'t connect to FTP',null,'productsImport.log');
//                return false;
//            }
            foreach($importImages as $image) {
                if(trim($image)) {
                // path to remote file

                    $remote_file = str_replace($server.'/','',$image);
                    $remote_file = str_replace($server,'',$remote_file);
                    $remote_file = addslashes($remote_file);
                    $filename = preg_replace('/[^a-z0-9-_]/i','',$remote_file);
                    $local_file = Mage::getBaseDir('media').'/importedImages/'.$filename.'.jpg';

                    $labels[] = $product->getImageLabel();
                    foreach($product->getMediaGalleryImages() as $im) {
                        $labels[] = $im->getLabel();
                    }

                    if(!in_array($filename,$labels)) {
                        Mage::log('load '.$remote_file.' to '.$local_file,null,'productsImport.log');
                        //      echo "ftp://$login:$password@$server$folder$remote_file"; die;
//                        $c = curl_init("ftp://$login:$password@{$server}{$folder}{$remote_file}");
                        $c = curl_init($image);
                        // $local is the location to store file on local machine
                        $fh = fopen($local_file, 'w') or die('Can\'t open file');
                        $timestamp = 0;
                        if(is_file($local_file)) {
                            //don't fetch the actual page, you only want headers
                            curl_setopt($c, CURLOPT_NOBODY, true);
                            //stop it from outputting stuff to stdout
                            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                            // attempt to retrieve the modification date
                            curl_setopt($c, CURLOPT_FILETIME, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
                            curl_exec($c);
                            $timestamp = curl_getinfo($curl, CURLINFO_FILETIME);

                        }

                        if($timestamp<filemtime($local_file) && $timestamp!=-1 && !in_array($local_file,$labels)) { // ??? 
//                            $c = curl_init("ftp://$login:$password@{$server}{$folder}{$remote_file}");
                            $c = curl_init($image);
                            curl_setopt($c, CURLOPT_FILE, $fh);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
                            curl_exec($c);
                            curl_close($c);
                            fclose($fh);
                            $fh = null;
                            $c = null;
                            // if(filesize($local_file)>0) {
                            $newImage = array(
                                'file' => array(
                                    'content' => base64_encode(file_get_contents($local_file)),
                                    'mime'    => 'image/jpeg'
                                ),
                                'label'    => $local_file,
                                'types' => array('image','small_image','thumbnail'),
                                'position' => $i,
                                'exclude'  => 0
                            );

                            Mage::getModel('catalog/product_attribute_media_api')->create($product->getId(),  $newImage);
                            Mage::log('Image saved for product '.$product->getId(),null,'productsImport.log');
                            //}
                        }
                    }
                } else {
                    Mage::log('Can\'t read file '.$remote_file.' from FTP',null,'productsImport.log');
                }
                // close the connection and the file handler
                $i++;
            }
            $product = null; $newImage=null; $filename = null; $labels = null;
            $importImages=null;
        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'productsImport.log');
        }
    } 

    public function addProductToCategories($addToCategories,$product) {
        try {
            $am = Mage::getModel('catalog/category_api');
//            $root = $this->getRootCategoryId(); 
            foreach($addToCategories as $category) {
                if (!$category['id']){
                     $cat = Mage::getModel('catalog/category')->getCollection()
                        ->addAttributetoFilter('name',$category['name'])->getFirstItem();
                } else {
                    $cat = Mage::getModel('catalog/category')->load($category['id']);
                }
                
                $id  = $cat->getId();
                
                if(!$id && $this->isCreateCategory()) {
                    if($category['parent']) {
                        $parentId = Mage::getModel('catalog/category')->getCollection()
                                ->addAttributetoFilter('name',$category['parent'])->getFirstItem()->getId();
                    } else {
                        $parentId = $category['root_category_id'];//$root;
                    }
                    $category['is_active'] = 1;
                    $category['include_in_menu'] = 1;
                    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                    $category['path'] =  $parentCategory['path'];
                    $category['available_sort_by'] = array('position');
                    $category['default_sort_by'] = 'position';
                    if ($category['image']){
                        $category['image'] = $this->importCategoryImage($category['image']);
                    }
                    $id = Mage::getModel('catalog/category_api')->create($parentId, $category);
                }
                if($id) {
                    $am->assignProduct($id, $product->getId());
                }
            }

        } catch (Exception $e) {
            Mage::log('Category add product error '.$e->getMessage(),null,'productsImport.log');
            echo $e->getMessage()."<br/>";
        }
    }
    
    protected function isCreateCategory()
    {
        return $this->createCategory;
    }
    
    public function _getVarModel()
    {
        return Mage::getSingleton('cpwms/xmlformat_format_entity_variable');
    }
    
    public function addProductInList()
    {
        $this->_productsIdUpdate[] = $this->_getProduct()->getId(); 
    }
    public function getProductList()
    {
        $this->_productsIdUpdate[] = $this->_getProduct()->getId(); 
    }


    public function parentInStock()
    {
        $productsId = $this->getProductList();
        $collection = Mage::getModel('catalog/product')->getCollection();
        $id = $this->getStoreId();
        foreach($collection as $prod) {
            if (is_array($productsId) && !in_array($prod->getId(), $productsId)){
                continue;
            }
           if($prod->getTypeId()=='grouped') {
               $prod = Mage::getModel('catalog/product')->load($prod->getId());
               $simples = $prod->getTypeInstance(true)
                ->getAssociatedProducts($prod);
               $parent['is_in_stock']=0;
               foreach($simples as $simple) {
                if($simple->isInStock()) {
                    $parent['is_in_stock']=1; break;
                }
               }
                Mage::getModel('cataloginventory/stock_item_api')->update($prod->getId(), $parent);
                Mage::log("Product ".$prod->getSku().' stock updated (automatically as parent product) with InStock = '.$parent['is_in_stock'].' in store '.$id,null,'inventoryAdvice.log');
                echo "Product ".$prod->getSku()." updated (automatically as parent product) with InStock = ".$parent['is_in_stock']." in store {$id} <br/>";
           }
           elseif($prod->getTypeId()=='configurable') {
                $prod = Mage::getModel('catalog/product')->load($prod->getId());
               $simples = $prod->getTypeInstance(true)
                    ->getUsedProducts(null, $prod);
                $parent['is_in_stock']=0;
                foreach ($simples as $simple) {

                        if($simple->isInStock()) {
                            $parent['is_in_stock']=1; break;
                        }

                }
                Mage::getModel('cataloginventory/stock_item_api')->update($prod->getId(), $parent);
                Mage::log("Product ".$prod->getSku().' stock updated (automatically as parent product) with InStock = '.$parent['is_in_stock'].' in store '.$id,null,'inventoryAdvice.log');
                echo "Product ".$prod->getSku()." updated (automatically as parent product) with InStock = ".$parent['is_in_stock']." in store {$id} <br/>";
           }
        }
    }
    
    public function onlyUpdate()
    {
        $this->_onlyUpdate = true;
    }

    
    
    
    
    
    
    
     public function quickSaveProduct() 
    {
        $this->reorganizeProductData();
        try {
            $storeIds = array();
            foreach(Mage::app()->getStores() as $store) {
                if (Mage::getStoreConfig('common/partner/id', $store->getId()) == $this->getSiteId()){
                    $storeIds[] = $store->getId();
                }
            }

            $productId = $this->_getProductIdBySky($this->getPData('sku'));
            $productData = $this->getPData();
            
$attributes = Mage::getModel('eav/config')->getEntityAttributeCodes('catalog_product');

            $data = array_keys($productData);
$num = count($data);
		for ($c=0; $c < $num; $c++) {
			if(in_array($data[$c],$attributes)) { 
				$obj[$data[$c]] = array('type'=>Mage::getModel('eav/config')->getAttribute('catalog_product',$data[$c])->getBackendType(),'name'=>$data[$c],'id'=>Mage::getModel('eav/config')->getAttribute('catalog_product',$data[$c])->getId(),'source'=>Mage::getModel('eav/config')->getAttribute('catalog_product',$data[$c])->getSource());
				$import[] = $data[$c];
                                
			}
		}
            
                $output = '';
                
$available_types = array('int','text','varchar','decimal','datetime');
$connection = Mage::getModel('catalog/product')->getCollection()->getConnection();

	    foreach($productData as $c=>$v) {
                if(in_array($c,$import)) { 
                	      $sku = $productData['sku'];
	    			$id = Mage::getModel('catalog/product')->getIdBySku($sku); 
                		if(in_array($obj[$c]['type'],$available_types) && trim($v)!='') { 
	    				try { 
			    			$arr = $connection->fetchAll("SELECT count(*) as c FROM catalog_product_entity_".$obj[$c]['type']." WHERE entity_id = ".$id." AND attribute_id=".$obj[$c]['id']);
			    			if($obj[$c]['type']=='int') { 
			    				$source = $obj[$c]['source'];
			  	    				foreach($source->getAllOptions(false) as $option) {
							            if ($option['label']==$v) {
							                $v = trim($option['value']);
							      
							            }
							        }
			    			
			    			}
			        		if($obj[$c]['type']=='int' && strtolower($v) == 'yes') { 
                                                                 $v = 1;
                                                             }
                                                             
                                                             if($obj[$c]['type']=='int' && strtolower($v) == 'no') { 
                                                                 $v = 0;
                                                             } 
                                                             
							if($arr[0]['c']==0) { 
								$output .="INSERT INTO catalog_product_entity_".$obj[$c]['type']." VALUES (value_id,4,".$obj[$c]['id'].",0,".$id.",'".addslashes($v)."');";
							}
							 else { 
                                                         	$output.="UPDATE catalog_product_entity_".$obj[$c]['type']." SET value = '".addslashes($v)."' WHERE entity_id = ".$id." AND attribute_id=".$obj[$c]['id'].";\n";
							 }	
	    				} catch (Exception $e) { 
	    					$str = '';
	    					foreach ($data as $k=>$d) { 
	    						$str.=$k.' => '.$d.' ';
	    					}
	    					Mage::log($sku.' '.$id.' '.$str.' '.$obj[$c]['type'].'  Exception '.$e->getMessage());
	    				}
	    			} 
	    		
	    	}
	    }
            if(isset($productData['qty'])) { 
                	$output.="UPDATE cataloginventory_stock_item SET qty = '".(int)$productData['qty']."', is_in_stock='".(((int)$productData['qty']>0)?1:0)."' WHERE product_id = ".$id.";\n";
							 
                }
            
           Mage::log($output,null,'queries.log');;
            $connection->query($output);
            
            
        } catch (Exception $e) {
            echo $e->getMessage();
            Mage::log("Inventory updating error: ".$e->getMessage(),null,'product.log');
            return false;
        }
    }
}
