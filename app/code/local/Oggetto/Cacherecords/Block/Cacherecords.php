<?php
class Oggetto_Cacherecords_Block_Cacherecords extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCacherecords()     
     { 
        if (!$this->hasData('cacherecords')) {
            $this->setData('cacherecords', Mage::registry('cacherecords'));
        }
        return $this->getData('cacherecords');
        
    }
}