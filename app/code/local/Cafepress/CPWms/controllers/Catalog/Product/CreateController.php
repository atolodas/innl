<?php

class Cafepress_CPWms_Catalog_Product_CreateController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('catalog/cpwms')
            ->_addBreadcrumb(Mage::helper('cpwms')->__('Create Cafe Press Product'), Mage::helper('cpwms')->__('Create Cafe Press Product'));

        $this->_title($this->__('cpwms'))->_title($this->__('Create Cafe Press Product'));
        return $this;
    }

    public function indexAction(){
        $this->_initAction()->renderLayout();
    }

    public function continueAction()
    {
        $action = $this->getRequest()->getParam('action');
        $data = $this->getRequest()->getPost();
        $params = array();

        if($action) {
            switch ($action){
                case 'merchandise':
                    $params['action'] = 'setparams';
                    $params['merchandise_id'] = $data['product_type'];
                    $params['design_id'] = $this->getRequest()->getParam('design_id');
                    $_SESSION['cp_type_content'] = $data['merchant_content'];
                    break;
                case 'setparams':
                    $params['name'] = $data['cp_name'];
                    $params['image_location'] = $data['cp_image_location'];
                    $params['cp_sellprice'] = $data['cp_sellprice'];
                    $params['media_height'] = $data['cp_height'];
                    $params['action'] = 'createremote';
                    $params['cp_ptn'] = $this->getRequest()->getParam('merchandise_id');
                    $params['design_id'] = $this->getRequest()->getParam('design_id');
                    break;
                case 'createremote':
                    $params['action'] = 'createlocal';
                    break;
            }
        } else{
            if(isset($data['set_new_print'])){
                $tmp_file_path = Mage::getBaseDir('media').'/cafepress/prints/'.$_FILES['new_print']['name'];
                move_uploaded_file($_FILES['new_print']['tmp_name'], $tmp_file_path);
                $design_id = Mage::getModel('cpwms/cafepress_token')->uploadImage($tmp_file_path);
                if($design_id){
                    $params['design_id'] = $design_id;
                    $info = pathinfo($tmp_file_path);
                    rename($tmp_file_path, Mage::getBaseDir('media').'/cafepress/prints/'.$design_id.'.'.$info['extension']);
                }
            } else{
                $tmp_file_path = Mage::getBaseDir('media').'/cafepress/prints/'.$data['selected_print'];
                $info = pathinfo($tmp_file_path);
                $params['design_id'] = $info['filename'];
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