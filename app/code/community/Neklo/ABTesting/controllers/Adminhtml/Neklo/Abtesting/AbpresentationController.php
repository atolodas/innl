<?php
class Neklo_ABTesting_Adminhtml_Neklo_Abtesting_AbpresentationController extends Mage_Adminhtml_Controller_Action {
    
    public function indexAction() {
        $this->_title($this->__('System'))
             ->_title($this->__('Manage A/B Presentations'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridAction() {
        $this->loadLayout();
         $this->getResponse()->setBody(
                $this->getLayout()->createBlock('neklo_abtesting/adminhtml_system_abpresentation_grid')->toHtml()
        );
    }    
    
      
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $presentation = Mage::getModel('neklo_abtesting/abpresentation')->load($id);

        if (!$presentation->getId() && $id) {
            $this->_getSession()->addError($this->__('This AB-presentation no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('current_presentation', $presentation);       
        
        
        $this->_title($this->__('System'))->_title($this->__('A/B Presentations'))->_title($this->__('Edit'));
        
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_addBreadcrumb($this->__('System'), $this->__('System'))
            ->_addBreadcrumb($this->__('A/B Tests'), $this->__('A/B Presentations'));
        $this->renderLayout();
    }
    
    public function saveAction() {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('presentation_id');
            $model = Mage::getModel('neklo_abtesting/abpresentation')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This AB-presentation no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            $data = $this->_filterDateTime($data, array('created_at'));
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');

            if (!empty($data['layout_update'])
            && !$validatorCustomLayout->isValid($data['layout_update'])) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }

            // init model and set data
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                
                if (class_exists('Enterprise_PageCache_Model_Cache')) {
                    $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
                    $cacheInstance->flush();
                }
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The AB-presentation has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('abpresentation_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    
    public function deleteAction() {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('neklo_abtesting/abpresentation');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The AB-presentation "%s" has been deleted.', $title));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a AB-presentation to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

   
}