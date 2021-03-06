<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abtest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        $this->_objectId    = 'id';
        $this->_controller  = 'neklo_abtesting_abtest';

        parent::__construct();

        $this->_removeButton('reset');
        
        $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -100);
        
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
        
    }

    public function getAbtest() {
        return Mage::registry('current_abtest');
    }
    
    public function getHeaderText() {
        if ($this->getAbtest()->getId()) {
            return Mage::helper('neklo_abtesting')->__("Edit A/B Test '%s'", $this->escapeHtml($this->getAbtest()->getName()));
        }
        else {
            return Mage::helper('neklo_abtesting')->__('New A/B Test');
        }
    }

    public function getBackUrl() {
        return $this->getUrl('*/*');
    }
    
    protected function _getSaveAndContinueUrl() {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit'
        ));
    }

}
