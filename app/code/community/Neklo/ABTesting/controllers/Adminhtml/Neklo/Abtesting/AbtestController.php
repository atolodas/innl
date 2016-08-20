<?php
class Neklo_ABTesting_Adminhtml_Neklo_Abtesting_AbtestController extends Mage_Adminhtml_Controller_Action {
    
    public function indexAction() {
        $this->_title($this->__('System'))
             ->_title($this->__('Manage A/B Tests'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }    
    
      
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $abtest = Mage::getModel('neklo_abtesting/abtest')->load($id);

        if (!$abtest->getId() && $id) {
            $this->_getSession()->addError($this->__('This abtest no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        // $variants = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($abtest->getId());
        // $abtest->addData(array(
        //     'variant_a' => $abtest->getVariantA(),
        //     'variant_b' => $abtest->getVariantB()
        // ));

        Mage::register('current_abtest', $abtest);       
        
        
        $this->_title($this->__('System'))->_title($this->__('A/B Tests'))->_title($this->__('Edit'));
        
        $this->loadLayout()
            ->_setActiveMenu('system')
            ->_addBreadcrumb($this->__('System'), $this->__('System'))
            ->_addBreadcrumb($this->__('A/B Tests'), $this->__('A/B Tests'));
        $this->renderLayout();
    }
    
    public function saveAction() {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('abtest_id');
            $model = Mage::getModel('neklo_abtesting/abtest')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This abtest no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            $this->_filterDates($data, array('start_at', 'end_at'));     

            // init model and set data
            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                
                $abTestId = $model->getId();
                if(isset($data['presentations'])) { 
                    $errorMessages = array();
                    foreach ($data['presentations'] as $presentation) {
                        $presentationIds[] = $presentation['presentation_id'];
                        $presentationChance[] = $presentation['chance'];
                    }
                    if(array_unique($presentationIds) != $presentationIds) { 
                        $errorMessages[] = Mage::helper('cms')->__('You can not link same presentation twice to the same AB-test');
                    }
                    if(array_sum($presentationChance) != 100) { 
                        $errorMessages[] = Mage::helper('cms')->__('Sum of all presentations chances should be equal 100%');
                    }

                    if(!empty($errorMessages)) { 
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The A/B test has been saved, but without presentations changes.'));
                
                        foreach ($errorMessages as $message) {
                            Mage::getSingleton('adminhtml/session')->addError($message);
                        }
                        Mage::getSingleton('adminhtml/session')->setFormData($data);
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    } else { 
                        Mage::getModel('neklo_abtesting/abtestpresentation')->updatePresentationsForAbTest($data['presentations'], $model);
                    } 
                }

                if(isset($data['events'])) { 
                    Mage::getModel('neklo_abtesting/abtestevent')->updateEventsForAbTest($data['events'], $model);
                }

                if (class_exists('Enterprise_PageCache_Model_Cache')) {
                    $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
                    $cacheInstance->flush();
                }
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The A/B test has been saved.'));
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
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('abtest_id')));
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
                $model = Mage::getModel('neklo_abtesting/abtest');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The abtest "%s" has been deleted.', $title));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a abtest to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

   
}