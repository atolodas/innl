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
 * Oggetto View block
 *
 * @category Mage
 * @package  Shaurmalab_Score
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_View extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Add meta information from oggetto to head block
     *
     * @return Shaurmalab_Score_Block_Oggetto_View
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->createBlock('score/breadcrumbs');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $oggetto = $this->getOggetto();
            $title = $oggetto->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
            $keyword = $oggetto->getMetaKeyword();
            $currentCategory = Mage::registry('current_category');
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } elseif ($currentCategory) {
                $headBlock->setKeywords($oggetto->getName());
            }
            $description = $oggetto->getMetaDescription();
            if ($description) {
                $headBlock->setDescription( ($description) );
            } else {
                $headBlock->setDescription(Mage::helper('core/string')->substr($oggetto->getDescription(), 0, 255));
            }
            if ($this->helper('score/oggetto')->canUseCanonicalTag()) {
                $params = array('_ignore_category' => true);
                $headBlock->addLinkRel('canonical', $oggetto->getUrlModel()->getUrl($oggetto, $params));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve current oggetto model
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!Mage::registry('oggetto') && $this->getOggettoId()) {
            $oggetto = Mage::getModel('score/oggetto')->load($this->getOggettoId());
            Mage::register('oggetto', $oggetto);
        }
        return Mage::registry('oggetto');
    }

    /**
     * Check if oggetto can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend()
    {
        $sendToFriendModel = Mage::registry('send_to_friend_model');
        return $sendToFriendModel && $sendToFriendModel->canEmailToFriend();
    }

    /**
     * Retrieve url for direct adding oggetto to cart
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($oggetto, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($oggetto, $additional);
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on oggetto options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();
        if (!$this->hasOptions()) {
            return Mage::helper('core')->jsonEncode($config);
        }

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = $this->getOggetto();
        $_request->setOggettoClassId($oggetto->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setOggettoClassId($oggetto->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $oggetto->getPrice();
        $_finalPrice = $oggetto->getFinalPrice();
        $_priceInclTax = Mage::helper('tax')->getPrice($oggetto, $_finalPrice, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($oggetto, $_finalPrice);
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($oggetto->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($oggetto, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'oggettoId'           => $oggetto->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'oggettoPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'oggettoOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('score_oggetto_view_config', array('response_object' => $responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Return true if oggetto has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getOggetto()->getTypeInstance(true)->hasOptions($this->getOggetto())) {
            return true;
        }
        return false;
    }

    /**
     * Check if oggetto has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getOggetto()->getTypeInstance(true)->hasRequiredOptions($this->getOggetto());
    }

    /**
     * Define if setting of oggetto options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     */
    public function isStartCustomization()
    {
        return $this->getOggetto()->getConfigureMode() || Mage::app()->getRequest()->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|Shaurmalab_Score_Model_Oggetto $oggetto
     * @return int|float
     */
    public function getOggettoDefaultQty($oggetto = null)
    {
        if (!$oggetto) {
            $oggetto = $this->getOggetto();
        }

        $qty = $this->getMinimalQty($oggetto);
        $config = $oggetto->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getOggetto()->getCacheIdTags());
    }
}
