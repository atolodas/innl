<?php

class Cafepress_CPWms_Block_Adminhtml_Review_Grid_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_allStores = false;


    public function render(Varien_Object $row)
    {
        $storeId = $this->getColumn()->getIndex();
        if ($storeId){
            return Mage::app()->getStore($storeId)->getWebsite()->getName();
        }
        
        
    }
    
}

?>