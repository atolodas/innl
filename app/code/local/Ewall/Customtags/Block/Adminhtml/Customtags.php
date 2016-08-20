<?php
class Ewall_Customtags_Block_Adminhtml_Customtags extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customtags';
    $this->_blockGroup = 'customtags';
    $this->_headerText = Mage::helper('customtags')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('customtags')->__('Add Item');
    parent::__construct();
  }
}
