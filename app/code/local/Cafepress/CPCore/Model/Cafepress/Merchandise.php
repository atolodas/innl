<?php

class Cafepress_CPCore_Model_Cafepress_Merchandise extends Cafepress_CPCore_Model_Cafepress
{
    protected $_merchandise                 = false;
    protected static $_merchandiseDataByIdResults  = array();

    const WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE = 'cafepress_get_merchandise';
    const WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_FOR_CREATE_PRODUCT   = 'cafepress_get_merchandise_for_create_product';
    const WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_DATA_BY_ID           = 'cafepress_get_merchandise_by_id';

    public function getProductTypes(){
        if(!file_exists(Mage::getBaseDir('media').'/cafepress')){
            return null;
        }
        if(!file_exists(Mage::getBaseDir('media').'/cafepress/marchandise')){
            return null;
        }
        $result = array();
        $merchandise_dir = opendir(Mage::getBaseDir('media').'/cafepress/merchandise/');
        while(($file = readdir($merchandise_dir)) !== false){
            $filepath = Mage::getBaseDir('media').'/cafepress/merchandise/'.$file;
            if(filetype($filepath) == 'file'){
                $info = pathinfo($filepath);
                if($info['extension'] == 'xml'){
                    $result[$file] = file_get_contents($filepath);
                }
            }
        }
        return $result;
    }

    public function getMerchandiseCollectionForSync()
    {
        if (!$this->_merchandise){
            $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE);

            $xmlformatModel->processRequest();

            $result = $xmlformatModel->processResponse();
            $this->_merchandise = $result;
            $_SESSION['cafepress_merchandise_collection'] = serialize($result);
        } else {
            $result = $this->_merchandise;
        }
        return $result;
    }

    public function getMerchandiseCollection()
    {
        if (!$this->_merchandise){
            $storeId = 0;
            $result = Mage::getModel('merchandise/merchandise')->getFormattedData();
            $this->_merchandise = $result;
            $_SESSION['cafepress_merchandise_collection'] = serialize($result);
        } else {
            $result = $this->_merchandise;
        }
        return $result;
    }
    
    public function getMerchandiseCollectionForCreateProducts()
    {
        if (!$this->_merchandise){
            $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_FOR_CREATE_PRODUCT);
            
            $xmlformatModel->processRequest();
            $requestResult = $xmlformatModel->getServerResponse();

            $result = $xmlformatModel->processResponse();
            $this->_merchandise = $result;
        } else {
            $result = $this->_merchandise;
        }
        return $result;
    }
    
    public function getDataById($id)
    {
        $allData = unserialize($_SESSION['cafepress_merchandise_collection']);
        
        if ($allData && ($allData!=array())){
            foreach($allData as $merchArr){
                if ($merchArr['id']==$id){
                    return $merchArr;
                }
            }
        }
        return array();
    }
    
    public function getOptions($product,$optionsName,$blockName = array('merchandise'))
    {
        $result = array();
        $productMerchandiseContent = $product->getCpMerchandiseContent();
        if (!$productMerchandiseContent) {
            return $result;
        }
        
        $xmlObj = new SimpleXMLElement($productMerchandiseContent);
        $optionsObj = $xmlObj->$optionsName;
        if ($optionsObj && (count($optionsObj)>0)){
            foreach($optionsObj as $option){
                $arr1 = (array)$option->attributes();
                $result[] = $arr1['@attributes'];
            } 
        }
        return $result;
    }
    
    public function getMerchandiseDataById($merchandiseId){
        if (!isset(self::$_merchandiseDataByIdResults[$merchandiseId])){
            $cacheId = 'CP_PRODUCT_DATA_CACHE_MERCHANDISE_ID_'.$merchandiseId;
            $result = Mage::app()->getCache()->load($cacheId);
            if (!$result){
                $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_DATA_BY_ID);
                $xmlformatModel->addVariable('merchandise_id', $merchandiseId);
                $xmlformatModel->processRequest();

                $result = $xmlformatModel->processResponse();

                Mage::app()->getCache()->save(serialize($result), $cacheId,array('CPCORE'));
            } else {
                $result = unserialize($result);
            }
            self::$_merchandiseDataByIdResults[$merchandiseId] = $result;
        }

        return self::$_merchandiseDataByIdResults[$merchandiseId];
    }
    
}