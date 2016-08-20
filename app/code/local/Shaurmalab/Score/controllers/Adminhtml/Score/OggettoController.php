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
 * Score oggetto controller
 *
 * @category   Mage
 * @package    Score_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Adminhtml_Score_OggettoController extends Mage_Adminhtml_Controller_Action
{
    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('edit');

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Score_Catalog');
    }

    /**
     * Initialize oggetto from request parameters
     *
     * @return Shaurmalab_Score_Model_oggetto
     */
    protected function _initoggetto()
    {
        $oggettoId  = (int) $this->getRequest()->getParam('id');
        $oggetto    = Mage::getModel('score/oggetto')
            ->setStoreId($this->getRequest()->getParam('store'));

        if (!$oggettoId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $oggetto->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $oggetto->setTypeId($typeId);
            }
        }

        $oggetto->setData('_edit_mode', true);
        if ($oggettoId) {
            try {
                $oggetto->load($oggettoId);
            } catch (Exception $e) {
                $oggetto->setTypeId(Shaurmalab_Score_Model_oggetto_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $oggetto->isConfigurable() &&
            (!$oggettoId || !$oggetto->getTypeInstance()->getUsedoggettoAttributeIds())) {
            $oggetto->getTypeInstance()->setUsedoggettoAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        // Required attributes of simple oggetto for configurable creation
        if ($this->getRequest()->getParam('popup')
            && $requiredAttributes = $this->getRequest()->getParam('required')) {
            $requiredAttributes = explode(",", $requiredAttributes);
            foreach ($oggetto->getAttributes() as $attribute) {
                if (in_array($attribute->getId(), $requiredAttributes)) {
                    $attribute->setIsRequired(1);
                }
            }
        }

        if ($this->getRequest()->getParam('popup')
            && $this->getRequest()->getParam('oggetto')
            && !is_array($this->getRequest()->getParam('oggetto'))
            && $this->getRequest()->getParam('id', false) === false) {

            $configoggetto = Mage::getModel('score/oggetto')
                ->setStoreId(0)
                ->load($this->getRequest()->getParam('oggetto'))
                ->setTypeId($this->getRequest()->getParam('type'));

            /* @var $configoggetto Shaurmalab_Score_Model_oggetto */
            $data = array();
            foreach ($configoggetto->getTypeInstance()->getEditableAttributes() as $attribute) {

                /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
                if(!$attribute->getIsUnique()
                    && $attribute->getFrontend()->getInputType()!='gallery'
                    && $attribute->getAttributeCode() != 'required_options'
                    && $attribute->getAttributeCode() != 'has_options'
                    && $attribute->getAttributeCode() != $configoggetto->getIdFieldName()) {
                    $data[$attribute->getAttributeCode()] = $configoggetto->getData($attribute->getAttributeCode());
                }
            }

            $oggetto->addData($data)
                ->setWebsiteIds($configoggetto->getWebsiteIds());
        }

        Mage::register('entity', $oggetto);
         Mage::register('oggetto', $oggetto);
        Mage::register('current_entity', $oggetto);
         Mage::register('current_oggetto', $oggetto);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $oggetto;
    }

    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Shaurmalab_Score_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $oggettosArray
     * @return Shaurmalab_Score_Adminhtml_Block_Score_Oggetto_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Shaurmalab_Score_Adminhtml_Block_Widget_Grid $gridBlock, $oggettosArray)
    {
        return $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setoggettos($oggettosArray)
            ->setInputElementName($inputName)
        ;
    }

    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }

    /**
     * oggetto list page
     */
    public function indexAction()
    {
        $this->_title($this->__(''))
             ->_title($this->__('Manage oggettos'));

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create new oggetto page
     */
    public function newAction()
    {
        $oggetto = $this->_initoggetto();

        $this->_title($this->__('New oggetto'));

        Mage::dispatchEvent('score_oggetto_new_action', array('oggetto' => $oggetto));

        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $_additionalLayoutPart = '';
            if ($oggetto->getTypeId() == Shaurmalab_Score_Model_oggetto_Type::TYPE_CONFIGURABLE
                && !($oggetto->getTypeInstance()->getUsedoggettoAttributeIds()))
            {
                $_additionalLayoutPart = '_new';
            }
            $this->loadLayout(array(
                'default',
                strtolower($this->getFullActionName()),
                'adminhtml_score_oggetto_'.$oggetto->getTypeId() . $_additionalLayoutPart
            ));
            $this->_setActiveMenu('score/oggettos');
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($oggetto->getStoreId());
        }

        $this->renderLayout();
    }

    /**
     * oggetto edit form
     */
    public function editAction()
    {
        $oggettoId  = (int) $this->getRequest()->getParam('id');
        $oggetto = $this->_initoggetto();

        if ($oggettoId && !$oggetto->getId()) {
            $this->_getSession()->addError(Mage::helper('score')->__('This oggetto no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($oggetto->getName());

        Mage::dispatchEvent('score_oggetto_edit_action', array('oggetto' => $oggetto));

        $_additionalLayoutPart = '';
        if ($oggetto->getTypeId() == Shaurmalab_Score_Model_oggetto_Type::TYPE_CONFIGURABLE
            && !($oggetto->getTypeInstance()->getUsedoggettoAttributeIds()))
        {
            $_additionalLayoutPart = '_new';
        }

        $this->loadLayout(array(
            'default',
            strtolower($this->getFullActionName()),
            'adminhtml_score_oggetto_'.$oggetto->getTypeId() . $_additionalLayoutPart
        ));

        $this->_setActiveMenu('score/oggettos');

        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($oggetto->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null))
                );
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($oggetto->getStoreId());
        }

        $this->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('score/adminhtml_score_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * oggetto grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('score/adminhtml_score_oggetto_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }

    /**
     * Get categories fieldset block
     *
     */
    public function categoriesAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get options fieldset block
     *
     */
    public function optionsAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get related oggettos grid and serializer block
     */
    public function relatedAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.related')
            ->setoggettosRelated($this->getRequest()->getPost('oggettos_related', null));
        $this->renderLayout();
    }

    /**
     * Get upsell oggettos grid and serializer block
     */
    public function upsellAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.upsell')
            ->setoggettosUpsell($this->getRequest()->getPost('oggettos_upsell', null));
        $this->renderLayout();
    }

    /**
     * Get crosssell oggettos grid and serializer block
     */
    public function crosssellAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.crosssell')
            ->setoggettosCrossSell($this->getRequest()->getPost('oggettos_crosssell', null));
        $this->renderLayout();
    }

    /**
     * Get related oggettos grid
     */
    public function relatedGridAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.related')
            ->setoggettosRelated($this->getRequest()->getPost('oggettos_related', null));
        $this->renderLayout();
    }

    /**
     * Get upsell oggettos grid
     */
    public function upsellGridAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.upsell')
            ->setoggettosRelated($this->getRequest()->getPost('oggettos_upsell', null));
        $this->renderLayout();
    }

    /**
     * Get crosssell oggettos grid
     */
    public function crosssellGridAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.crosssell')
            ->setoggettosRelated($this->getRequest()->getPost('oggettos_crosssell', null));
        $this->renderLayout();
    }

    /**
     * Get associated grouped oggettos grid and serializer block
     */
    public function superGroupAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.super.group')
            ->setoggettosGrouped($this->getRequest()->getPost('oggettos_grouped', null));
        $this->renderLayout();
    }

    /**
     * Get associated grouped oggettos grid only
     *
     */
    public function superGroupGridOnlyAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('score.oggetto.edit.tab.super.group')
            ->setoggettosGrouped($this->getRequest()->getPost('oggettos_grouped', null));
        $this->renderLayout();
    }

    /**
     * Get oggetto reviews grid
     *
     */
    public function reviewsAction()
    {
        $this->_initoggetto();
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.oggetto.reviews')
                ->setEntityId(Mage::registry('oggetto')->getId())
                ->setUseAjax(true);
        $this->renderLayout();
    }

    /**
     * Get super config grid
     *
     */
    public function superConfigAction()
    {
        $this->_initoggetto();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Deprecated since 1.2
     *
     */
    public function bundlesAction()
    {
        $oggetto = $this->_initoggetto();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('bundle/adminhtml_score_oggetto_edit_tab_bundle', 'admin.oggetto.bundle.items')
                ->setoggettoId($oggetto->getId())
                ->toHtml()
        );
    }

    /**
     * Validate oggetto
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $oggettoData = $this->getRequest()->getPost('entity');

            if ($oggettoData && !isset($oggettoData['stock_data']['use_config_manage_stock'])) {
                $oggettoData['stock_data']['use_config_manage_stock'] = 0;
            }
            /* @var $oggetto Shaurmalab_Score_Model_oggetto */
            $oggetto = Mage::getModel('score/oggetto');
            $oggetto->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $oggetto->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $oggetto->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $oggetto->setTypeId($typeId);
            }
            if ($oggettoId = $this->getRequest()->getParam('id')) {
                $oggetto->load($oggettoId);
            }

            $dateFields = array();
            $attributes = $oggetto->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $oggettoData) && $oggettoData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $oggettoData = $this->_filterDates($oggettoData, $dateFields);
            $oggetto->addData($oggettoData);

            /* set restrictions for date ranges */
            $resource = $oggetto->getResource();
            $resource->getAttribute('special_from_date')
                ->setMaxValue($oggetto->getSpecialToDate());
            $resource->getAttribute('news_from_date')
                ->setMaxValue($oggetto->getNewsToDate());
            $resource->getAttribute('custom_design_from')
                ->setMaxValue($oggetto->getCustomDesignTo());

            $oggetto->validate();
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $oggetto->validate())) {
//                foreach ($errors as $code => $error) {
//                    if ($error === true) {
//                        Mage::throwException(Mage::helper('score')->__('Attribute "%s" is invalid.', $oggetto->getResource()->getAttribute($code)->getFrontend()->getLabel()));
//                    }
//                    else {
//                        Mage::throwException($error);
//                    }
//                }
//            }
        }
        catch (Score_Eav_Model_oggetto_Attribute_Exception $e) {
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
     * Initialize oggetto before saving
     */
    protected function _initoggettosave()
    {
        $oggetto     = $this->_initoggetto();
        $oggettoData = $this->getRequest()->getPost('entity');
        if ($oggettoData) {
            $this->_filterStockData($oggettoData['stock_data']);
        }

          foreach ($this->getRequest()->getPost() as $key => $value) {
            if(is_array($value) &&  Mage::helper('score/oggetto')->isDictionaryAttribute($key)) {
                $oggettoData[$key] = implode(',',$value);
            }
        }
        /**
         * Websites
         */
        if (!isset($oggettoData['website_ids'])) {
            $oggettoData['website_ids'] = array();
        }

        $wasLockedMedia = false;
        if ($oggetto->isLockedAttribute('media')) {
            $oggetto->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $oggetto->addData($oggettoData);

        if ($wasLockedMedia) {
            $oggetto->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $oggetto->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($oggetto->getId() && isset($oggettoData['url_key_create_redirect']))
        // && $oggetto->getOrigData('url_key') != $oggetto->getData('url_key')
        {
            $oggetto->setData('save_rewrites_history', (bool)$oggettoData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $oggetto->setData($attributeCode, false);
            }
        }

        /**
         * Init oggetto links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$oggetto->getRelatedReadonly()) {
            $oggetto->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$oggetto->getUpsellReadonly()) {
            $oggetto->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$oggetto->getCrosssellReadonly()) {
            $oggetto->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$oggetto->getGroupedReadonly()) {
            $oggetto->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize oggetto categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $oggetto->setCategoryIds($categoryIds);
        }

        /**
         * Initialize data for configurable oggetto
         */
        if (($data = $this->getRequest()->getPost('configurable_oggettos_data'))
            && !$oggetto->getConfigurableReadonly()
        ) {
            $oggetto->setConfigurableoggettosData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$oggetto->getConfigurableReadonly()
        ) {
            $oggetto->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $oggetto->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_oggetto_attributes')
                && !$oggetto->getConfigurableReadonly()
        );

        /**
         * Initialize oggetto options
         */
        if (isset($oggettoData['options']) && !$oggetto->getOptionsReadonly()) {
            $oggetto->setoggettoOptions($oggettoData['options']);
        }

        $oggetto->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_oggetto_custom_options')
            && !$oggetto->getOptionsReadonly()
        );

        Mage::dispatchEvent(
            'score_oggetto_prepare_save',
            array('oggetto' => $oggetto, 'request' => $this->getRequest())
        );

        return $oggetto;
    }

    /**
     * Filter oggetto stock data
     *
     * @param array $stockData
     * @return null
     */
    protected function _filterStockData(&$stockData)
    {
        if (is_null($stockData)) {
            return;
        }
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }

    public function categoriesJsonAction()
    {
        $oggetto = $this->_initoggetto();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Save oggetto action
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $oggettoId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();

        if ($data) {
            $this->_filterStockData($data['oggetto']['stock_data']);

            $oggetto = $this->_initoggettosave();

            try {
                $oggetto->save();
                $oggettoId = $oggetto->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newoggetto = Mage::getModel('score/oggetto')
                            ->setStoreId($storeFrom)
                            ->load($oggettoId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                $this->_getSession()->addSuccess($this->__('The oggetto has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setoggettoData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $oggettoId,
                '_current'=>true
            ));
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $oggettoId,
                'edit'       => $isEdit
            ));
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * Create oggetto duplicate
     */
    public function duplicateAction()
    {
        $oggetto = $this->_initoggetto();
        try {
            $newoggetto = $oggetto->duplicate();
            $this->_getSession()->addSuccess($this->__('The oggetto has been duplicated.'));
            $this->_redirect('*/*/edit', array('_current'=>true, 'id'=>$newoggetto->getId()));
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }
    }

    /**
     * @deprecated since 1.4.0.0-alpha2
     */
    protected function _decodeInput($encoded)
    {
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }
        return $data;
    }

    /**
     * Delete oggetto action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $oggetto = Mage::getModel('score/oggetto')
                ->load($id);
            $sku = $oggetto->getSku();
            try {
                $oggetto->delete();
                $this->_getSession()->addSuccess($this->__('The oggetto has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }

    /**
     * Get tag grid
     */
    public function tagGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.oggetto.tags')
            ->setoggettoId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

    /**
     * Get alerts price grid
     */
    public function alertsPriceGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Get alerts stock grid
     */
    public function alertsStockGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * @deprecated since 1.5.0.0
     * @return Shaurmalab_Score_Adminhtml_Score_OggettoController
     */
    public function addCustomersToAlertQueueAction()
    {
        return $this;
    }

    public function addAttributeAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('score')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_initoggetto();
        $this->_addContent(
            $this->getLayout()->createBlock('score/adminhtml_score_oggetto_attribute_new_oggetto_created')
        );
        $this->renderLayout();
    }

    public function createdAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('score')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_addContent(
            $this->getLayout()->createBlock('score/adminhtml_score_oggetto_created')
        );
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $oggettoIds = $this->getRequest()->getParam('entity');
        if (!is_array($oggettoIds)) {
            $this->_getSession()->addError($this->__('Please select oggetto(s).'));
        } else {
            if (!empty($oggettoIds)) {
                try {
                    foreach ($oggettoIds as $oggettoId) {
                        $oggetto = Mage::getSingleton('score/oggetto')->load($oggettoId);

                        Mage::dispatchEvent('score_controller_oggetto_delete', array('oggetto' => $oggetto));
                        $oggetto->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($oggettoIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

     public function massDeleteAndBanAction()
    {
        $oggettoIds = $this->getRequest()->getParam('entity');
        if (!is_array($oggettoIds)) {
            $this->_getSession()->addError($this->__('Please select oggetto(s).'));
        } else {
            if (!empty($oggettoIds)) {
                try {
                    foreach ($oggettoIds as $oggettoId) {
                        $oggetto = Mage::getSingleton('score/oggetto')->load($oggettoId);

                        if($oggetto->getData('visitor_info')) {
                            $writeConnection = Mage::getSingleton('core/resource')->getConnection('core/write');
                            $query = "UPDATE visitors SET is_banned = 1 where visitor_info = :visitor_info";
                            $visitorInfo = explode(' ||| ', $oggetto->getData('visitor_info'));
                            if(isset($visitorInfo[1])) {
                                $binds = array(
                                    'visitor_info' => $visitorInfo[1]
                                );
                                $writeConnection->query($query, $binds);
                            }
                        }
                        Mage::dispatchEvent('score_controller_oggetto_delete', array('oggetto' => $oggetto));
                        $oggetto->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($oggettoIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update oggetto(s) status action
     *
     */
    public function massStatusAction()
    {
        $oggettoIds = (array)$this->getRequest()->getParam('oggetto');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($oggettoIds, $status);
            Mage::getSingleton('score/oggetto_action')
                ->updateAttributes($oggettoIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($oggettoIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the oggetto(s) status.'));
        }

        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * Validate batch of oggettos before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $oggettoIds
     * @param  int $status
     * @return void
     */
    public function _validateMassStatus(array $oggettoIds, $status)
    {
        if ($status == Shaurmalab_Score_Model_oggetto_Status::STATUS_ENABLED) {
            if (!Mage::getModel('score/oggetto')->isoggettosHasSku($oggettoIds)) {
                throw new Mage_Core_Exception(
                    $this->__('Some of the processed oggettos have no SKU value defined. Please fill it prior to performing operations on these oggettos.')
                );
            }
        }
    }

    /**
     * Get tag customer grid
     *
     */
    public function tagCustomerGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.oggetto.tags.customers')
                ->setoggettoId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

    public function quickCreateAction()
    {
        $result = array();

        /* @var $configurableoggetto Shaurmalab_Score_Model_oggetto */
        $configurableoggetto = Mage::getModel('score/oggetto')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->load($this->getRequest()->getParam('oggetto'));

        if (!$configurableoggetto->isConfigurable()) {
            // If invalid parent oggetto
            $this->_redirect('*/*/');
            return;
        }

        /* @var $oggetto Shaurmalab_Score_Model_oggetto */

        $oggetto = Mage::getModel('score/oggetto')
            ->setStoreId(0)
            ->setTypeId(Shaurmalab_Score_Model_oggetto_Type::TYPE_SIMPLE)
            ->setAttributeSetId($configurableoggetto->getAttributeSetId());


        foreach ($oggetto->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($attribute->getIsUnique()
                || $attribute->getAttributeCode() == 'url_key'
                || $attribute->getFrontend()->getInputType() == 'gallery'
                || $attribute->getFrontend()->getInputType() == 'media_image'
                || !$attribute->getIsVisible()) {
                continue;
            }

            $oggetto->setData(
                $attribute->getAttributeCode(),
                $configurableoggetto->getData($attribute->getAttributeCode())
            );
        }

        $oggetto->addData($this->getRequest()->getParam('simple_oggetto', array()));
        $oggetto->setWebsiteIds($configurableoggetto->getWebsiteIds());

        $autogenerateOptions = array();
        $result['attributes'] = array();

        foreach ($configurableoggetto->getTypeInstance()->getConfigurableAttributes() as $attribute) {
            $value = $oggetto->getAttributeText($attribute->getoggettoAttribute()->getAttributeCode());
            $autogenerateOptions[] = $value;
            $result['attributes'][] = array(
                'label'         => $value,
                'value_index'   => $oggetto->getData($attribute->getoggettoAttribute()->getAttributeCode()),
                'attribute_id'  => $attribute->getoggettoAttribute()->getId()
            );
        }

        if ($oggetto->getNameAutogenerate()) {
            $oggetto->setName($configurableoggetto->getName() . '-' . implode('-', $autogenerateOptions));
        }

        if ($oggetto->getSkuAutogenerate()) {
            $oggetto->setSku($configurableoggetto->getSku() . '-' . implode('-', $autogenerateOptions));
        }

        if (is_array($oggetto->getPricing())) {
           $result['pricing'] = $oggetto->getPricing();
           $additionalPrice = 0;
           foreach ($oggetto->getPricing() as $pricing) {
               if (empty($pricing['value'])) {
                   continue;
               }

               if (!empty($pricing['is_percent'])) {
                   $pricing['value'] = ($pricing['value']/100)*$oggetto->getPrice();
               }

               $additionalPrice += $pricing['value'];
           }

           $oggetto->setPrice($oggetto->getPrice() + $additionalPrice);
           $oggetto->unsPricing();
        }

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
//            if (is_array($errors = $oggetto->validate())) {
//                $strErrors = array();
//                foreach($errors as $code=>$error) {
//                    $codeLabel = $oggetto->getResource()->getAttribute($code)->getFrontend()->getLabel();
//                    $strErrors[] = ($error === true)? Mage::helper('score')->__('Value for "%s" is invalid.', $codeLabel) : Mage::helper('score')->__('Value for "%s" is invalid: %s', $codeLabel, $error);
//                }
//                Mage::throwException('data_invalid', implode("\n", $strErrors));
//            }

            $oggetto->validate();
            $oggetto->save();
            $result['oggetto_id'] = $oggetto->getId();
            $this->_getSession()->addSuccess(Mage::helper('score')->__('The oggetto has been created.'));
            $this->_initLayoutMessages('adminhtml/session');
            $result['messages']  = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = array(
                'message' =>  $e->getMessage(),
                'fields'  => array(
                    'sku'  =>  $oggetto->getSku()
                )
            );

        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = array(
                'message'   =>  $this->__('An error occurred while saving the oggetto. ') . $e->getMessage()
             );
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('score/oggettos');
    }

    /**
     * Show item update result from updateAction
     * in Wishlist and Cart controllers.
     *
     */
    public function showUpdateResultAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->hasCompositeoggettoResult() && $session->getCompositeoggettoResult() instanceof Varien_Object){
            /* @var $helper Shaurmalab_Score_Adminhtml_Helper_Score_Oggetto_Composite */
            $helper = Mage::helper('score/adminhtml_score_oggetto_composite');
            $helper->renderUpdateResult($this, $session->getCompositeoggettoResult());
            $session->unsCompositeoggettoResult();
        } else {
            $session->unsCompositeoggettoResult();
            return false;
        }
    }
}
