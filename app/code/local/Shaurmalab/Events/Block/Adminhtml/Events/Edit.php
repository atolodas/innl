<?php

class Shaurmalab_Events_Block_Adminhtml_Events_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'events';
        $this->_controller = 'adminhtml_events';
        
        $this->_updateButton('save', 'label', Mage::helper('events')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('events')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('events_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'events_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'events_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('events_data') && Mage::registry('events_data')->getId() ) {
            return Mage::helper('events')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('events_data')->getTitle()));
        } else {
            return Mage::helper('events')->__('Add Item');
        }
    }
}