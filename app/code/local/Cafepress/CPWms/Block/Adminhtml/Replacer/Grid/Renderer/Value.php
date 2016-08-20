<?php

class Cafepress_CPWms_Block_Adminhtml_Replacer_Grid_Renderer_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$replacerId = $row->getData($this->getColumn()->getIndex());
        $defaultValues = Mage::getSingleton('wmsreplacer/replacer_sub')->getDefaultValues($replacerId);


    	return implode(' | ',$defaultValues);
    }

}

?>