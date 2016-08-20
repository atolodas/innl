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
 * Configurable oggetto type resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_super_link', 'link_id');
    }

    /**
     * Save configurable oggetto relations
     *
     * @param Shaurmalab_Score_Model_Oggetto|int $mainOggetto the parent id
     * @param array $oggettoIds the children id array
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable
     */
    public function saveOggettos($mainOggetto, $oggettoIds)
    {
        $isOggettoInstance = false;
        if ($mainOggetto instanceof Shaurmalab_Score_Model_Oggetto) {
            $mainOggettoId = $mainOggetto->getId();
            $isOggettoInstance = true;
        } else {
            $mainOggettoId = $mainOggetto;
        }
        $old = $mainOggetto->getTypeInstance()->getUsedOggettoIds();

        $insert = array_diff($oggettoIds, $old);
        $delete = array_diff($old, $oggettoIds);

        if ((!empty($insert) || !empty($delete)) && $isOggettoInstance) {
            $mainOggetto->setIsRelationsChanged(true);
        }

        if (!empty($delete)) {
            $where = array(
                'parent_id = ?'     => $mainOggettoId,
                'oggetto_id IN(?)'  => $delete
            );
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $childId) {
                $data[] = array(
                    'oggetto_id' => (int)$childId,
                    'parent_id'  => (int)$mainOggettoId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);
        }

        // configurable oggetto relations should be added to relation table
        Mage::getResourceSingleton('score/oggetto_relation')
            ->processRelations($mainOggettoId, $oggettoIds);

        return $this;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        $childrenIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('l' => $this->getMainTable()), array('oggetto_id', 'parent_id'))
            ->join(
                array('e' => $this->getTable('score/oggetto')),
                'e.entity_id = l.oggetto_id AND e.required_options = 0',
                array()
            )
            ->where('parent_id = ?', $parentId);

        $childrenIds = array(0 => array());
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $childrenIds[0][$row['oggetto_id']] = $row['oggetto_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        $parentIds = array();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('oggetto_id', 'parent_id'))
            ->where('oggetto_id IN(?)', $childId);
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $parentIds[] = $row['parent_id'];
        }

        return $parentIds;
    }

    /**
     * Collect oggetto options with values according to the oggetto instance and attributes, that were received
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $attributes
     * @return array
     */
    public function getConfigurableOptions($oggetto, $attributes)
    {
        $attributesOptionsData = array();
        foreach ($attributes as $superAttribute) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array(
                        'super_attribute'       => $this->getTable('score/oggetto_super_attribute')
                    ),
                    array(
                        'sku'                   => 'entity.sku',
                        'oggetto_id'            => 'super_attribute.oggetto_id',
                        'attribute_code'        => 'attribute.attribute_code',
                        'option_title'          => 'option_value.value',
                        'pricing_value'         => 'attribute_pricing.pricing_value',
                        'pricing_is_percent'    => 'attribute_pricing.is_percent'
                    )
                )->joinInner(
                    array(
                        'oggetto_link'          => $this->getTable('score/oggetto_super_link')
                    ),
                    'oggetto_link.parent_id = super_attribute.oggetto_id',
                    array()
                )->joinInner(
                    array(
                        'attribute'             => $this->getTable('eav/attribute')
                    ),
                    'attribute.attribute_id = super_attribute.attribute_id',
                    array()
                )->joinInner(
                    array(
                        'entity'                => $this->getTable('score/oggetto')
                    ),
                    'entity.entity_id = oggetto_link.oggetto_id',
                    array()
                )->joinInner(
                    array(
                        'entity_value'          => $superAttribute->getBackendTable()
                    ),
                    implode(
                        ' AND ',
                        array(
                            $this->_getReadAdapter()
                                ->quoteInto('entity_value.entity_type_id = ?', $oggetto->getEntityTypeId()),
                            'entity_value.attribute_id = super_attribute.attribute_id',
                            'entity_value.store_id = 0',
                            'entity_value.entity_id = oggetto_link.oggetto_id'
                        )
                    ),
                    array()
                )->joinLeft(
                    array(
                        'option_value'          => $this->getTable('eav/attribute_option_value')
                    ),
                    implode(' AND ', array(
                        'option_value.option_id = entity_value.value',
                        'option_value.store_id = ' . Mage_Core_Model_App::ADMIN_STORE_ID,
                    )),
                    array()
                )->joinLeft(
                    array(
                        'attribute_pricing'     => $this->getTable('score/oggetto_super_attribute_pricing')
                    ),
                    implode(' AND ', array(
                        'super_attribute.oggetto_super_attribute_id = attribute_pricing.oggetto_super_attribute_id',
                        'entity_value.value = attribute_pricing.value_index'
                    )),
                    array()
                )->where('super_attribute.oggetto_id = ?', $oggetto->getId());

            $attributesOptionsData[$superAttribute->getAttributeId()] = $this->_getReadAdapter()->fetchAssoc($select);
        }
        return $attributesOptionsData;
    }
}
