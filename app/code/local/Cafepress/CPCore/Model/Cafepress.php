<?php

class Cafepress_CPCore_Model_Cafepress extends Mage_Core_Model_Abstract
{
    const CACHE_TAG_CPCORE  = 'CPCORE';
    const FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT = 'cafepress_create_product';

    public function editWmsHttpCurlSetopt($event){
        $ch = $event->getCurl();
    }
    
    public function createProduct($designId,$prototypeProductId, $additionalProductData = array()){
        $userToken = $this->getUserToken();
        $productData=array(
            'cp_design_id'     => $designId,
            'cp_user_token' => $userToken,
        );
        $productData = array_merge($additionalProductData,$productData);
        
        $product = Mage::getModel('cpcore/product')->getModifiedPrduct($prototypeProductId,$productData);

        $xml = Mage::getModel('cpcore/cafepress_product')->setProduct($product)->createNewCafepress(false,$additionalProductData);
        $xml = Mage::helper('cpcore')->checkXmlForCreateProduct($xml);
        $result = Mage::getModel('cpcore/cafepress_product')->setProduct($product)->saveCafepress($xml, false);
        
        return $result;
    }
    
    #TODO INL: It is dog-nail. It need rebuild!
    public function createDoubleSideProduct($designIdFront, $designIdBack ,$prototypeProductId, $additionalProductData = array()){
        $userToken = $this->getUserToken();
        $productData=array(
            'cp_design_id'          => $designIdFront,
            'cp_user_token'         => $userToken,
            'cp_static_design_id'   => $designIdBack
        );
        $productData = array_merge($additionalProductData,$productData);
        
        $product = Mage::getModel('cpcore/product')->getModifiedPrduct($prototypeProductId,$productData);

        $xml = Mage::getModel('cpcore/cafepress_product')->setProduct($product)->createNewCafepress(false);
        $xml = Mage::helper('cpcore')->checkXmlForCreateProduct($xml);
        
        $result = Mage::getModel('cpcore/cafepress_product')->setProduct($product)->saveCafepress($xml, false);
        return $result;
    }
    
    public function getXmlForSaveProductOnCafepress($product){
        $xmlformatModel = Mage::getModel('cpcore/xmlformat')->getModelFormatByApi(self::FORMAT_NAME_GET_CAFEPRESS_CREATE_PRODUCT);
        
        $xmlformatModel->addVariable('merchandise_id', $product->getCpPtn());
        
        $this->_getVarModel()->setVar('product',$product->getData());
        $this->_getVarModel()->setVar('store_id',Mage::getStoreConfig('cafepress_common/partner/storename'));
        
        $xmlformatModel->setServerResponse($xmlformatModel->getRequest());
        return $xmlformatModel->processResponse();
    }
    
    public function _getVarModel(){
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
    }
    
    public function getUserToken(){
        return Mage::getModel('cpcore/cafepress_token')->get();
    }
    
    
    /**
     * RELISE 2: Product Builder
     */
    public function createCafepressProduct($prototypeProductId){
        $userToken = $this->getUserToken();
    }

    public function changeXmlAttributes($xml, $attributes){
        $sxml = simplexml_load_string($xml);
        foreach($attributes as $name => $value){
            $sxml[0]->attributes()->$name = $value;
        }
        return $sxml->asXML();
    }
}
