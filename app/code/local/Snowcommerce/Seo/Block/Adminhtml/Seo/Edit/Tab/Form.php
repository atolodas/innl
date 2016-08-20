<?php

class Snowcommerce_Seo_Block_Adminhtml_Seo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	  $model = $this;
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('seo_form', array('legend'=>Mage::helper('seo')->__('Item information')));


      $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('seo')->__('Type'),
          'name'      => 'type',
          'values'    => array(
              array(
                  'value'     => 'url',
                  'label'     => Mage::helper('seo')->__('For Url Only'),
              ),
            array(
                  'value'     => 'category',
                  'label'     => Mage::helper('seo')->__('For All Category pages'),
              ),
              array(
                  'value'     => 'product',
                  'label'     => Mage::helper('seo')->__('For All Product pages'),
              ),
                            array(
                  'value'     => 'oggetto',
                  'label'     => Mage::helper('seo')->__('For Oggetto pages with selected type'),
              ),
          ),
      ));
	  
	   $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->load()
            ->toOptionHash(true);
					  	
					
	  $fieldset->addField('oggetto_type', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Use for Oggetto Type'),
          'name'      => 'oggetto_type',
          'values'   => $sets
      ));
	  

      $fieldset->addField('url', 'text', array(
          'label'     => Mage::helper('seo')->__('URL'),
          'class'     => '',
          'required'  => false,
          'name'      => 'url',
      ));

      $fieldset->addField('category', 'multiselect', array(
          'label'     => Mage::helper('seo')->__('Category'),
          'name'      => 'category',
          'values'    => Mage::getModel('seo/category')->toOptionArray()
      ));


      $priority = array();
      for($i = 0; $i<10; $i++) {
          $priority[] = array(
              'value'     => $i,
              'label'     => $i,
          );
      }
      $fieldset->addField('priority', 'select', array(
          'label'     => Mage::helper('seo')->__('Priority'),
          'name'      => 'priority',
          'values'    => $priority,
          'note'   => '0 - is highest priority'
      ));


      $fieldset->addField('head', 'editor', array(
          'label'     => Mage::helper('seo')->__('Head changes'),
          'class'     => 'editor',
          'name'      => 'head',
      ));


      $fieldset->addField('seo_tag', 'text', array(
          'label'     => Mage::helper('seo')->__('H1 Tag After Body'),
          'name'      => 'seo_tag',
      ));

      $fieldset->addField('robots', 'select', array(
          'label'     => Mage::helper('seo')->__('Robots'),
          'name'      => 'robots',
          'values'    => array_merge(array(array('value'=>'', 'label'=>'Use Default')),Mage::getModel('adminhtml/system_config_source_design_robots')->toOptionArray())
      ));

      $fieldset->addField('canonical', 'text', array(
          'label'     => Mage::helper('seo')->__('Canonical Link'),
          'name'      => 'canonical',
      ));
      

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('seo')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('seo')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('seo')->__('Disabled'),
              ),
          ),
      ));
     
     
     
      if ( Mage::getSingleton('adminhtml/session')->getSeoData() )
      {
      	$values = Mage::getSingleton('adminhtml/session')->getSeoData();
      	
      	if(isset($values['url'])) { $values['url'] = urldecode($values['url']); } 
          $form->setValues($values);
          Mage::getSingleton('adminhtml/session')->setSeoData(null);
      } elseif ( Mage::registry('seo_data') ) {
      	$values = Mage::registry('seo_data')->getData();
      	if(isset($values['url'])) { $values['url'] = urldecode($values['url']); }
          $form->setValues($values);
      }
      return parent::_prepareForm();
  }
}