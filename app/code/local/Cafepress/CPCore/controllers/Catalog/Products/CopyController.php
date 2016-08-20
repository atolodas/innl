<?php

class Cafepress_CPCore_Catalog_Products_CopyController extends Mage_Adminhtml_Controller_Action
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
        $params = array('store'=>$storeId);

        if($action) {
            switch ($action){
                case 'select_products':
//                    Mage::log($data, null, 'lomantik.log');
                    $_SESSION['cp_copy_data'] = $data;
                    $params['action'] = 'products_copied';
                    break;
                case 'select_section':
                    $_SESSION['cp_copy_section'] = $data['section'];
                    $params['action'] = 'select_products';
                    break;
            }
        } else{
            if (isset($data['cp_store'])){
                $_SESSION['cp_copy_store'] = $data['cp_store'];
                $params['action'] = 'select_section';
            } elseif(isset($data['section'])){
                $_SESSION['cp_copy_section'] = $data['section'];
                $params['action'] = 'select_products';
            }

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