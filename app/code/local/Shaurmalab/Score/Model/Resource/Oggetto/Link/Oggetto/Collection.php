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
 * Score oggetto linked oggettos collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection extends Shaurmalab_Score_Model_Resource_Oggetto_Collection
{
    /**
     * Store oggetto model
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggetto;

    /**
     * Store oggetto link model
     *
     * @var Shaurmalab_Score_Model_Oggetto_Link
     */
    protected $_linkModel;

    /**
     * Store link type id
     *
     * @var int
     */
    protected $_linkTypeId;

    /**
     * Store strong mode flag that determine if needed for inner join or left join of linked oggettos
     *
     * @var bool
     */
    protected $_isStrongMode;

    /**
     * Store flag that determine if oggetto filter was enabled
     *
     * @var bool
     */
    protected $_hasLinkFilter  = false;

    /**
     * Declare link model and initialize type attributes join
     *
     * @param Shaurmalab_Score_Model_Oggetto_Link $linkModel
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setLinkModel(Shaurmalab_Score_Model_Oggetto_Link $linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->getLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }
        return $this;
    }

    /**
     * Enable strong mode for inner join of linked oggettos
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setIsStrongMode()
    {
        $this->_isStrongMode = true;
        return $this;
    }

    /**
     * Retrieve collection link model
     *
     * @return Shaurmalab_Score_Model_Oggetto_Link
     */
    public function getLinkModel()
    {
        return $this->_linkModel;
    }

    /**
     * Initialize collection parent oggetto and add limitation join
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setOggetto(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $this->_oggetto = $oggetto;
        if ($oggetto && $oggetto->getId()) {
            $this->_hasLinkFilter = true;
            $this->setStore($oggetto->getStore());
        }
        return $this;
    }

    /**
     * Retrieve collection base oggetto object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        return $this->_oggetto;
    }

    /**
     * Exclude oggettos from filter
     *
     * @param array $oggettos
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function addExcludeOggettoFilter($oggettos)
    {
        if (!empty($oggettos)) {
            if (!is_array($oggettos)) {
                $oggettos = array($oggettos);
            }
            $this->_hasLinkFilter = true;
            $this->getSelect()->where('links.linked_oggetto_id NOT IN (?)', $oggettos);
        }
        return $this;
    }

    /**
     * Add oggettos to filter
     *
     * @param array|int|string $oggettos
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function addOggettoFilter($oggettos)
    {
        if (!empty($oggettos)) {
            if (!is_array($oggettos)) {
                $oggettos = array($oggettos);
            }
            $this->getSelect()->where('links.oggetto_id IN (?)', $oggettos);
            $this->_hasLinkFilter = true;
        }

        return $this;
    }
	
	    /**
     * Add oggettos to filter
     *
     * @param array|int|string $oggettos
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function addParentOggettoFilter($oggettos)
    {
        if (!empty($oggettos)) {
            if (!is_array($oggettos)) {
                $oggettos = array($oggettos);
            }
			$this->getSelect()->where('links.linked_oggetto_id IN (?)', $oggettos);
            $this->_hasLinkFilter = true;
        }

        return $this;
    }

    /**
     * Add random sorting order
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setRandomOrder()
    {
        $this->getSelect()->orderRand('main_table.entity_id');
        return $this;
    }

    /**
     * Setting group by to exclude duplications in collection
     *
     * @param string $groupBy
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setGroupBy($groupBy = 'e.entity_id')
    {
        $this->getSelect()->group($groupBy);

        /*
         * Allow Analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Join linked oggettos when specified link model
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    protected function _beforeLoad()
    {
        if ($this->getLinkModel()) {
            $this->_joinLinks();
        }
        return parent::_beforeLoad();
    }

    /**
     * Join linked oggettos and their attributes
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    protected function _joinLinks()
    {
        $select  = $this->getSelect();
        $adapter = $select->getAdapter();

        $joinCondition = array(
            'links.linked_oggetto_id = e.entity_id',
            $adapter->quoteInto('links.link_type_id = ?', $this->_linkTypeId)
        );
        $joinType = 'join';
        if ($this->getOggetto() && $this->getOggetto()->getId()) {
            $oggettoId = $this->getOggetto()->getId();
            if ($this->_isStrongMode) {
                $this->getSelect()->where('links.oggetto_id = ?', (int)$oggettoId);
            } else {
                $joinType = 'joinLeft';
                $joinCondition[] = $adapter->quoteInto('links.oggetto_id = ?', $oggettoId);
            }
            $this->addFieldToFilter('entity_id', array('neq' => $oggettoId));
        } else if ($this->_isStrongMode) {
            $this->addFieldToFilter('entity_id', array('eq' => -1));
        }
        if($this->_hasLinkFilter) {
            $select->$joinType(
                array('links' => $this->getTable('score/oggetto_link')),
                implode(' AND ', $joinCondition),
                array('link_id')
            );
            $this->joinAttributes();
        }
        return $this;
    }




    /**
     * Enable sorting oggettos by its position
     *
     * @param string $dir sort type asc|desc
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setPositionOrder($dir = self::SORT_ORDER_ASC)
    {
        if ($this->_hasLinkFilter) {
            $this->getSelect()->order('position ' . $dir);
        }
        return $this;
    }

    /**
     * Enable sorting oggettos by its attribute set name
     *
     * @param string $dir sort type asc|desc
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setAttributeSetIdOrder($dir = self::SORT_ORDER_ASC)
    {
        $this->getSelect()
            ->joinLeft(
                array('set' => $this->getTable('eav/attribute_set')),
                'e.attribute_set_id = set.attribute_set_id',
                array('attribute_set_name')
            )
            ->order('set.attribute_set_name ' . $dir);
        return $this;
    }

    /**
     * Join attributes
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function joinAttributes()
    {
        if (!$this->getLinkModel()) {
            return $this;
        }
        $attributes = $this->getLinkModel()->getAttributes();

        $attributesByType = array();
        foreach ($attributes as $attribute) {
            $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
            $alias = sprintf('link_attribute_%s_%s', $attribute['code'], $attribute['type']);

            $joinCondiotion = array(
                "{$alias}.link_id = links.link_id",
                $this->getSelect()->getAdapter()->quoteInto("{$alias}.oggetto_link_attribute_id = ?", $attribute['id'])
            );
            $this->getSelect()->joinLeft(
                array($alias => $table),
                implode(' AND ', $joinCondiotion),
                array($attribute['code'] => 'value')
            );
        }

        return $this;
    }

    /**
     * Set sorting order
     *
     * $attribute can also be an array of attributes
     *
     * @param string|array $attribute
     * @param string $dir
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == 'position') {
            return $this->setPositionOrder($dir);
        } elseif ($attribute == 'attribute_set_id') {
            return $this->setAttributeSetIdOrder($dir);
        }
        return parent::setOrder($attribute, $dir);
    }
}
