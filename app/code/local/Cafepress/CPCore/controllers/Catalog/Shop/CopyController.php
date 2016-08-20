<?php

class Cafepress_CPCore_Catalog_Shop_CopyController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        Mage::app()->setCurrentStore($storeId);

        $this->loadLayout()
            ->_setActiveMenu('catalog/cpcore')
            ->_addBreadcrumb(Mage::helper('cpcore')->__('Copy Cafe Press Products'), Mage::helper('cpcore')->__('Copy Cafe Press Products'));

        $this->_title($this->__('cpcore'))->_title($this->__('Copy Cafe Press Products'));
        return $this;
    }

    public function indexAction(){
        $this->_initAction()->renderLayout();
    }

    public function continueAction()
    {
        $action = $this->getRequest()->getParam('action');
        $storeId = (int) $this->getRequest()->getParam('store', false);
        $data = $this->getRequest()->getPost();

        Mage::app()->setCurrentStore($storeId);
        $params = array(/*'store'=>$storeId*/);

        if($action) {
            switch ($action){
                case 'select_stores':
                    $_SESSION['cp_shop_copy_log'] = Mage::getModel('cpcore/cafepress_shops')->copy($data);
                    $params['action'] = 'copy_log';
                    break;
            }
        } else{
            $_SESSION['cp_shop_accounts_data'] = $data;
            $params['action'] = 'select_stores';
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/index', $params));
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $this->getResponse()->setBody($response->toJson());
    }
}