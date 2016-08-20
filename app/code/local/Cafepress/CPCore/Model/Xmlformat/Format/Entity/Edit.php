<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Entity_Edit extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    protected $_incomXmlObj = false;
    protected $_attributes = array();
    protected $_values = array();
    
    protected $_filterArray = array(
        'wms_action'
    );
    
    protected $_exceptionPathArray = array(
        'wms_if',
        'wms_action'
    );
    
    public function __construct() {
        parent::__construct();
        $this->_attributes = array();
        $this->_values = array();
    }

    public function _getVarModel()
    {
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
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
        $exceptionPath = $this->_exceptionPathArray;
        
        foreach ($this->_attributes as $attribute){
            $path = $this->filterArray($attribute['path']);
            $attributeName = $attribute['name'];
            $attributeVal = $attribute['val'];
            if ($attributeVal==''){
                $attributeVal = '';
            }
            $allPath = '';
            if (count($path)>0){
               foreach ($path as $val){
                   if (!in_array($val, $exceptionPath)){
                       $allPath .= '->'.$val;
                   }
                } 
            }
            
            $objLocal = false;
            $attrLoc = NULL;
            eval ('$objLocal = $xmlObj'.$allPath.';');
            if ($objLocal instanceof SimpleXMLElement){
                eval('$attrLoc = $objLocal->attributes()->'.$attributeName.';');
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
        return $result->asXml();
    }

    
    
    
    
}
