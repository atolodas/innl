<?php

class Cafepress_CPWms_Block_Adminhtml_Wms_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'cpwms';
        $this->_controller = 'adminhtml_wms';
        
        $this->_updateButton('save', 'label', Mage::helper('cpwms')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('cpwms')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('wms_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'wms_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'wms_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('wms_data') && Mage::registry('wms_data')->getId() ) {
            return Mage::helper('cpwms')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('wms_data')->getTitle()));
        } else {
            return Mage::helper('cpwms')->__('Add Item');
        }
    }
}