<?php

class Oggetto_Cacherecords_Block_Adminhtml_Cacherecords_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('cacherecords_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('cacherecords')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('cacherecords')->__('Item Information'),
          'title'     => Mage::helper('cacherecords')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('cacherecords/adminhtml_cacherecords_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}