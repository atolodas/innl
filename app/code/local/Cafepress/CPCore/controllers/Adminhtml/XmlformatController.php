<?php

class Cafepress_CPCore_Adminhtml_XmlformatController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/wms')
                ->_addBreadcrumb(Mage::helper('cpcore')->__('XML Format Manager'), Mage::helper('cpcore')->__('XML Format Manager'));

        $this->_title($this->__('cpcore'))->_title($this->__('Manage XML Format'));
        return $this;
    }

    public function _construct() {
        $session = Mage::getSingleton("customer/session");
        $session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
        return parent::_construct();
    }

    /**
     * Show grid
     * 
     */
    public function indexAction() {
        $this->_title($this->__('cpcore'))->_title($this->__('Manage Wms'));
        $this->_initAction()
             ->renderLayout();
    }

    /**
     * Create New Xml format
     */
    public function newAction() {
        Mage::register('xmlformat_type', $this->getRequest()->getParam('type'));
//        $this->_title($this->__('cpcore'))->_title($this->__('Manage Wms'));
        $this->_initXmlformat();

        $this->_initAction()
                ->renderLayout();
    }

    /**
     * Validate product
     *
     */
    public function validateAction() {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            //todo: validate new xml format
            $xmlformatData = $this->getRequest()->getPost('xmlformat');
////
//            if ($xmlformatData && !isset($xmlformatData['stock_data']['use_config_manage_stock'])) {
//                $xmlformatData['stock_data']['use_config_manage_stock'] = 0;
//            }
            $format = Mage::getModel('cpcore/xmlformat');
//            $format->setData('_edit_mode', true);
//            if ($storeId = $this->getRequest()->getParam('store')) {
//                $product->setStoreId($storeId);
//            }
//            if ($setId = $this->getRequest()->getParam('set')) {
//                $product->setAttributeSetId($setId);
//            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $format->setType($typeId);
            }
//            if ($productId = $this->getRequest()->getParam('id')) {
//                $product->load($productId);
//            }
//
            $dateFields = array();
            $attributes = $format;//->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $xmlformatData) && $xmlformatData[$attrKey] != '') {
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $xmlformatData = $this->_filterDates($xmlformatData, $dateFields);

            $format->addData($xmlformatData);
//            $format->validate(); //@todo: resave->not valid uniqui
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

    /**
     * Save xml format action
     */
    public function saveAction() {
        $storeId = (int) $this->getRequest()->getParam('store', false);
        $redirectBack = $this->getRequest()->getParam('back', false);
        $xmlformatId = (int) $this->getRequest()->getParam('id', false);
        $isEdit = (int) ($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost('xmlformat');
        
        if ($data) {
            try {
                foreach($data  as $key=>$val){
                    if (is_string($val)){
                        $data[$key]=trim($val);
                    }
                }
                
                $xmlformat = $this->_initXmlformat();
                $xmlformat->addData($data);
				/**
				 * Check "Use Default Value" checkboxes values
				 */
//				if ($useDefaults = $this->getRequest()->getPost('use_default')) {
//					foreach ($useDefaults as $attributeCode) {
//						$xmlformat->setData($attributeCode, false);
//					}
//				}
                $xmlformat->save();
                $xmlformatId = $xmlformat->getId();

                $this->_getSession()->addSuccess($this->__('The XML format has been saved.'));
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
                'id' => $xmlformatId,
                '_current' => true
            ));
        } else if ($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current' => true,
                'id' => $xmlformatId,
                'edit' => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store' => $storeId));
        }

        Mage::dispatchEvent('xmlformat_save_after', array());
    }

    /**
     * Edit Xml Format action
     */
    public function editAction() {
        Mage::register('xmlformat_type', $this->getRequest()->getParam('type'));
        $this->_initXmlformat();
        $this->_initAction()
            ->renderLayout();
    }

    protected function _initXmlformat() {
        $xmlformatId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);

        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $xmlformat = Mage::getModel('cpcore/xmlformat')
                ->setStoreId($storeId);
        if ($xmlformatId != false) {
            $xmlformat->load($xmlformatId);
        }

        Mage::register('xmlformat', $xmlformat,true);
        Mage::register('current_xmlformat', $xmlformat,true);
        return $xmlformat;
    }

    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_grid')->toHtml()
        );
    }

    /**
     * Delete format action
     */
    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('id')) {
            $xmlformat = Mage::getModel('cpcore/xmlformat')
                    ->load($id);
            try {
                $xmlformat->delete();
                $this->_getSession()->addSuccess($this->__('The XML Format has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store' => $this->getRequest()->getParam('store'))));
    }

    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction()
    {
        $formatsIds = (array)$this->getRequest()->getParam('format');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            foreach($formatsIds as $id){
                $format = Mage::getModel('cpcore/xmlformat')->load($id);
                $format->setStatus($status)->save();
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($formatsIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the XML Format(s) status.'));
        }

        $this->_redirect('*/*/', array('store'=> $storeId));
    }


}