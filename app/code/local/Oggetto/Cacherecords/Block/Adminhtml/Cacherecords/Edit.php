<?php

class Oggetto_Cacherecords_Block_Adminhtml_Cacherecords_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'cacherecords';
        $this->_controller = 'adminhtml_cacherecords';
        
        $this->_updateButton('save', 'label', Mage::helper('cacherecords')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('cacherecords')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('cacherecords_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'cacherecords_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'cacherecords_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('cacherecords_data') && Mage::registry('cacherecords_data')->getId() ) {
            return Mage::helper('cacherecords')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('cacherecords_data')->getTitle()));
        } else {
            return Mage::helper('cacherecords')->__('Add Item');
        }
    }
}