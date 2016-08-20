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
 * Score Oggetto Abstract Block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_Score_Block_Oggetto_Abstract extends Mage_Core_Block_Template
{
    /**
     * Price block array
     *
     * @var array
     */
    protected $_priceBlock = array();

    /**
     * Default price block
     *
     * @var string
     */
    protected $_block = 'score/oggetto_price';

    /**
     * Price template
     *
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'score/oggetto/price.phtml';

    /**
     * Tier price template
     *
     * @var string
     */
    protected $_tierPriceDefaultTemplate  = 'score/oggetto/view/tierprices.phtml';

    /**
     * Price types
     *
     * @var array
     */
    protected $_priceBlockTypes = array();

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Review block instance
     *
     * @var null|Mage_Review_Block_Helper
     */
    protected $_reviewsHelperBlock;

    /**
     * Default oggetto amount per row
     *
     * @var int
     */
    protected $_defaultColumnCount = 3;

    /**
     * Oggetto amount per row depending on custom page layout of category
     *
     * @var array
     */
    protected $_columnCountLayoutDepend = array();

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp';

    /**
     * Retrieve url for add oggetto to cart
     * Will return oggetto view page URL if oggetto has required options
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($oggetto, $additional = array())
    {
        if (!$oggetto->getTypeInstance(true)->hasRequiredOptions($oggetto)) {
            return $this->helper('checkout/cart')->getAddUrl($oggetto, $additional);
        }
        $additional = array_merge(
            $additional,
            array(Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey())
        );
        if (!isset($additional['_escape'])) {
            $additional['_escape'] = true;
        }
        if (!isset($additional['_query'])) {
            $additional['_query'] = array();
        }
        $additional['_query']['options'] = 'cart';
        return $this->getOggettoUrl($oggetto, $additional);
    }

    /**
     * Return model instance
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($className, $arguments = array())
    {
        return Mage::getSingleton($className, $arguments);
    }

    /**
     * Retrieves url for form submitting:
     * some objects can use setSubmitRouteData() to set route and params for form submitting,
     * otherwise default url will be used
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $additional
     * @return string
     */
    public function getSubmitUrl($oggetto, $additional = array())
    {
        $submitRouteData = $this->getData('submit_route_data');
        if ($submitRouteData) {
            $route = $submitRouteData['route'];
            $params = isset($submitRouteData['params']) ? $submitRouteData['params'] : array();
            $submitUrl = $this->getUrl($route, array_merge($params, $additional));
        } else {
            $submitUrl = $this->getAddToCartUrl($oggetto, $additional);
        }
        return $submitUrl;
    }

    /**
     * Return link to Add to Wishlist
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getAddToWishlistUrl($oggetto)
    {
        return $this->helper('wishlist')->getAddUrl($oggetto);
    }

    /**
     * Retrieve Add Oggetto to Compare Oggettos List URL
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getAddToCompareUrl($oggetto)
    {
        return $this->helper('score/oggetto_compare')->getAddUrl($oggetto);
    }

    /**
     * Gets minimal sales quantity
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return int|null
     */
    public function getMinimalQty($oggetto)
    {
        $stockItem = $oggetto->getStockItem();
        if ($stockItem) {
            return ($stockItem->getMinSaleQty()
                && $stockItem->getMinSaleQty() > 0 ? $stockItem->getMinSaleQty() * 1 : null);
        }
        return null;
    }

    /**
     * Return price block
     *
     * @param string $oggettoTypeId
     * @return mixed
     */
    protected function _getPriceBlock($oggettoTypeId)
    {
        if (!isset($this->_priceBlock[$oggettoTypeId])) {
            $block = $this->_block;
            if (isset($this->_priceBlockTypes[$oggettoTypeId])) {
                if ($this->_priceBlockTypes[$oggettoTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$oggettoTypeId]['block'];
                }
            }
            $this->_priceBlock[$oggettoTypeId] = $this->getLayout()->createBlock($block);
        }
        return $this->_priceBlock[$oggettoTypeId];
    }

    /**
     * Return Block template
     *
     * @param string $oggettoTypeId
     * @return string
     */
    protected function _getPriceBlockTemplate($oggettoTypeId)
    {
        if (isset($this->_priceBlockTypes[$oggettoTypeId])) {
            if ($this->_priceBlockTypes[$oggettoTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$oggettoTypeId]['template'];
            }
        }
        return $this->_priceBlockDefaultTemplate;
    }


    /**
     * Prepares and returns block to render some oggetto type
     *
     * @param string $oggettoType
     * @return Mage_Core_Block_Template
     */
    public function _preparePriceRenderer($oggettoType)
    {
        return $this->_getPriceBlock($oggettoType)
            ->setTemplate($this->_getPriceBlockTemplate($oggettoType))
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs);
    }

    /**
     * Returns oggetto price block html
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param boolean $displayMinimalPrice
     * @param string $idSuffix
     * @return string
     */
    public function getPriceHtml($oggetto, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $oggetto->getTypeId();
        if (Mage::helper('score')->canApplyMsrp($oggetto)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setOggetto($oggetto)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->toHtml();
            $oggetto->setAddToCartUrl($this->getAddToCartUrl($oggetto));
            $oggetto->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        return $this->_preparePriceRenderer($type_id)
            ->setOggetto($oggetto)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    /**
     * Adding customized price template for oggetto type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }

    /**
     * Get oggetto reviews summary
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(Shaurmalab_Score_Model_Oggetto $oggetto, $templateType = false,
        $displayIfNoReviews = false)
    {
        if ($this->_initReviewsHelperBlock()) {
            return $this->_reviewsHelperBlock->getSummaryHtml($oggetto, $templateType, $displayIfNoReviews);
        }

        return '';
    }

    /**
     * Add/replace reviews summary template by type
     *
     * @param string $type
     * @param string $template
     * @return string
     */
    public function addReviewSummaryTemplate($type, $template)
    {
        if ($this->_initReviewsHelperBlock()) {
            $this->_reviewsHelperBlock->addTemplate($type, $template);
        }

        return '';
    }

    /**
     * Create reviews summary helper block once
     *
     * @return boolean
     */
    protected function _initReviewsHelperBlock()
    {
        if (!$this->_reviewsHelperBlock) {
            if (!Mage::helper('score')->isModuleEnabled('Mage_Review')) {
                return false;
            } else {
                $this->_reviewsHelperBlock = $this->getLayout()->createBlock('review/helper');
            }
        }

        return true;
    }

    /**
     * Retrieve currently viewed oggetto object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!$this->hasData('oggetto')) {
            $this->setData('oggetto', Mage::registry('oggetto'));
        }
        return $this->getData('oggetto');
    }

    /**
     * Return tier price template
     *
     * @return mixed|string
     */
    public function getTierPriceTemplate()
    {
        if (!$this->hasData('tier_price_template')) {
            return $this->_tierPriceDefaultTemplate;
        }

        return $this->getData('tier_price_template');
    }
    /**
     * Returns oggetto tier price block html
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getTierPriceHtml($oggetto = null)
    {
        if (is_null($oggetto)) {
            $oggetto = $this->getOggetto();
        }
        return $this->_getPriceBlock($oggetto->getTypeId())
            ->setTemplate($this->getTierPriceTemplate())
            ->setOggetto($oggetto)
            ->setInGrouped($this->getOggetto()->isGrouped())
            ->toHtml();
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getTierPrices($oggetto = null)
    {
        if (is_null($oggetto)) {
            $oggetto = $this->getOggetto();
        }
        $prices  = $oggetto->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $_oggettoPrice = $oggetto->getPrice();
                if ($_oggettoPrice != $oggetto->getFinalPrice()) {
                    $_oggettoPrice = $oggetto->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $oggetto->getGroupPrice();
                if ($_oggettoPrice > $groupPrice) {
                    $_oggettoPrice = $groupPrice;
                }

                if ($price['price'] < $_oggettoPrice) {
                    $price['savePercent'] = ceil(100 - ((100 / $_oggettoPrice) * $price['price']));

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($oggetto, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('tax')->getPrice($oggetto, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('score')->canApplyMsrp($oggetto)) {
                        $oldPrice = $oggetto->getFinalPrice();
                        $oggetto->setPriceCalculation(false);
                        $oggetto->setPrice($tierPrice);
                        $oggetto->setFinalPrice($tierPrice);

                        $this->getPriceHtml($oggetto);
                        $oggetto->setPriceCalculation(true);

                        $price['real_price_html'] = $oggetto->getRealPriceHtml();
                        $oggetto->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }

    /**
     * Add all attributes and apply pricing logic to oggettos collection
     * to get correct values in different oggettos lists.
     * E.g. crosssells, upsells, new oggettos, recently viewed
     *
     * @param Shaurmalab_Score_Model_Resource_Oggetto_Collection $collection
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _addOggettoAttributesAndPrices(Shaurmalab_Score_Model_Resource_Oggetto_Collection $collection)
    {
        return $collection
            //->addMinimalPrice()
            //->addFinalPrice()
            //->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('score/config')->getOggettoAttributes())
            ->addUrlRewrite();
    }

    /**
     * Retrieve given media attribute label or oggetto name if no label
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param string $mediaAttributeCode
     *
     * @return string
     */
    public function getImageLabel($oggetto = null, $mediaAttributeCode = 'image')
    {
        if (is_null($oggetto)) {
            $oggetto = $this->getOggetto();
        }

        $label = $oggetto->getData($mediaAttributeCode . '_label');
        if (empty($label)) {
            $label = $oggetto->getName();
        }

        return $label;
    }

    /**
     * Retrieve Oggetto URL using UrlDataObject
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $additional the route params
     * @return string
     */
    public function getOggettoUrl($oggetto, $additional = array())
    {
        if ($this->hasOggettoUrl($oggetto)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $oggetto->getUrlModel()->getUrl($oggetto, $additional);
        }
        return '#';
    }

    /**
     * Check Oggetto has URL
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     *
     */
    public function hasOggettoUrl($oggetto)
    {
        if ($oggetto->getVisibleInSiteVisibilities()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve oggetto amount per row
     *
     * @return int
     */
    public function getColumnCount()
    {
        if (!$this->_getData('column_count')) {
            $pageLayout = $this->getPageLayout();
            if ($pageLayout && $this->getColumnCountLayoutDepend($pageLayout->getCode())) {
                $this->setData(
                    'column_count',
                    $this->getColumnCountLayoutDepend($pageLayout->getCode())
                );
            } else {
                $this->setData('column_count', $this->_defaultColumnCount);
            }
        }

        return (int) $this->_getData('column_count');
    }

    /**
     * Add row size depends on page layout
     *
     * @param string $pageLayout
     * @param int $columnCount
     * @return Shaurmalab_Score_Block_Oggetto_List
     */
    public function addColumnCountLayoutDepend($pageLayout, $columnCount)
    {
        $this->_columnCountLayoutDepend[$pageLayout] = $columnCount;
        return $this;
    }

    /**
     * Remove row size depends on page layout
     *
     * @param string $pageLayout
     * @return Shaurmalab_Score_Block_Oggetto_List
     */
    public function removeColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            unset($this->_columnCountLayoutDepend[$pageLayout]);
        }

        return $this;
    }

    /**
     * Retrieve row size depends on page layout
     *
     * @param string $pageLayout
     * @return int|boolean
     */
    public function getColumnCountLayoutDepend($pageLayout)
    {
        if (isset($this->_columnCountLayoutDepend[$pageLayout])) {
            return $this->_columnCountLayoutDepend[$pageLayout];
        }

        return false;
    }

    /**
     * Retrieve current page layout
     *
     * @return Varien_Object
     */
    public function getPageLayout()
    {
        return $this->helper('page/layout')->getCurrentPageLayout();
    }

    /**
     * Check whether the price can be shown for the specified oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function getCanShowOggettoPrice($oggetto)
    {
        return $oggetto->getCanShowPrice() !== false;
    }

    /**
     * Get if it is necessary to show oggetto stock status
     *
     * @return bool
     */
    public function displayOggettoStockStatus()
    {
        $statusInfo = new Varien_Object(array('display_status' => true));
        Mage::dispatchEvent('score_block_oggetto_status_display', array('status' => $statusInfo));
        return (boolean)$statusInfo->getDisplayStatus();
    }

    /**
     * If exists price template block, retrieve price blocks from it
     *
     * @return Shaurmalab_Score_Block_Oggetto_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /* @var $block Shaurmalab_Score_Block_Oggetto_Price_Template */
        $block = $this->getLayout()->getBlock('score_oggetto_price_template');
        if ($block) {
            foreach ($block->getPriceBlockTypes() as $type => $priceBlock) {
                $this->addPriceBlockType($type, $priceBlock['block'], $priceBlock['template']);
            }
        }

        return $this;
    }
}
