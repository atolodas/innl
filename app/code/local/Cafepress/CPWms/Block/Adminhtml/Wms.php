<?php
class Cafepress_CPWms_Block_Adminhtml_Wms extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cpwms';
    $this->_blockGroup = 'cpwms';
    $this->_headerText = Mage::helper('cpwms')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('cpwms')->__('Add Item');
    parent::__construct();
  }
}