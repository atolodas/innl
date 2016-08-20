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
 * @package     Shaurmalab_ScoreIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index data retreiver factory
 *
 * @method Shaurmalab_ScoreIndex_Model_Resource_Retreiver _getResource()
 * @method Shaurmalab_ScoreIndex_Model_Resource_Retreiver getResource()
 * @method int getEntityTypeId()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setTypeId(string $value)
 * @method string getSku()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setSku(string $value)
 * @method int getHasOptions()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Shaurmalab_ScoreIndex_Model_Retreiver setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Retreiver extends Mage_Core_Model_Abstract
{
    const CHILDREN_FOR_TIERS = 1;
    const CHILDREN_FOR_PRICES = 2;
    const CHILDREN_FOR_ATTRIBUTES = 3;

    protected $_attributeIdCache = array();

    /**
     * Customer group cache
     *
     * @var Mage_Customer_Model_Mysql4_Group_Collection
     */
    protected $_customerGroups;

    /**
     * Retreiver model names cache
     *
     * @var array
     */
    protected $_retreivers = array();

    /**
     * Retreiver factory init, load retreiver settings
     *
     */
    protected function _construct()
    {
        $config = Mage::getConfig()->getNode('global/score/oggetto/type')->asArray();
        foreach ($config as $type=>$data) {
            if (isset($data['index_data_retreiver'])) {
                $this->_retreivers[$type] = $data['index_data_retreiver'];
            }
        }

        $this->_init('scoreindex/retreiver');
    }

    /**
     * Returns data retreiver model by specified oggetto type
     *
     * @param string $type
     * @return Shaurmalab_ScoreIndex_Model_Data_Abstract
     */
    public function getRetreiver($type)
    {
        if (isset($this->_retreivers[$type])) {
            return Mage::getSingleton($this->_retreivers[$type]);
        } else {
            Mage::throwException("Data retreiver for '{$type}' is not defined");
        }
    }

    /**
     * Return customer group collection
     *
     * @return Mage_Customer_Model_Entity_Group_Collection
     */
    public function getCustomerGroups()
    {
        if (is_null($this->_customerGroups)) {
            $this->_customerGroups = Mage::getModel('customer/group')->getCollection();
        }
        return $this->_customerGroups;
    }

    /**
     * Return oggetto ids sorted by type
     *
     * @param array $oggettos
     * @return array
     */
    public function assignOggettoTypes($oggettos)
    {
        $flat = $this->_getResource()->getOggettoTypes($oggettos);
        $result = array();
        foreach ($flat as $one) {
            $result[$one['type']][] = $one['id'];
        }
        return $result;
    }
}
