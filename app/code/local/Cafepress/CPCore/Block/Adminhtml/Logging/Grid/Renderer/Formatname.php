<?php

class Cafepress_CPCore_Block_Adminhtml_Logging_Grid_Renderer_Formatname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$formatId = $row->getData($this->getColumn()->getIndex());
        $format = Mage::getSingleton('cpcore/xmlformat')->load($formatId);
        
        return $format->getName();
    }
    
}

?>