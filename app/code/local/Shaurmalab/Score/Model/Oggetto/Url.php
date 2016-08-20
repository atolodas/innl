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
 * Oggetto Url model
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Url extends Varien_Object
{
    const CACHE_TAG = 'url_rewrite';

    /**
     * URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected  $_url;

    /**
     * URL Rewrite Instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Factory instance
     *
     * @var Shaurmalab_Score_Model_Factory
     */
    protected $_factory;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Initialize Url model
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('score/factory');
        $this->_store = !empty($args['store']) ? $args['store'] : Mage::app()->getStore();
    }

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (null === $this->_url) {
            $this->_url = Mage::getModel('core/url');
        }
        return $this->_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (null === $this->_urlRewrite) {
            $this->_urlRewrite = $this->_factory->getUrlRewriteInstance();
        }
        return $this->_urlRewrite;
    }

    /**
     * 'no_selection' shouldn't be a valid image attribute value
     *
     * @param string $image
     * @return string
     */
    protected function _validImage($image)
    {
        if($image == 'no_selection') {
            $image = null;
        }
        return $image;
    }

    /**
     * Retrieve URL in current store
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $params the URL route params
     * @return string
     */
    public function getUrlInStore(Shaurmalab_Score_Model_Oggetto $oggetto, $params = array())
    {
        $params['_store_to_url'] = true;
        return $this->getUrl($oggetto, $params);
    }

    /**
     * Retrieve Oggetto URL
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @param  bool $useSid forced SID mode
     * @return string
     */
    public function getOggettoUrl($oggetto, $useSid = null)
    {
        if ($useSid === null) {
            $useSid = Mage::app()->getUseSessionInUrl();
        }

        $params = array();
        if (!$useSid) {
            $params['_nosid'] = true;
        }

        return $this->getUrl($oggetto, $params);
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        if($str) {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('score/oggetto_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
        } else { return ''; }
    }

    /**
     * Retrieve Oggetto Url path (with category if exists)
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Shaurmalab_Score_Model_Category $category
     *
     * @return string
     */
    public function getUrlPath($oggetto, $category=null)
    {
        $path = $oggetto->getData('url_path');

        //if (is_null($category)) {
            /** @todo get default category */
            return $path;
        //} elseif (!$category instanceof Shaurmalab_Score_Model_Category) {
        //    Mage::throwException('Invalid category object supplied');
        //}

        //return Mage::helper('score/category')->getCategoryUrlPath($category->getUrlPath())
        //    . '/' . $path;
    }

    /**
     * Retrieve Oggetto URL using UrlDataObject
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $params
     * @return string
     */
    public function getUrl(Shaurmalab_Score_Model_Oggetto $oggetto, $params = array())
    {
        $url = $oggetto->getData('url');
        if (!empty($url)) {
            return $url;
        }

        $requestPath = $oggetto->getData('request_path');
        if (empty($requestPath)) {
            $requestPath = $this->_getRequestPath($oggetto, $this->_getCategoryIdForUrl($oggetto, $params));
            $oggetto->setRequestPath($requestPath);
        }

        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $oggetto->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            $params['_store_to_url'] = true;
        }

        // reset cached URL instance GET query params
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }

        $this->getUrlInstance()->setStore($storeId);
        $oggettoUrl = $this->_getOggettoUrl($oggetto, $requestPath, $params);
        $oggetto->setData('url', $oggettoUrl);
        return $oggetto->getData('url');
    }

    /**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }

    /**
     * Check oggetto category
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $params
     *
     * @return int|null
     */
    protected function _getCategoryIdForUrl($oggetto, $params)
    {
        //if (isset($params['_ignore_category'])) {
            return null;
        //} else {
        //    return $oggetto->getCategoryId() && !$oggetto->getDoNotUseCategoryId()
        //        ? $oggetto->getCategoryId() : null;
        //}
    }

    /**
     * Retrieve oggetto URL based on requestPath param
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param string $requestPath
     * @param array $routeParams
     *
     * @return string
     */
    protected function _getOggettoUrl($oggetto, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }
        $routeParams['id'] = $oggetto->getId();
        $routeParams['s'] = $oggetto->getUrlKey();
        $categoryId = $this->_getCategoryIdForUrl($oggetto, $routeParams);
        if ($categoryId) {
            $routeParams['category'] = $categoryId;
        }
        return $this->getUrlInstance()->getUrl('score/oggetto/view', $routeParams);
    }

    /**
     * Retrieve request path
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int $categoryId
     * @return bool|string
     */
    protected function _getRequestPath($oggetto, $categoryId)
    {
        $idPath = sprintf('oggetto/%d', $oggetto->getEntityId());
        //if ($categoryId) {
        //    $idPath = sprintf('%s/%d', $idPath, $categoryId);
        //}
        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($oggetto->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }
}
