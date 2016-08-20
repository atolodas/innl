<?php

class Cafepress_CPWms_Model_Cafepress_Merchandise extends Cafepress_CPWms_Model_Cafepress_Abstract
{
    protected $_merchandise = false;
    
    const WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE = 'cafepress_get_merchandise';
    const WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_FOR_CREATE_PRODUCT = 'cafepress_get_merchandise_for_create_product';

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
            $storeId = 0;
            $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                    ->setStoreId($storeId)
                    ->loadByAttribute('name','cafepress_get_merchandise');

            $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                        ->setStoreId($storeId)
                        ->load($xmlformatModel->getId());

            if (!$xmlformatModel->getName()){
                die('No format With name "'.'cafepress_get_merchandise'.'"!');
            }

            $xmlformatModel->processRequest();
//            $xmlResult = $xmlformatModel->getServerResponse();
//            $xmlformatModel->setServerResponse($this->getFakeMerchandise());

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
            $storeId = 0;
            $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                    ->setStoreId($storeId)
                    ->getModelformatByName(self::WMS_FORMAT_CAFEPRESS_GET_MERCHANDISE_FOR_CREATE_PRODUCT);
            
            #INL TEST: START
//            $cacheId = 'INL_TEMP_MERCHANT_COLLECT5';
//            $cacheData = Mage::app()->getCache()->load($cacheId);
//            if ($cacheData !== false) {
//                $requestResult = $cacheData;
//                $xmlformatModel->setServerResponse($requestResult);
//
//            } else {
                $xmlformatModel->processRequest();
                $requestResult = $xmlformatModel->getServerResponse();
//                Mage::app()->getCache()->save($requestResult, $cacheId);
//            }
            #INL TEST: END

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
    
}