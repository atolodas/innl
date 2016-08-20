<?php
class Ewall_Customtags_Block_Customtags extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCustomtags()     
     { 
        if (!$this->hasData('customtags')) {
            $this->setData('customtags', Mage::registry('customtags'));
        }
        return $this->getData('customtags');
        
    }
    public function getValues()
    {
		$prdct=Mage::getModel('customtags/customtags')->getCollection();
		foreach ($prdct as $collection) 
			{ 
				$var[]=$collection->getTitle();
				$var[]=$collection->getFilename();
				$var[]=$collection->getContent();
				$var[]=$collection->getStatus();
			}
			return $var;
				
	}
}
