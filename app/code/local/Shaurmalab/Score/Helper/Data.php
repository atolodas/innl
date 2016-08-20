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
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score data helper
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PRICE_SCOPE_GLOBAL               = 0;
    const PRICE_SCOPE_WEBSITE              = 1;
    const XML_PATH_PRICE_SCOPE             = 'score/price/scope';
    const XML_PATH_SEO_SAVE_HISTORY        = 'score/seo/save_rewrites_history';
    const CONFIG_USE_STATIC_URLS           = 'cms/wysiwyg/use_static_urls_in_score';
    const CONFIG_PARSE_URL_DIRECTIVES      = 'score/frontend/parse_url_directives';
    const XML_PATH_CONTENT_TEMPLATE_FILTER = 'global/score/content/tempate_filter';
    const XML_PATH_DISPLAY_OGGETTO_COUNT   = 'score/layered_navigation/display_oggetto_count';

    /**
     * Minimum advertise price constants
     */
    const XML_PATH_MSRP_ENABLED = 'sales/msrp/enabled';
    const XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE = 'sales/msrp/display_price_type';
    const XML_PATH_MSRP_APPLY_TO_ALL = 'sales/msrp/apply_for_all';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE = 'sales/msrp/explanation_message';
    const XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS = 'sales/msrp/explanation_message_whats_this';


    /**
     * Breadcrumb Path cache
     *
     * @var string
     */
    protected $_categoryPath;

    /**
     * Array of oggetto types that MAP enabled
     *
     * @var array
     */
    protected $_mapApplyToOggettoType = null;

    /**
     * Currenty selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return Shaurmalab_Score_Helper_Data
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Return current category path or get it from current category
     * and creating array of categories|oggetto paths for breadcrumbs
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {

            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path['category'.$categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link' => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : ''
                        );
                    }
                }
            }

            if ($this->getOggetto()) {
                $path['oggetto'] = array('label'=>$this->getOggetto()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }

    /**
     * Check is category link
     *
     * @param int $categoryId
     * @return bool
     */
    protected function _isCategoryLink($categoryId)
    {
        if ($this->getOggetto()) {
            return true;
        }
        if ($categoryId != $this->getCategory()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Return current category object
     *
     * @return Shaurmalab_Score_Model_Category|null
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Retrieve current Oggetto object
     *
     * @return Shaurmalab_Score_Model_Oggetto|null
     */
    public function getOggetto()
    {
        return Mage::registry('current_oggetto');
    }

    /**
     * Retrieve Visitor/Customer Last Viewed URL
     *
     * @return string
     */
    public function getLastViewedUrl()
    {
        if ($oggettoId = Mage::getSingleton('score/session')->getLastViewedOggettoId()) {
            $oggetto = Mage::getModel('score/oggetto')->load($oggettoId);
            /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
            if (Mage::helper('score/oggetto')->canShow($oggetto, 'score')) {
                return $oggetto->getOggettoUrl();
            }
        }
        if ($categoryId = Mage::getSingleton('score/session')->getLastViewedCategoryId()) {
            $category = Mage::getModel('score/category')->load($categoryId);
            /* @var $category Shaurmalab_Score_Model_Category */
            if (!Mage::helper('score/category')->canShow($category)) {
                return '';
            }
            return $category->getCategoryUrl();
        }
        return '';
    }

    /**
     * Split SKU of an item by dashes and spaces
     * Words will not be broken, unless thir length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return array
     */
    public function splitSku($sku, $length = 30)
    {
        return Mage::helper('core/string')->str_split($sku, $length, true, false, '[\-\s]');
    }

    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields()
    {
        if (Mage::registry('attribute_type_hidden_fields')) {
            return Mage::registry('attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (Mage::registry('attribute_type_disabled_types')) {
            return Mage::registry('attribute_type_disabled_types');
        } else {
            return array();
        }
    }

    /**
     * Retrieve Score Price Scope
     *
     * @return int
     */
    public function getPriceScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRICE_SCOPE);
    }

    /**
     * Is Global Price
     *
     * @return bool
     */
    public function isPriceGlobal()
    {
        return $this->getPriceScope() == self::PRICE_SCOPE_GLOBAL;
    }

    /**
     * Indicate whether to save URL Rewrite History or not (create redirects to old URLs)
     *
     * @param int $storeId Store View
     * @return bool
     */
    public function shouldSaveUrlRewritesHistory($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEO_SAVE_HISTORY, $storeId);
    }

    /**
     * Check if the store is configured to use static URLs for media
     *
     * @return bool
     */
    public function isUsingStaticUrlsAllowed()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_USE_STATIC_URLS, $this->_storeId);
    }

    /**
     * Check if the parsing of URL directives is allowed for the catalog
     *
     * @return bool
     */
    public function isUrlDirectivesParsingAllowed()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_PARSE_URL_DIRECTIVES, $this->_storeId);
    }

    /**
     * Retrieve template processor for score content
     *
     * @return Varien_Filter_Template
     */
    public function getPageTemplateProcessor()
    {
        $model = (string)Mage::getConfig()->getNode(self::XML_PATH_CONTENT_TEMPLATE_FILTER);
        return Mage::getModel($model);
    }

    /**
    * Initialize mapping for old and new field names
    *
    * @return array
    */
    public function getOldFieldMap()
    {
        $node = Mage::getConfig()->getNode('global/score_oggetto/old_fields_map');
        if ($node === false) {
            return array();
        }
        return (array) $node;
    }
    /**
     * Check if Minimum Advertised Price is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_MSRP_ENABLED, $this->_storeId);
    }

    /**
     * Return MAP display actual type
     *
     * @return null|string
     */
    public function getMsrpDisplayActualPriceType()
    {
        return Mage::getStoreConfig(self::XML_PATH_MSRP_DISPLAY_ACTUAL_PRICE_TYPE, $this->_storeId);
    }

    /**
     * Check if MAP apply to all oggettos
     *
     * @return bool
     */
    public function isMsrpApplyToAll()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_MSRP_APPLY_TO_ALL, $this->_storeId);
    }

    /**
     * Return MAP explanation message
     *
     * @return string
     */
    public function getMsrpExplanationMessage()
    {
        return $this->escapeHtml(
            Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Return MAP explanation message for "Whats This" window
     *
     * @return string
     */
    public function getMsrpExplanationMessageWhatsThis()
    {
        return $this->escapeHtml(
            Mage::getStoreConfig(self::XML_PATH_MSRP_EXPLANATION_MESSAGE_WHATS_THIS, $this->_storeId),
            array('b','br','strong','i','u', 'p', 'span')
        );
    }

    /**
     * Check if can apply Minimum Advertise price to oggetto
     * in specific visibility
     *
     * @param int|Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int $visibility Check displaying price in concrete place (by default generally)
     * @param bool $checkAssociatedItems
     * @return bool
     */
    public function canApplyMsrp($oggetto, $visibility = null, $checkAssociatedItems = true)
    {
        if (!$this->isMsrpEnabled()) {
            return false;
        }

        if (is_numeric($oggetto)) {
            $oggetto = Mage::getModel('score/oggetto')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($oggetto);
        }

        if (!$this->canApplyMsrpToOggettoType($oggetto)) {
            return false;
        }

        $result = $oggetto->getMsrpEnabled();
        if ($result == Shaurmalab_Score_Model_Oggetto_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG) {
            $result = $this->isMsrpApplyToAll();
        }

        if (!$oggetto->hasMsrpEnabled() && $this->isMsrpApplyToAll()) {
            $result = true;
        }

        if ($result && $visibility !== null) {
            $oggettoVisibility = $oggetto->getMsrpDisplayActualPriceType();
            if ($oggettoVisibility == Shaurmalab_Score_Model_Oggetto_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG) {
                $oggettoVisibility = $this->getMsrpDisplayActualPriceType();
            }
            $result = ($oggettoVisibility == $visibility);
        }

        if ($oggetto->getTypeInstance(true)->isComposite($oggetto)
            && $checkAssociatedItems
            && (!$result || $visibility !== null)
        ) {
            $resultInOptions = $oggetto->getTypeInstance(true)->isMapEnabledInOptions($oggetto, $visibility);
            if ($resultInOptions !== null) {
                $result = $resultInOptions;
            }
        }

        return $result;
    }

    /**
     * Check whether MAP applied to oggetto Oggetto Type
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function canApplyMsrpToOggettoType($oggetto)
    {
        if($this->_mapApplyToOggettoType === null) {
            /** @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
            $attribute = Mage::getModel('score/resource_eav_attribute')
                ->loadByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'msrp_enabled');
            $this->_mapApplyToOggettoType = $attribute->getApplyTo();
        }
        return empty($this->_mapApplyToOggettoType) || in_array($oggetto->getTypeId(), $this->_mapApplyToOggettoType);
    }

    /**
     * Get MAP message for price
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getMsrpPriceMessage($oggetto)
    {
        $message = "";
        if ($this->canApplyMsrp(
            $oggetto,
            Shaurmalab_Score_Model_Oggetto_Attribute_Source_Msrp_Type::TYPE_IN_CART
        )) {
            $message = $this->__('To see oggetto price, add this item to your cart. You can always remove it later.');
        } elseif ($this->canApplyMsrp(
            $oggetto,
            Shaurmalab_Score_Model_Oggetto_Attribute_Source_Msrp_Type::TYPE_BEFORE_ORDER_CONFIRM
        )) {
            $message = $this->__('See price before order confirmation.');
        }
        return $message;
    }

    /**
     * Check is oggetto need gesture to show price
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function isShowPriceOnGesture($oggetto)
    {
        return $this->canApplyMsrp(
            $oggetto,
            Shaurmalab_Score_Model_Oggetto_Attribute_Source_Msrp_Type::TYPE_ON_GESTURE
        );
    }

    /**
     * Whether to display items count for each filter option
     * @param int $storeId Store view ID
     * @return bool
     */
    public function shouldDisplayOggettoCountOnLayer($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DISPLAY_OGGETTO_COUNT, $storeId);
    }

    public function getLikeArray($attribute,$value) {
        return array(
            array(
                'attribute' => $attribute,
                'like'=>'%,'.$value.',%'
            ),
            array(
                'attribute' => $attribute,
                'like'=>'%,'.$value
            ),
            array(
                'attribute' => $attribute,
                'like'=>$value.',%'
            ),
            array(
                'attribute' => $attribute,
                'eq'=>$value
            )
        );


    }
  public function getLikeOrEmptyArray($attribute,$value) {
        return array(
            array(
                'attribute' => $attribute,
                'like'=>'%,'.$value.',%'
            ),
            array(
                'attribute' => $attribute,
                'like'=>'%,'.$value
            ),
            array(
                'attribute' => $attribute,
                'like'=>$value.',%'
            ),
            array(
                'attribute' => $attribute,
                'eq'=>$value
            ),
            array(
                'attribute' => $attribute,
                'eq'=>''
            ),
            array(
                'attribute' => $attribute,
                'null'=>true
            )
        );


    }

    public function getSetCode($attributeSet) { 
            if(!$attributeSet->getIdentifier()) return strtolower(str_replace(' ', '', $attributeSet->getAttributeSetName()));
            return $attributeSet->getIdentifier();
    }


    public function checkAntiForgeryToken() {
        if (!($formKey =  Mage::app()->getRequest()->getParam('form_key', null))
            || $formKey != Mage::getSingleton('core/session')->getFormKey()) {
            return false;
        }
        return true;
    }

     /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  mixed
     */
    public static function currency($value)
    {
        return Mage::helper('core')->formatPrice($value,false);
    }

    public function isDmEnabled() { 
        return true;
    }

    public function getDirectMessages() { 
        $messages = array();
        $setId = Mage::helper('score/oggetto')->getSetIdByCode('Dm');
        $collection = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id', $setId)
            // ->addAttributeToFilter('to',Mage::getSingleton('customer/session')->getCustomerId())
            // ->addAttributeToSelect('from')
            // ->addAttributeToSelect('text')
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')

             ->setPageSize(5)
             ->setCurPage(1)
        ;
        $messages = $collection;      

        return $messages;
    }

    public function sendMailByCode($email_code, $entity, $customer,$additionalData = array()) {
        try {
            $translate = Mage::getSingleton('core/translate');
            $email = Mage::getModel('core/email_template');
            $template = Mage::getModel('core/email_template')->loadByCode($email_code)->getTemplateId();

            $sender = array(
                'name' => Mage::getStoreConfig('trans_email/ident_support/name', Mage::app()->getStore()->getId()),
                'email' => Mage::getStoreConfig('trans_email/ident_support/email', Mage::app()->getStore()->getId()),
            );

            $customerName = $customer->getFirstname() . " " . $customer->getLastname();
            $customerEmail = $customer->getEmail();

            $vars = array('entity' => $entity, 'customer' => $customer, 'data' => $additionalData);

            $storeId = Mage::app()->getStore()->getId();

            $translate = Mage::getSingleton('core/translate');
            Mage::getModel('core/email_template')
                ->sendTransactional($template, $sender, $customerEmail, $customerName, $vars, $storeId);
            $translate->setTranslateInline(true);
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            Mage::log($e->getMessage(), null, 'fail-emails.log');
            return false;
        }
    }

    public function filterDates($array, $format = null) {
        if(!$format) $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT;
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat($format),
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));

        foreach ($array as $k=>$dateField) {
                $array[$k] = $filterInput->setOptions(array('locale'=>Mage::registry('slang')))->filter($array[$k]);
                $array[$k] = $filterInternal->filter($array[$k]);
        }
        return $array;
    }
}