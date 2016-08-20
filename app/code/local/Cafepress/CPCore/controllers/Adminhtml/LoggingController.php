<?php

class Cafepress_CPCore_Adminhtml_LoggingController extends Mage_Adminhtml_Controller_Action {

    public function _construct() {

        $session = Mage::getSingleton("customer/session");
//        $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
        return parent::_construct();
    }
    
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpcore')->__('WMS Logging'), Mage::helper('cpcore')->__('WMS Logging'));

        $this->_title($this->__('cpcore'))->_title($this->__('WMS Logging'));
        return $this;
    }
    
    protected function _initLog() {
        $logId = (int) $this->getRequest()->getParam('log_id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $logModel = Mage::getModel('cplog/log')
                ->setStoreId($storeId);
        if ($logId != false) {
            $logModel->load($logId);
        }

        Mage::unregister('current_cp_log');
        Mage::register('current_cp_log', $logModel);
        return $logModel;
    }
    
    

    public function indexAction() {
        $this->_title($this->__('cpcore'))->_title($this->__('Wms Logging'));

        $this->_initAction()
                ->renderLayout();
    }
    
    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpcore/adminhtml_logging_grid')->toHtml()
        );
    }

    public function requestAction()
    {
        $this->_initLog();
        
        $this->_initAction()
                ->renderLayout();
    }
    
    public function validateAction() {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            //todo: validate new xml format
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
    
    public function resendAction()
    {
        $resendData = $this->getRequest()->getParam('resend', false);
        $logId = (int)$this->getRequest()->getParam('log_id', false);
        $logModel = Mage::getModel('cplog/log')->load($logId);
        
        Mage::unregister('cp_log_logparent_id');
        Mage::register('cp_log_logparent_id', $logId);
        
        /*
         * Spase for Magik function 
         */
        $formatData = array(
            'format_id' => $logModel->getFormatId(),
            'condition' => '1',
            'function' => $resendData['function'],
            'request' => $resendData['request'],
            'response' => $logModel->getResponse(),//$resendData['response'],
            'response_format' => $resendData['response_format'],//$logModel->getRequestFormat(),
        //    'status' => 'Canceled',
            'link_to_file' => $logModel->getLinkToFile(),
            'order_id' => $logModel->getOrderId(),
            'url_of_request' => $resendData['url'],
            'cp_wms_files' => $logModel->getWmsFiles(),
            'cp_wms_statuses' => $logModel->getWmsStatuses()
        );

        Mage::getModel('cplog/log')->resend($formatData);
        Mage::unregister('cp_log_logparent_id');
        
        $this->_redirect('*/*/', array());
    }
    

}