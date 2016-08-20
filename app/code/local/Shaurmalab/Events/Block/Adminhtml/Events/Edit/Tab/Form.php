<?php

class Shaurmalab_Events_Block_Adminhtml_Events_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('events_form', array('legend'=>Mage::helper('events')->__('Item information')));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('events')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

$fieldset->addField('event_type', 'select', array(
          'label'     => Mage::helper('events')->__('Event type'),
          'name'      => 'event_type',
          'values'    => array(
              array(
                  'value'     => 'oggetto_created',
                  'label'     => Mage::helper('events')->__('Oggetto created'),
              ),
              array(
                  'value'     => 'oggetto_updated',
                  'label'     => Mage::helper('events')->__('Oggetto updated'),
              ),

              array(
                  'value'     => 'oggetto_deleted',
                  'label'     => Mage::helper('events')->__('Oggetto deleted'),
              ),

          ),
      ));

       $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

	  $fieldset->addField('oggetto_type', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Use for Oggetto Type'),
          'name'      => 'oggetto_type',
          'values'   => $sets
      ));

      $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setSortOrder()
            ->load();
		$attributes = array(array());


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
                $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                      $attributes[$child->getAttributeCode()] = array('value'=>$child->getAttributeCode(),'label'=>$child->getAttributeCode());

                }
            }
        }


	  $fieldset->addField('changed_attribute', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Changed attribute'),
          'name'      => 'changed_attribute',
          'values'   => $attributes,
          'memo' => 'For "Oggetto updated" event type only'
      ));

      $fieldset->addField('todo', 'select', array(
          'label'     => Mage::helper('events')->__('ToDo'),
          'name'      => 'todo',
          'values'    => array(
              array(
                  'value'     => 'create_new',
                  'label'     => Mage::helper('events')->__('Create 1 new Oggetto'),
              ),
              array(
                  'value'     => 'create_bulk',
                  'label'     => Mage::helper('events')->__('Create several Oggettos based on attribute'),
              ),

              array(
                  'value'     => 'modify_current',
                  'label'     => Mage::helper('events')->__('Modify current Oggetto'),
              ),


              array(
                  'value'     => 'modify_related',
                  'label'     => Mage::helper('events')->__('Modify related Oggettos'),
              ),

          ),
      ));

       $fieldset->addField('related_oggettos', 'text', array(
          'label'     => Mage::helper('events')->__('Related Oggettos'),
          'name'      => 'related_oggettos',
      ));

 	  $fieldset->addField('new_oggetto_type', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Act on Oggetto Type'),
          'name'      => 'new_oggetto_type',
          'values'   => $sets
      ));


       $fieldset->addField('attributes_values', 'text', array(
          'label'     => Mage::helper('events')->__('Attributes'),
          'name'      => 'attributes_values',
      ));



      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('events')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('events')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('events')->__('Disabled'),
              ),
          ),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getEventsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEventsData());
          Mage::getSingleton('adminhtml/session')->setEventsData(null);
      } elseif ( Mage::registry('events_data') ) {
          $form->setValues(Mage::registry('events_data')->getData());
      }
      return parent::_prepareForm();
  }
}