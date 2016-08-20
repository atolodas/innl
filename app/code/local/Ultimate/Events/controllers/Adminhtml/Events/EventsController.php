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
 * Events admin controller
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Adminhtml_Events_EventsController
    extends Ultimate_Events_Controller_Adminhtml_Events {
    /**
     * init the events
     * @access protected
     * @return Ultimate_Events_Model_Events
     */
    protected function _initEvents(){
        $eventsId  = (int) $this->getRequest()->getParam('id');
        $events    = Mage::getModel('ultimate_events/events');
        if ($eventsId) {
            $events->load($eventsId);
        }
        Mage::register('current_events', $events);
        return $events;
    }
     /**
     * default action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Events'));
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit events - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $eventsId    = $this->getRequest()->getParam('id');
        $events      = $this->_initEvents();
        if ($eventsId && !$events->getId()) {
            $this->_getSession()->addError(Mage::helper('ultimate_events')->__('This events no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getEventsData(true);
        if (!empty($data)) {
            $events->setData($data);
        }
        Mage::register('events_data', $events);
        $this->loadLayout();
        $this->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Events'));
        if ($events->getId()){
            $this->_title($events->getTitle());
        }
        else{
            $this->_title(Mage::helper('ultimate_events')->__('Add events'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }
    /**
     * new events action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction() {
        $this->_forward('edit');
    }
    /**
     * save events - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('events')) {
            try {
                $data = $this->_filterDates($data, array('date'));
                $events = $this->_initEvents();
                $events->addData($data);
                $events->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Events was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $events->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setEventsData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was a problem saving the events.'));
                Mage::getSingleton('adminhtml/session')->setEventsData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Unable to find events to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete events - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $events = Mage::getModel('ultimate_events/events');
                $events->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Events was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error deleting events.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Could not find events to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete events - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $eventsIds = $this->getRequest()->getParam('events');
        if(!is_array($eventsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Please select events to delete.'));
        }
        else {
            try {
                foreach ($eventsIds as $eventsId) {
                    $events = Mage::getModel('ultimate_events/events');
                    $events->setId($eventsId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Total of %d events were successfully deleted.', count($eventsIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error deleting events.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction(){
        $eventsIds = $this->getRequest()->getParam('events');
        if(!is_array($eventsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Please select events.'));
        }
        else {
            try {
                foreach ($eventsIds as $eventsId) {
                $events = Mage::getSingleton('ultimate_events/events')->load($eventsId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d events were successfully updated.', count($eventsIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error updating events.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * export as csv - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction(){
        $fileName   = 'events.csv';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'events.xls';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'events.xml';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('cms/ultimate_events/events');
    }
}
