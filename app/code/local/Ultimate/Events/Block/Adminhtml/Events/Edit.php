<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events admin edit form
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->_blockGroup = 'ultimate_events';
        $this->_controller = 'adminhtml_events';
        $this->_updateButton('save', 'label', Mage::helper('ultimate_events')->__('Save Events'));
        $this->_updateButton('delete', 'label', Mage::helper('ultimate_events')->__('Delete Events'));
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('ultimate_events')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText(){
        if( Mage::registry('current_events') && Mage::registry('current_events')->getId() ) {
            return Mage::helper('ultimate_events')->__("Edit Events '%s'", $this->escapeHtml(Mage::registry('current_events')->getTitle()));
        }
        else {
            return Mage::helper('ultimate_events')->__('Add Events');
        }
    }
}
