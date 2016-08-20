<?php

class Neklo_ABTesting_Block_Test extends Mage_Core_Block_Text
{
	public function setColor($color)
    {
        $this->setData('color', $color);
        return $this;
    }

    public function getColor()
    {
        return $this->getData('color');
    }

    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        if($this->getColor()) { 
        	return "<font style='color: {$this->getColor()}'>{$this->getText()}</font>";
        }
        return $this->getText();
    }

}