<?php
class Cafepress_CPWms_Block_Wms extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getWms()     
     { 
        if (!$this->hasData('cpwms')) {
            $this->setData('cpwms', Mage::registry('cpwms'));
        }
        return $this->getData('cpwms');
        
    }
}