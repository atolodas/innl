<?php

class DP_Popup_Block_Adminhtml_Popup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('popup_form', array('legend'=>Mage::helper('popup')->__('Item information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('popup')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
          'note'      => "Any readable Name. Its not affect work process",
      ));
	  
	  $fieldset->addField('text_id', 'text', array(
          'label'     => Mage::helper('popup')->__('Text ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'text_id',
          'note'      => 'ID/class/tag of element that will be under action. Ex. #top div.page ul.linkslinks li.first a',
      ));
	  
	  $fieldset->addField('action', 'select', array(
          'label'     => Mage::helper('popup')->__('Action'),
          'name'      => 'action',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('popup')->__('Onclick'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('popup')->__('Onmouseover'),
              ),
			),
      ));
	  
	   $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('popup')->__('Type'),
          'name'      => 'type',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('popup')->__('Small'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('popup')->__('Big'),
              ),
			  
		   ),
            "note" => "Type of popup"
      ));
	  
	  $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('popup')->__('Url'),
          'required'  => false,
          'name'      => 'url',
          "note"    => "Url of Ajax request. Should not contain base site url. Ex.  /customer/account/login. If empty than Href attribute of element under action will be used"
      ));
	  
	   $fieldset->addField('url2', 'text', array(
          'label'     => Mage::helper('popup')->__('Url2'),
          'name'      => 'url2',
      ));
	  
	   $fieldset->addField('block', 'text', array(
          'label'     => Mage::helper('popup')->__('Block'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'block',
           "note"   => "Block identificator from layout which will be reached by ajax"
      ));
	  
	   $fieldset->addField('preload', 'select', array(
          'label'     => Mage::helper('popup')->__('Preload'),
          'name'      => 'preload',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('popup')->__('Yes'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('popup')->__('No'),
              ),
			  
		   ),
      ));
	  
	   $fieldset->addField('style', 'textarea', array( // TODO: rename field in database later
          'label'     => Mage::helper('popup')->__('Scripts executed after popup'),
          'name'      => 'style',
      ));
           
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('popup')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('popup')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('popup')->__('Disabled'),
              ),
			  
		   ),
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPopupData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPopupData());
          Mage::getSingleton('adminhtml/session')->setPopupData(null);
      } elseif ( Mage::registry('popup_data') ) {
          $form->setValues(Mage::registry('popup_data')->getData());
      }
      return parent::_prepareForm();
  }
}
