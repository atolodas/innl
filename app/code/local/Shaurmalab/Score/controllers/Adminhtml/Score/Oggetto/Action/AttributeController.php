<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Score_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml score entity action attribute update controller
 *
 * @category   Mage
 * @package    Score_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Adminhtml_Score_Oggetto_Action_AttributeController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Score_Catalog');
    }

    public function editAction()
    {
        if (!$this->_validateEntitys()) {
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Update entity attributes
     */
    public function saveAction()
    {
        if (!$this->_validateEntitys()) {
            return;
        }

        /* Collect Data */
        $inventoryData      = $this->getRequest()->getParam('inventory', array());
        $attributesData     = $this->getRequest()->getParam('attributes', array());
        $websiteRemoveData  = $this->getRequest()->getParam('remove_website_ids', array());
        $websiteAddData     = $this->getRequest()->getParam('add_website_ids', array());

if($this->getRequest()->getParam('city_dict')) { 
	$attributesData = array_merge($attributesData, array('city_dict'=>$this->getRequest()->getParam('city_dict')));
}

if($this->getRequest()->getParam('country_dict')) {
        $attributesData = array_merge($attributesData, array('country_dict'=>$this->getRequest()->getParam('country_dict')));
}


        /* Prepare inventory data item options (use config settings) */
        foreach (Mage::helper('cataloginventory')->getConfigItemOptions() as $option) {
            if (isset($inventoryData[$option]) && !isset($inventoryData['use_config_' . $option])) {
                $inventoryData['use_config_' . $option] = 0;
            }
        }

        try {
            if ($attributesData) {
                $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                $storeId    = $this->_getHelper()->getSelectedStoreId();

                foreach ($attributesData as $attributeCode => $value) {
                    $attribute = Mage::getSingleton('eav/config')
                        ->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    if ($attribute->getBackendType() == 'datetime') {
                        if (!empty($value)) {
                            $filterInput    = new Zend_Filter_LocalizedToNormalized(array(
                                'date_format' => $dateFormat
                            ));
                            $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                                'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
                            ));
                            $value = $filterInternal->filter($filterInput->filter($value));
                        } else {
                            $value = null;
                        }
                        $attributesData[$attributeCode] = $value;
                    } elseif (in_array($attributeCode,array('city_dict','country_dict')) || $attribute->getFrontendInput() == 'multiselect') {
                        // Check if 'Change' checkbox has been checked by admin for this attribute
                        $isChanged = implode(',', $value);// (bool)$this->getRequest()->getPost($attributeCode . '_checkbox');
                        if (!$isChanged) {
                            unset($attributesData[$attributeCode]);
                            continue;
                        }
                        if (is_array($value)) {
                            $value = implode(',', $value);
                        }
                        $attributesData[$attributeCode] = $value;
                    }
                }

                Mage::getSingleton('score/oggetto_action')
                    ->updateAttributes($this->_getHelper()->getEntityIds(), $attributesData, $storeId);
            }
            if ($inventoryData) {
                /** @var $stockItem Score_CatalogInventory_Model_Stock_Item */
                $stockItem = Mage::getModel('cataloginventory/stock_item');
                $stockItem->setProcessIndexEvents(false);
                $stockItemSaved = false;

                foreach ($this->_getHelper()->getEntityIds() as $entityId) {
                    $stockItem->setData(array());
                    $stockItem->loadByEntity($entityId)
                        ->setEntityId($entityId);

                    $stockDataChanged = false;
                    foreach ($inventoryData as $k => $v) {
                        $stockItem->setDataUsingMethod($k, $v);
                        if ($stockItem->dataHasChangedFor($k)) {
                            $stockDataChanged = true;
                        }
                    }
                    if ($stockDataChanged) {
                        $stockItem->save();
                        $stockItemSaved = true;
                    }
                }

                if ($stockItemSaved) {
                    Mage::getSingleton('index/indexer')->indexEvents(
                        Mage_CatalogInventory_Model_Stock_Item::ENTITY,
                        Mage_Index_Model_Event::TYPE_SAVE
                    );
                }
            }

            if ($websiteAddData || $websiteRemoveData) {
                /* @var $actionModel Shaurmalab_Score_Model_Oggetto_Action */
                $actionModel = Mage::getSingleton('score/oggetto_action');
                $entityIds  = $this->_getHelper()->getEntityIds();

                if ($websiteRemoveData) {
                    $actionModel->updateWebsites($entityIds, $websiteRemoveData, 'remove');
                }
                if ($websiteAddData) {
                    $actionModel->updateWebsites($entityIds, $websiteAddData, 'add');
                }

                /**
                 * @deprecated since 1.3.2.2
                 */
                Mage::dispatchEvent('score_oggetto_to_website_change', array(
                    'entitys' => $entityIds
                ));

                $notice = Mage::getConfig()->getNode('adminhtml/messages/website_chnaged_indexers/label');
                if ($notice) {
                    $this->_getSession()->addNotice($this->__((string)$notice, $this->getUrl('adminhtml/process/list')));
                }
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) were updated', count($this->_getHelper()->getEntityIds()))
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the entity(s) attributes.'));
        }

        $this->_redirect('score/adminhtml_score_oggetto/');
    }

    /**
     * Validate selection of entitys for massupdate
     *
     * @return boolean
     */
    protected function _validateEntitys()
    {
        $error = false;
        $entityIds = $this->_getHelper()->getEntityIds();
        if (!is_array($entityIds)) {
            $error = $this->__('Please select entitys for attributes update');
        } //else if (!Mage::getModel('score/oggetto')->isEntitysHasSku($entityIds)) {
          //  $error = $this->__('Some of the processed entitys have no SKU value defined. Please fill it prior to performing operations on these entitys.');
        //}

        if ($error) {
            $this->_getSession()->addError($error);
            $this->_redirect('*/score_oggetto/', array('_current'=>true));
        }

        return !$error;
    }

    /**
     * Rertive data manipulation helper
     *
     * @return Shaurmalab_Score_Adminhtml_Helper_Score_Oggetto_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return Mage::helper('score/adminhtml_score_oggetto_edit_action_attribute');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('score/update_attributes');
    }

    /**
     * Attributes validation action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributesData = $this->getRequest()->getParam('attributes', array());
        $data = new Varien_Object();

        try {
            if ($attributesData) {
                $dateFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                $storeId    = $this->_getHelper()->getSelectedStoreId();

                foreach ($attributesData as $attributeCode => $value) {
                    $attribute = Mage::getSingleton('eav/config')
                        ->getAttribute('score_oggetto', $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    $data->setData($attributeCode, $value);
                    $attribute->getBackend()->validate($data);
                }
            }
        } catch (Score_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('An error occurred while updating the entity(s) attributes.'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
}
