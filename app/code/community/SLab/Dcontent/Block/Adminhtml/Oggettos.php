<?php
/**
 * Blocks grid container
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Oggettos extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  /**
   * Init grid
   *
   */
  public function __construct()
  {
    $this->_controller = 'adminhtml_oggettos';
    $this->_blockGroup = 'dcontent';
    $this->_headerText = Mage::helper('dcontent')->__('Oggettos Blocks Manager');
    $this->_addButtonLabel = Mage::helper('dcontent')->__('Add Block');
    parent::__construct();
  }
}