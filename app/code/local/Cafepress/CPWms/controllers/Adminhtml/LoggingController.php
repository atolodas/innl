<?php

class Cafepress_CPWms_Adminhtml_LoggingController extends Mage_Adminhtml_Controller_Action {

    public function _construct() {

        $session = Mage::getSingleton("customer/session");
//        $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
        return parent::_construct();
    }
    
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpwms')->__('WMS Logging'), Mage::helper('cpwms')->__('WMS Logging'));

        $this->_title($this->__('cpwms'))->_title($this->__('WMS Logging'));
        return $this;
    }
    
    protected function _initLog() {
        $logId = (int) $this->getRequest()->getParam('log_id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $logModel = Mage::getModel('wmslog/log')
                ->setStoreId($storeId);
        if ($logId != false) {
            $logModel->load($logId);
        }

        Mage::unregister('current_wms_log');
        Mage::register('current_wms_log', $logModel);
        return $logModel;
    }
    
    

    public function indexAction() {
        $this->_title($this->__('cpwms'))->_title($this->__('Wms Logging'));

        $this->_initAction()
                ->renderLayout();
    }
    
    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpwms/adminhtml_logging_grid')->toHtml()
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
        $logModel = Mage::getModel('wmslog/log')->load($logId);
        
        Mage::unregister('wms_log_logparent_id');
        Mage::register('wms_log_logparent_id', $logId);
        
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
            'wms_files' => $logModel->getWmsFiles(),
            'wms_statuses' => $logModel->getWmsStatuses()
        );

//        echo 'formatData';
//        Zend_Debug::dump($formatData);
//        echo 'logModel';
//        Zend_Debug::dump($logModel);
//        die();
        
        Mage::getModel('wmslog/log')->resend($formatData);
        
        Mage::unregister('wms_log_logparent_id');
        
        
        $this->_redirect('*/*/', array());
    }
    

}