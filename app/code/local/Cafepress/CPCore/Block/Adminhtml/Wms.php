<?php
class Cafepress_CPCore_Block_Adminhtml_Wms extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cpcore';
    $this->_blockGroup = 'cpcore';
    $this->_headerText = Mage::helper('cpcore')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('cpcore')->__('Add Item');
    parent::__construct();
  }
}