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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * List of oggettos scoretagged by customer Block
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Block_Customer_View extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    /**
     * Scoretagged Oggetto Collection
     *
     * @var Mage_Scoretag_Model_Mysql4_Oggetto_Collection
     */
    protected $_collection;

    /**
     * Current Scoretag object
     *
     * @var Mage_Scoretag_Model_Scoretag
     */
    protected $_scoretagInfo;

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setScoretagId(Mage::registry('scoretagId'));
    }

    /**
     * Retrieve current Scoretag object
     *
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function getScoretagInfo()
    {
        if (is_null($this->_scoretagInfo)) {
            $this->_scoretagInfo = Mage::getModel('scoretag/scoretag')
                ->load($this->getScoretagId());
        }
        return $this->_scoretagInfo;
    }

    /**
     * Retrieve Scoretagged Oggetto Collection items
     *
     * @return array
     */
    public function getMyOggettos()
    {
        return $this->_getCollection()->getItems();
    }

    /**
     * Retrieve count of Scoretagged Oggetto(s)
     *
     * @return int
     */
    public function getCount()
    {
        return sizeof($this->getMyOggettos());
    }

    /**
     * Retrieve Oggetto Info URL
     *
     * @param int $oggettoId
     * @return string
     */
    public function getReviewUrl($oggettoId)
    {
        return Mage::getUrl('review/oggetto/list', array('id' => $oggettoId));
    }

    /**
     * Preparing block layout
     *
     * @return Mage_Scoretag_Block_Customer_View
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()
            ->createBlock('page/html_pager', 'customer_scoretag_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Retrieve Current Mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve Scoretagged oggetto(s) collection
     *
     * @return Mage_Scoretag_Model_Mysql4_Oggetto_Collection
     */
    protected function _getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_collection = Mage::getModel('scoretag/scoretag')
                ->getEntityCollection()
                ->addScoretagFilter($this->getScoretagId())
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToSelect(Mage::getSingleton('score/config')->getOggettoAttributes())
                ->setActiveFilter();

            Mage::getSingleton('score/oggetto_status')
                ->addVisibleFilterToCollection($this->_collection);
            Mage::getSingleton('score/oggetto_visibility')
                ->addVisibleInSiteFilterToCollection($this->_collection);
        }
        return $this->_collection;
    }
}
