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
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Oggetto search result block
 *
 * @category   Mage
 * @package    Shaurmalab_ScoreSearch
 * @module     Catalog
 */
class Shaurmalab_ScoreSearch_Block_Result extends Mage_Core_Block_Template
{
    /**
     * Score Oggetto collection
     *
     * @var Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Collection
     */
    protected $_oggettoCollection;

    /**
     * Retrieve query model object
     *
     * @return Shaurmalab_ScoreSearch_Model_Query
     */
    protected function _getQuery()
    {
        return $this->helper('scoresearch')->getQuery();
    }

    /**
     * Prepare layout
     *
     * @return Shaurmalab_ScoreSearch_Block_Result
     */
    protected function _prepareLayout()
    {
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = $this->__("Search results for: '%s'", $this->helper('scoresearch')->getQueryText());

            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label' => $title,
                'title' => $title
            ));
        }

        // modify page title
        $title = $this->__("Search results for: '%s'", $this->helper('scoresearch')->getEscapedQueryText());
        $this->getLayout()->getBlock('head')->setTitle($title);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return Mage_Catalog_Block_Product_List
     */
    public function getListBlock()
    {
        return $this->getChild('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return Shaurmalab_ScoreSearch_Block_Result
     */
    public function setListOrders()
    {
        $category = Mage::getSingleton('score/layer')
            ->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'relevance' => $this->__('Relevance')
        ), $availableOrders);

        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return Shaurmalab_ScoreSearch_Block_Result
     */
    public function setListModes()
    {
        $this->getListBlock()
            ->setModes(array(
                'grid' => $this->__('Grid'),
                'list' => $this->__('List'))
            );
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return Shaurmalab_ScoreSearch_Block_Result
     */
    public function setListCollection()
    {
//        $this->getListBlock()
//           ->setCollection($this->_getOggettoCollection());
       return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getOggettoListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Collection
     */
    protected function _getOggettoCollection()
    {
        if (is_null($this->_oggettoCollection)) {
            $this->_oggettoCollection = $this->getListBlock()->getLoadedOggettoCollection();
            $this->_oggettoCollection->addAttributeToFilter('is_public',array(1,"1"));
            $this->getListBlock()->setCollection($this->_oggettoCollection);
            //TODO: create a separate function to define if oggetto is visible.
        }
        return $this->_oggettoCollection;
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getOggettoCollection()->getSize();
            $this->_getQuery()->setNumResults($size);
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        if (Mage::helper('scoresearch')->isMinQueryLength()) {
            return Mage::helper('scoresearch')->__('Minimum Search query length is %s', $this->_getQuery()->getMinQueryLength());
        }
        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return Mage::helper('scoresearch')->getNoteMessages();
    }
}
