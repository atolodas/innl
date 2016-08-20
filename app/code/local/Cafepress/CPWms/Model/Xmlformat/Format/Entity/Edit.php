<?php

class Cafepress_CPWms_Model_Xmlformat_Format_Entity_Edit extends Cafepress_CPWms_Model_Xmlformat_Format_Abstract
{
    protected $_incomXmlObj = false;
    protected $_attributes = array();
    protected $_values = array();
    
    protected $_filterArray = array(
        'wms_action'
    );
    
    public function __construct() {
        parent::__construct();
        $this->_attributes = array();
        $this->_values = array();
    }

    public function _getVarModel()
    {
        return Mage::getSingleton('cpwms/xmlformat_format_entity_variable');
    }
    
    protected function getPatnName()
    {
        return $this->_getVarModel()->getVar('wms_path_name');
    }


    public function setIncomingXmlObj($xmlObj)
    {
        $this->_incomXmlObj = $xmlObj;
        return $this;
    }
    
    public function setAttribute($name,$value)
    {
        $pathName = $this->getPatnName();
//        Zend_Debug::dump($pathName);
        
        $this->_attributes[] = array(
            'name'  => trim($name),
            'val'   => trim($value),
            'path'  => $pathName,
        );
        return $this;
    }
    
    public function processAttribute()
    {
        $xmlObj = $this->_incomXmlObj;
        
        foreach ($this->_attributes as $attribute){
            $path = $this->filterArray($attribute['path']);
            $attributeName = $attribute['name'];
            $attributeVal = $attribute['val'];
            if ($attributeVal==''){
                $attributeVal = '';
            }
//            $xmlObjLit = $xmlObj;
            $allPath = '';
            if (count($path)>0){
               foreach ($path as $val){
                    $allPath .= '->'.$val;
                } 
            }
            
            $objLocal = false;
            $attrLoc = NULL;
            eval ('$objLocal = $xmlObj'.$allPath.';');
//            Zend_Debug::dump($objLocal);
//            die ();
            if ($objLocal instanceof SimpleXMLElement){
                eval('$attrLoc = $objLocal->attributes()->'.$attributeName.';');
//                eval('$attrLoc = $objLocal->attributes()->'.'id'.';');
//                Zend_Debug::dump($attrLoc);
//                die(5);
                if (is_null($attrLoc)){
                    eval ('$xmlObj'.$allPath.'->addAttribute("'.$attributeName.'", "'.$attributeVal.'");');
                } else {
                    eval ('$xmlObj'.$allPath.'->attributes()->'.$attributeName.'= "'.$attributeVal.'";');
                }
            }
            

        }
        
        return $xmlObj;
    }
    
    protected function filterArray($array){
        if (!is_array($array)){
            return $array;
        }
        
        unset($array[0]);
        foreach ($array as $key => $val){
            if (in_array($val, $this->_filterArray)){
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    public function getResult()
    {
        $result = $this->processAttribute();
//        Zend_Debug::dump($result);
//        die();
        return $result->asXml();
    }

    
    
    
    
}
