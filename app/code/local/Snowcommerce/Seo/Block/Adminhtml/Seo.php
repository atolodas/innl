<?php
class Snowcommerce_Seo_Block_Adminhtml_Seo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_seo';
    $this->_blockGroup = 'seo';
    $this->_headerText = Mage::helper('seo')->__('Seo Data Manager');
    $this->_addButtonLabel = Mage::helper('seo')->__('Add Data');
    parent::__construct();
  }
}
