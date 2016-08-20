<?php

class Snowcommerce_Seo_Block_Adminhtml_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	 //$order = Mage::getModel('sales/order')->loadByIncrementId($row->getIncrementId());
    	 
        return urldecode($row->getUrl());
    }
}

?>