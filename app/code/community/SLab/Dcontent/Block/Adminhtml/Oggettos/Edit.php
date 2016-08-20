<?php
/**
 * Template edit page 
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */

class SLab_Dcontent_Block_Adminhtml_Oggettos_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
     * Prepare page
     *
     */
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'dcontent';
        $this->_controller = 'adminhtml_oggettos';
        
        $this->_updateButton('save', 'label', Mage::helper('dcontent')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('dcontent')->__('Delete Block'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('dcontent_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'dcontent_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'dcontent_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

	/**
     * Get page header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if( Mage::registry('dcontent_data') && Mage::registry('dcontent_data')->getId() ) {
            return Mage::helper('dcontent')->__("Edit Block '%s'", $this->htmlEscape(Mage::registry('dcontent_data')->getTitle()));
        } else {
            return Mage::helper('dcontent')->__('Add Block');
        }
    }

}