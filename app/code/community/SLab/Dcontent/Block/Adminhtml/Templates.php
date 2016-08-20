<?php
/**
 * Templates grid container
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  /**
   * Init grid
   *
   */
  public function __construct()
  {
    $this->_controller = 'adminhtml_templates';
    $this->_blockGroup = 'dcontent';
    $this->_headerText = Mage::helper('dcontent')->__('Templates Manager');
    $this->_addButtonLabel = Mage::helper('dcontent')->__('Add Template');
	$this->_addButton('add_oggettos', array(
            'label'   => Mage::helper('catalog')->__('Add Oggettos Template'),
            'onclick' => "setLocation('{$this->getUrl('*/*/newOggettos')}')",
            'class'   => 'add'
        ));
    parent::__construct();
  }
}