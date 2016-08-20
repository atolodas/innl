<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Grid_Renderer_Formatname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$formatId = $row->getData($this->getColumn()->getIndex());
        $format = Mage::getSingleton('cpwms/xmlformat')->load($formatId);
        
        return $format->getName();
    }
    
}

?>