<?php

class Cafepress_CPCore_Catalog_Product_CreateController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _initAction() {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        Mage::app()->setCurrentStore($storeId);
        
        $this->loadLayout()
            ->_setActiveMenu('catalog/cpcore')
            ->_addBreadcrumb(Mage::helper('cpcore')->__('Create Cafe Press Product'), Mage::helper('cpcore')->__('Create Cafe Press Product'));

        $this->_title($this->__('cpcore'))->_title($this->__('Create Cafe Press Product'));
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
                case 'merchandise':
                    $params['action'] = 'setparams';
                    $params['merchandise_id'] = $data['product_type'];
                    $params['cp_design_id'] = $this->getRequest()->getParam('cp_design_id');
                    $_SESSION['cp_type_content'] = $data['merchant_content'];
                    break;
                case 'setparams':
                    $params['name'] = $data['cp_name'];
                    $params['cp_image_location'] = $data['cp_image_location'];
                    $params['cp_sellprice'] = $data['cp_sellprice'];
                    $params['cp_media_height'] = $data['cp_height'];
                    $params['action'] = 'createremote';
                    $params['cp_ptn'] = $this->getRequest()->getParam('merchandise_id');
                    $params['cp_design_id'] = $this->getRequest()->getParam('cp_design_id');
                    break;
                case 'createremote':
                    $params['action'] = 'createlocal';
                    break;
            }
        } else{
            if(isset($data['set_new_print'])){
                $tmp_file_path = Mage::helper('cpcore/prints')->getPrintsPath().$_FILES['new_print']['name'];
                move_uploaded_file($_FILES['new_print']['tmp_name'], $tmp_file_path);
                $design_id = Mage::getModel('cpcore/cafepress_token')->uploadImage($tmp_file_path);
                if($design_id){
                    $params['cp_design_id'] = $design_id;
                    $info = pathinfo($tmp_file_path);
                    rename($tmp_file_path, Mage::helper('cpcore/prints')->getPrintsPath().$design_id.'.'.$info['extension']);
                }
            } else{
                $tmp_file_path = Mage::helper('cpcore/prints')->getPrintsPath().$data['selected_print'];
                $info = pathinfo($tmp_file_path);
                $params['cp_design_id'] = $info['filename'];
            }
            $params['action'] = 'merchandise';
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