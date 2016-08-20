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
 * Resource model ScoreIndex Data Abstract
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Resource_Data_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Attribute id by code cache
     *
     * @var array
     */
    protected $_attributeCodeIds     = array();

    /**
     * Link select object
     *
     * @var Zend_Db_Select
     */
    protected $_linkSelect           = null;

    /**
     * Set link select
     *
     * @param Zend_Db_Select $select
     * @return Shaurmalab_ScoreIndex_Model_Resource_Data_Abstract
     */
    protected function _setLinkSelect($select)
    {
        $this->_linkSelect = $select;
        return $this;
    }

    /**
     * Get link select
     *
     * @return Zend_Db_Select $select
     */
    protected function _getLinkSelect()
    {
        return $this->_linkSelect;
    }

    /**
     * Init resource
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto', 'entity_id');
    }

    /**
     * Retreive specified attribute data for specified oggettos from specified store
     *
     * @param array $oggettos
     * @param array $attributes
     * @param int $store
     * @return unknown
     */
    public function getAttributeData($oggettos, $attributes, $store)
    {
        $suffixes = array('decimal', 'varchar', 'int', 'text', 'datetime');
        if (!is_array($oggettos)) {
            $oggettos = new Zend_Db_Expr($oggettos);
        }
        $result = array();
        foreach ($suffixes as $suffix) {
            $tableName = "{$this->getTable('score/oggetto')}_{$suffix}";
            $condition = "oggetto.entity_id = c.entity_id AND c.store_id = {$store} AND c.attribute_id = d.attribute_id";
            $defaultCondition = "oggetto.entity_id = d.entity_id AND d.store_id = 0";
            $fields = array(
                'entity_id',
                'type_id',
                'attribute_id'  => 'IF(c.value_id > 0, c.attribute_id, d.attribute_id)',
                'value'         => 'IF(c.value_id > 0, c.value, d.value)'
            );

            $select = $this->_getReadAdapter()->select()
                ->from(array('oggetto'=>$this->getTable('score/oggetto')), $fields)
                ->where('oggetto.entity_id in (?)', $oggettos)
                ->joinRight(array('d'=>$tableName), $defaultCondition, array())
                ->joinLeft(array('c'=>$tableName), $condition, array())
                ->where('c.attribute_id IN (?) OR d.attribute_id IN (?)', $attributes);
            $part = $this->_getReadAdapter()->fetchAll($select);

            if (is_array($part)) {
                $result = array_merge($result, $part);
            }
        }

        return $result;
    }

    /**
     * Returns an array of oggetto children/parents
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int $id
     * @param array $additionalWheres
     * @return mixed
     */
    public function fetchLinkInformation($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {
        $idsConditionSymbol = "= ?";
        if (is_array($id)) {
            $idsConditionSymbol = "in (?)";
        }

        $select = $this->_getReadAdapter()->select();
        $select->from(array('l'=>$this->getTable($table)), array("l.{$idField}"))
            ->where("l.{$whereField} {$idsConditionSymbol}", $id);
        foreach ($additionalWheres as $field=>$condition) {
            $select->where("l.$field = ?", $condition);
        }

        // add status filter
        $this->_addAttributeFilter($select, 'status', 'l', $idField, $store,
            Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);
        // add website filter
        if ($websiteId = Mage::app()->getStore($store)->getWebsiteId()) {
            $select->join(
                array('w' => $this->getTable('score/oggetto_website')),
                "l.{$idField}=w.oggetto_id AND w.website_id={$websiteId}",
                array()
            );
        }

        $this->_setLinkSelect($select);
        $this->_prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres);

        return $this->_getWriteAdapter()->fetchCol($this->_getLinkSelect());
    }

    /**
     * Prepare select statement before 'fetchLinkInformation' function result fetch
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int $id
     * @param array $additionalWheres
     */
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {

    }

    /**
     * Return minimal prices for specified oggettos
     *
     * @param array $oggettos
     * @param array $priceAttributes
     * @param int $store
     * @return mixed
     */
    public function getMinimalPrice($oggettos, $priceAttributes, $store)
    {
        $website = Mage::app()->getStore($store)->getWebsiteId();

        $fields = array('customer_group_id', 'minimal_value'=>'MIN(value)');
        $select = $this->_getReadAdapter()->select()
            ->from(array('base'=>$this->getTable('scoreindex/price')), $fields)
            ->where('base.entity_id in (?)', $oggettos)
            ->where('base.attribute_id in (?)', $priceAttributes)
            ->where('base.website_id = ?', $website)
            ->group('base.customer_group_id');
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Return tier prices for specified oggetto in specified website
     *
     * @param array $oggettos
     * @param int $website
     * @return mixed
     */
    public function getTierPrices($oggettos, $website)
    {
        $fields = array(
            'entity_id',
            'type_id',
            'c.customer_group_id',
            'c.qty',
            'c.value',
            'c.all_groups',
        );
        $condition = "oggetto.entity_id = c.entity_id";

        $select = $this->_getReadAdapter()->select()
            ->from(array('oggetto'=>$this->getTable('score/oggetto')), $fields)
            ->joinLeft(array('c'=>"{$this->getTable('score/oggetto')}_tier_price"), $condition, array())
            ->where('oggetto.entity_id in (?)', $oggettos);
        if (Mage::helper('score')->isPriceGlobal())
        {
            $select->where('c.website_id=?', 0);
        }
        elseif (Mage::app()->getWebsite($website)->getBaseCurrencyCode() != Mage::app()->getBaseCurrencyCode()) {
            $select->where('c.website_id=?', $website);
        }
        else {
            $select->where('c.website_id IN(?)', array(0, $website));
        }

        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * Add attribute filter to select
     *
     * @param Varien_Db_Select $select
     * @param string $attributeCode
     * @param string $table the main table name or alias
     * @param string $field entity_id field name
     * @param int $store
     * @param int|string|array $value the filter value
     * @return Shaurmalab_ScoreIndex_Model_Resource_Data_Abstract
     */
    protected function _addAttributeFilter(Varien_Db_Select $select, $attributeCode, $table, $field, $store, $value)
    {
        $adapter = $this->_getReadAdapter();
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, $attributeCode);
        /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
        $attributeTable = $attribute->getBackend()->getTable();
        if ($attribute->getBackendType() == 'static') {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond   = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias)
            ));
            $select
                ->join(
                    array($tableAlias => $attributeTable),
                    $joinCond,
                    array())
                ->where(sprintf('%s.%s IN(?)', $tableAlias, $attribute->getAttributeCode()), $value);
        }
        elseif ($attribute->isScopeGlobal()) {
            $tableAlias = sprintf('t_%s', $attribute->getAttributeCode());
            $joinCond   = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableAlias),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableAlias), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableAlias), 0)
            ));
            $select
                ->join(
                    array($tableAlias => $attributeTable),
                    $joinCond,
                    array())
                ->where(sprintf('%s.value IN(?)', $tableAlias), $value);
        }
        else {
            $tableGlobal    = sprintf('t_global_%s', $attribute->getAttributeCode());
            $tableStore     = sprintf('t_store_%s', $attribute->getAttributeCode());
            $joinCondGlobal = join(' AND ', array(
                sprintf('`%s`.`%s`=`%s`.`entity_id`', $table, $field, $tableGlobal),
                $adapter->quoteInto(sprintf('`%s`.`attribute_id`=?', $tableGlobal), $attribute->getAttributeId()),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableGlobal), 0)
            ));
            $joinCondStore  = join(' AND ', array(
                sprintf('`%s`.`entity_id`=`%s`.`entity_id`', $tableGlobal, $tableStore),
                sprintf('`%s`.`attribute_id`=`%s`.`attribute_id`', $tableGlobal, $tableStore),
                $adapter->quoteInto(sprintf('`%s`.`store_id`=?', $tableStore), $store)
            ));
            $whereCond      = sprintf('IF(`%s`.`value_id`>0, `%s`.`value`, `%s`.`value`) IN(?)',
                $tableStore, $tableStore, $tableGlobal);

            $select
                ->join(
                    array($tableGlobal => $attributeTable),
                    $joinCondGlobal,
                    array())
                ->joinLeft(
                    array($tableStore => $attributeTable),
                    $joinCondStore,
                    array())
                ->where($whereCond, $value);
        }

        return $this;
    }
}
