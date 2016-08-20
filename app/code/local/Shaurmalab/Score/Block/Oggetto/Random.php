<?php
/**
 * Create oggetto block
 * * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Random extends Mage_Core_Block_Template
{
	public $set;

    public function _toHtml()
    {
       $data = file_get_contents(Mage::getBaseUrl().'index.php/api/get/random/f/text/t/'.$this->getSet());
       return $data;
    }
}
