<?php
class Shaurmalab_Events_Block_Events extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEvents()     
     { 
        if (!$this->hasData('events')) {
            $this->setData('events', Mage::registry('events'));
        }
        return $this->getData('events');
        
    }
}