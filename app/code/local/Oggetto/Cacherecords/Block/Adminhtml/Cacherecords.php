<?php
class Oggetto_Cacherecords_Block_Adminhtml_Cacherecords extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cacherecords';
    $this->_blockGroup = 'cacherecords';
    $this->_headerText = Mage::helper('cacherecords')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('cacherecords')->__('Add Item');
    $this->_addButton('refresh_records', array(
            'label'     => Mage::helper('core')->__('Get Actual Cache info'),
            'onclick'   => 'setLocation(\'' . $this->getInfoUrl() .'\')',
            'class'     => 'add',
        ));

     $this->_addButton('mark_records', array(
            'label'     => Mage::helper('core')->__('Mark System records'),
            'onclick'   => 'setLocation(\'' . $this->getMarkUrl() .'\')',
            'class'     => 'add',
        ));
     $this->_addButton('get_content', array(
            'label'     => Mage::helper('core')->__('Get Content'),
            'onclick'   => 'setLocation(\'' . $this->getContentUrl() .'\')',
            'class'     => 'add',
        ));
    parent::__construct();
        $this->_removeButton('add');
  }

    public function getInfoUrl()
    {
        return $this->getUrl('*/*/info');
    }

    public function getMarkUrl()
    {
        return $this->getUrl('*/*/mark');
    }
    
    public function getContentUrl()
    {
        return $this->getUrl('*/*/content');
    }
}