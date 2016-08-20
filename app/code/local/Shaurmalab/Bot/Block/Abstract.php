<?php

class Shaurmalab_Bot_Block_Abstract extends Mage_Core_Block_Template
{
	public function getBot()
	{
		if(Mage::registry('current_oggetto')) return Mage::registry('current_oggetto');
		return false;
	}
}
