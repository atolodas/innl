<?php

class Cafepress_CPWms_Adminhtml_ReplacerController extends Mage_Adminhtml_Controller_Action {

    /**
     * Init controller actions
     * 
     * @return Oggetto_Wms_Adminhtml_XmlformatController 
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpwms')->__('XML Format replacer'), Mage::helper('cpwms')->__('XML Format replacer'));

        $this->_title($this->__('cpwms'))->_title($this->__('XML Format replacer'));
        return $this;
    }

    public function _construct() {
        $session = Mage::getSingleton("customer/session");
        $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
        return parent::_construct();
    }

    /**
     * Show grid
     */
    public function indexAction() {
        $this->_title($this->__('cpwms'))->_title($this->__('XML Format replacer'));

        $this->_initAction()
                ->renderLayout();
    }

    protected function _initReplacer() {
        $replacerId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $replacer = Mage::getModel('wmsreplacer/replacer')
                ->setStoreId($storeId);
        if ($replacerId != false) {
            $replacer->load($replacerId);
        }

        Mage::unregister('current_replacer');
        Mage::register('current_replacer', $replacer);
        return $replacer;
    }

    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpwms/adminhtml_replacer_grid')->toHtml()
        );
    }

    /**
     * Edit Xml Format action
     */
    public function editAction() {
        $this->_title($this->__('cpwms'))->_title($this->__('Edit WMS Replace'));
        $this->_initReplacer();

        $this->_initAction()
            ->renderLayout();
    }

    public function deleteAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        $replacerId = (int) $this->getRequest()->getParam('id', false);

        $redirectBack = false;

        if ($replacerId) {
            try {
                $replacer = $this->_initReplacer();
                $replacer->delete();

                $this->_getSession()->addSuccess($this->__('Replacer has been deleted.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
        $this->_redirect('*/*/edit', array(
            'id' => $replacerId,
            '_current' => true
        ));
        } else {
            $this->_redirect('*/*/', array('store' => $storeId));
        }
    }

    public function saveAction() {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        $redirectBack = $this->getRequest()->getParam('back', false);
        $replacerId = (int) $this->getRequest()->getParam('id', false);
        $isEdit = (int) ($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost('replacer');
        
        if ($data) {
            try {
                $replacer = $this->_initReplacer();

                $replacer->addData($data);
                $replacer->save();
                $replacerId = $replacer->getId();

                $replacer->setValues($data['values']);

                if(!$isEdit){
                    $replacerId = $replacer->getId();

                } else {

                }

                Mage::getSingleton('adminhtml/session')->setReplacerData($data);
                $this->_getSession()->addSuccess($this->__('Replacer has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setXmlformatData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setXmlformatData($data);
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id' => $replacerId,
                '_current' => true
            ));
        } else {
            $this->_redirect('*/*/', array('store' => $storeId));
        }

    }

    public function validateAction() {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            #TODO INL: edit validator for new/edit replacer
//            $xmlformatData = $this->getRequest()->getPost('xmlformat');

        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
    
    public function getPossibleValuesAction(){
        $result = array('error'=>true);
        if ($this->getRequest()->isPost()) {
            $precondition = $this->getRequest()->getPost('construction');
            if ($precondition){
                $possibleValues = Mage::getSingleton('cpwms/replacer')->getPossibleValues($precondition);
                
                $html = '<div id="orders_grid_content">';
                $html .= '<select multiple="multiple" size="10">';
                foreach ($possibleValues as $value) {
                    $html .= '<option>'.$value.'</option>';
                }
                $html .= '</select></div>';
                
                $result['update_possible_values_grid_section_html'] = $html;
                $result['error'] = false;
            }
        }
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($result)
        );
        return;
    }
}