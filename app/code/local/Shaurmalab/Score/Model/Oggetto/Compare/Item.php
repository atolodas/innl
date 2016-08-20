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
 * Score Compare Item Model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Compare_Item _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Compare_Item getResource()
 * @method Shaurmalab_Score_Model_Oggetto_Compare_Item setVisitorId(int $value)
 * @method Shaurmalab_Score_Model_Oggetto_Compare_Item setCustomerId(int $value)
 * @method int getOggettoId()
 * @method Shaurmalab_Score_Model_Oggetto_Compare_Item setOggettoId(int $value)
 * @method int getStoreId()
 * @method Shaurmalab_Score_Model_Oggetto_Compare_Item setStoreId(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Compare_Item extends Mage_Core_Model_Abstract
{
    /**
     * Model cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'score_compare_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'score_compare_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getItem() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Initialize resourse model
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_compare_item');
    }

    /**
     * Retrieve Resource instance
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set current store before save
     *
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->hasStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        return $this;
    }

    /**
     * Add customer data from customer object
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function addCustomerData(Mage_Customer_Model_Customer $customer)
    {
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Set visitor
     *
     * @param int $visitorId
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function addVisitorId($visitorId)
    {
        $this->setVisitorId($visitorId);
        return $this;
    }

    /**
     * Load compare item by oggetto
     *
     * @param mixed $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function loadByOggetto($oggetto)
    {
        $this->_getResource()->loadByOggetto($this, $oggetto);
        return $this;
    }

    /**
     * Set oggetto data
     *
     * @param mixed $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function addOggettoData($oggetto)
    {
        if ($oggetto instanceof Shaurmalab_Score_Model_Oggetto) {
            $this->setOggettoId($oggetto->getId());
        }
        else if(intval($oggetto)) {
            $this->setOggettoId(intval($oggetto));
        }

        return $this;
    }

    /**
     * Retrieve data for save
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = array();
        $data['customer_id'] = $this->getCustomerId();
        $data['visitor_id']  = $this->getVisitorId();
        $data['oggetto_id']  = $this->getOggettoId();

        return $data;
    }

    /**
     * Customer login bind process
     *
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function bindCustomerLogin()
    {
        //$this->_getResource()->updateCustomerFromVisitor($this);

        Mage::helper('score/oggetto_compare')->setCustomerId($this->getCustomerId())->calculate();
        return $this;
    }

    /**
     * Customer logout bind process
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function bindCustomerLogout(Varien_Event_Observer $observer = null)
    {
        return $this;
    }

    /**
     * Clean compare items
     *
     * @return Shaurmalab_Score_Model_Oggetto_Compare_Item
     */
    public function clean()
    {
        $this->_getResource()->clean($this);
        return $this;
    }

    /**
     * Retrieve Customer Id if loggined
     *
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->hasData('customer_id')) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $this->setData('customer_id', $customerId);
        }
        return $this->getData('customer_id');
    }

    /**
     * Retrieve Visitor Id
     *
     * @return int
     */
    public function getVisitorId()
    {
        if (!$this->hasData('visitor_id')) {
            $visitorId = Mage::getSingleton('log/visitor')->getId();
            $this->setData('visitor_id', $visitorId);
        }
        return $this->getData('visitor_id');
    }
}
