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
 * Score oggetto link resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Link extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Oggetto Link Attributes Table
     *
     * @var string
     */
    protected $_attributesTable;

    /**
     * Define main table name and attributes table
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_link', 'link_id');
        $this->_attributesTable = $this->getTable('score/oggetto_link_attribute');
    }

    /**
     * Save Oggetto Links process
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $data
     * @param int $typeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link
     */
    public function saveOggettoLinks($oggetto, $data, $typeId)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $attributes = $this->getAttributesByType($typeId);
        $adapter    = $this->_getWriteAdapter();

        $bind   = array(
            ':oggetto_id'    => (int)$oggetto->getId(),
            ':link_type_id'  => (int)$typeId
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('linked_oggetto_id', 'link_id'))
            ->where('oggetto_id = :oggetto_id')
            ->where('link_type_id = :link_type_id');

        $links   = $adapter->fetchPairs($select, $bind);

        $deleteIds = array();
        foreach($links as $linkedOggettoId => $linkId) {
            if (!isset($data[$linkedOggettoId])) {
                $deleteIds[] = (int)$linkId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete($this->getMainTable(), array(
                'link_id IN (?)' => $deleteIds,
            ));
        }

        foreach ($data as $linkedOggettoId => $linkInfo) {
            $linkId = null;
            if (isset($links[$linkedOggettoId])) {
                $linkId = $links[$linkedOggettoId];
                unset($links[$linkedOggettoId]);
            } else {
                $bind = array(
                    'oggetto_id'        => $oggetto->getId(),
                    'linked_oggetto_id' => $linkedOggettoId,
                    'link_type_id'      => $typeId
                );
                $adapter->insert($this->getMainTable(), $bind);
                $linkId = $adapter->lastInsertId($this->getMainTable());
            }

            foreach ($attributes as $attributeInfo) {
                $attributeTable = $this->getAttributeTypeTable($attributeInfo['type']);
                if ($attributeTable) {
                    if (isset($linkInfo[$attributeInfo['code']])) {
                        $value = $this->_prepareAttributeValue($attributeInfo['type'],
                            $linkInfo[$attributeInfo['code']]);
                        $bind = array(
                            'oggetto_link_attribute_id' => $attributeInfo['id'],
                            'link_id'                   => $linkId,
                            'value'                     => $value
                        );
                        $adapter->insertOnDuplicate($attributeTable, $bind, array('value'));
                    } else {
                        $adapter->delete($attributeTable, array(
                            'link_id = ?'                   => $linkId,
                            'oggetto_link_attribute_id = ?' => $attributeInfo['id']
                        ));
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Prepare link attribute value by attribute type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function _prepareAttributeValue($type, $value)
    {
        if ($type == 'int') {
            $value = (int)$value;
        } elseif ($type == 'decimal') {
            $value = (float)sprintf('%F', $value);
        }
        return $value;
    }

    /**
     * Retrieve oggetto link attributes by link type
     *
     * @param int $typeId
     * @return array
     */
    public function getAttributesByType($typeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_attributesTable, array(
                'id'    => 'oggetto_link_attribute_id',
                'code'  => 'oggetto_link_attribute_code',
                'type'  => 'data_type'
            ))
            ->where('link_type_id = ?', $typeId);
        return $adapter->fetchAll($select);
    }

    /**
     * Returns table for link attribute by attribute type
     *
     * @param string $type
     * @return string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->getTable('score/oggetto_link_attribute_' . $type);
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param int $typeId
     * @return array
     */
    public function getChildrenIds($parentId, $typeId)
    {
        $adapter     = $this->_getReadAdapter();
        $childrenIds = array();
        $bind        = array(
            ':oggetto_id'    => (int)$parentId,
            ':link_type_id'  => (int)$typeId
        );
        $select = $adapter->select()
            ->from(array('l' => $this->getMainTable()), array('linked_oggetto_id'))
            ->where('oggetto_id = :oggetto_id')
            ->where('link_type_id = :link_type_id');
        if ($typeId == Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED) {
            $select->join(
                array('e' => $this->getTable('score/oggetto')),
                'e.entity_id = l.linked_oggetto_id AND e.required_options = 0',
                array()
            );
        }

        $childrenIds[$typeId] = array();
        $result = $adapter->fetchAll($select, $bind);
        foreach ($result as $row) {
            $childrenIds[$typeId][$row['linked_oggetto_id']] = $row['linked_oggetto_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by required child
     *
     * @param int|array $childId
     * @param int $typeId
     * @return array
     */
    public function getParentIdsByChild($childId, $typeId)
    {
        $parentIds  = array();
        $adapter    = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('oggetto_id', 'linked_oggetto_id'))
            ->where('linked_oggetto_id IN(?)', $childId)
            ->where('link_type_id = ?', $typeId);

        $result = $adapter->fetchAll($select);
        foreach ($result as $row) {
            $parentIds[] = $row['oggetto_id'];
        }

        return $parentIds;
    }

    /**
     * Save grouped oggetto relations
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $data
     * @param int $typeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link
     */
    public function saveGroupedLinks($oggetto, $data, $typeId)
    {
        $adapter = $this->_getWriteAdapter();
        // check for change relations
        $bind    = array(
            'oggetto_id'    => (int)$oggetto->getId(),
            'link_type_id'  => (int)$typeId
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('linked_oggetto_id'))
            ->where('oggetto_id = :oggetto_id')
            ->where('link_type_id = :link_type_id');
        $old = $adapter->fetchCol($select, $bind);
        $new = array_keys($data);

        if (array_diff($old, $new) || array_diff($new, $old)) {
            $oggetto->setIsRelationsChanged(true);
        }

        // save oggetto links attributes
        $this->saveOggettoLinks($oggetto, $data, $typeId);

        // Grouped oggetto relations should be added to relation table
        Mage::getResourceSingleton('score/oggetto_relation')
            ->processRelations($oggetto->getId(), $new);

        return $this;
    }
}
