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
 * Events admin edit tabs
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct() {
        parent::__construct();
        $this->setId('events_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ultimate_events')->__('Events'));
    }
    /**
     * before render html
     * @access protected
     * @return Ultimate_Events_Block_Adminhtml_Events_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml(){
        $this->addTab('form_events', array(
            'label'        => Mage::helper('ultimate_events')->__('Events'),
            'title'        => Mage::helper('ultimate_events')->__('Events'),
            'content'     => $this->getLayout()->createBlock('ultimate_events/adminhtml_events_edit_tab_form')->toHtml(),
        ));
        $this->addTab('form_meta_events', array(
            'label'        => Mage::helper('ultimate_events')->__('Meta'),
            'title'        => Mage::helper('ultimate_events')->__('Meta'),
            'content'     => $this->getLayout()->createBlock('ultimate_events/adminhtml_events_edit_tab_meta')->toHtml(),
        ));
        if (!Mage::app()->isSingleStoreMode()){
            $this->addTab('form_store_events', array(
                'label'        => Mage::helper('ultimate_events')->__('Store views'),
                'title'        => Mage::helper('ultimate_events')->__('Store views'),
                'content'     => $this->getLayout()->createBlock('ultimate_events/adminhtml_events_edit_tab_stores')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve events entity
     * @access public
     * @return Ultimate_Events_Model_Events
     * @author Ultimate Module Creator
     */
    public function getEvents(){
        return Mage::registry('current_events');
    }
}
