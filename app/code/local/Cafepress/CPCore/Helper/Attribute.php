<?php
class Cafepress_CPCore_Helper_Attribute extends Mage_Core_Helper_Abstract
{
    const CACHE_TAG_CPCORE  = 'CPCORE';

    protected static $_attributeValueExist = array();

    /**
     * Get valueId for attribute
     * @param $arg_attribute
     * @param $arg_value
     * @return bool
     */
    public function addAttributeValue($arg_attribute, $arg_value)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);
        
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        if(!$this->attributeValueExists($arg_attribute, $arg_value))
        {
            $value['option'] = array($arg_value,$arg_value);
            $result = array('value' => $value);
            $attribute->setData('option',$result);
            $attribute->save();
        }
        
        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }
        return false;
    }
    
    public function attributeValueExists($arg_attribute, $arg_value)
    {
//        $cacheId = 'CATEGORY_PRODUCT_ATTR_LABEL_'.$arg_attribute.'_'.hash('md4',$arg_value).'_'.Mage::app()->getStore()->getId();
//        $attrValue = Mage::app()->getCache()->load($cacheId);
        
//        $result = false;
//        if(!$attrValue){
        if(!isset(self::$_attributeValueExist[$arg_attribute][$arg_value])){
            $attribute_model        = Mage::getModel('eav/entity_attribute');
            $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

            $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
            $attribute              = $attribute_model->load($attribute_code);

            $attribute_table        = $attribute_options_model->setAttribute($attribute);
            $options                = $attribute_options_model->getAllOptions(false);

            foreach($options as $option)
            {
                if ($option['label'] == $arg_value)
                {
                    $result =  $option['value'];
                    break;
                }
            }
            self::$_attributeValueExist[$arg_attribute][$arg_value] = $result;

//            Mage::app()->getCache()->save(serialize($result), $cacheId,array(self::CACHE_TAG_CPCORE));
        }
//        else {
//            $result = unserialize($result);
//        }
        
        return self::$_attributeValueExist[$arg_attribute][$arg_value];
    }
    
    public function getAttributeValue($arg_attribute, $arg_option_id)
    {
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_table        = Mage::getModel('eav/entity_attribute_source_table');
        
        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);
        
                                  $attribute_table->setAttribute($attribute);
                                  
        $option                 = $attribute_table->getOptionText($arg_option_id);
        
        return $option;
    }
}
