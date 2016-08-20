<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abpresentation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        $this->_objectId    = 'id';
        $this->_controller  = 'neklo_abtesting_abpresentation';

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

    public function getAbpresentation() {
        return Mage::registry('current_presentation');
    }
    
    public function getHeaderText() {
        if ($this->getAbpresentation()->getId()) {
            return Mage::helper('neklo_abtesting')->__("Edit A/B Presentation '%s'", $this->escapeHtml($this->getAbpresentation()->getName()));
        }
        else {
            return Mage::helper('neklo_abtesting')->__('New A/B Presentation');
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
