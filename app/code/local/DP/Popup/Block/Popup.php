<?php
class DP_Popup_Block_Popup extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
    	return parent::_prepareLayout();
    }
    
     public function getPopup()     
     { 
        if (!$this->hasData('popup')) {
            $this->setData('popup', Mage::registry('popup'));
        }
        return $this->getData('popup');
        
    }

    public function isEasyAjax() {
        return Mage::app()->getRequest()->getParam('easy_ajax');
    }
}