<?php
class Cafepress_CPCore_Block_Wms extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getWms()     
     { 
        if (!$this->hasData('cpcore')) {
            $this->setData('cpcore', Mage::registry('cpcore'));
        }
        return $this->getData('cpcore');
        
    }
}