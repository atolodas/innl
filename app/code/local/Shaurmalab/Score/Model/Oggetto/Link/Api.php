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
 * Score oggetto link api
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Link_Api extends Shaurmalab_Score_Model_Api_Resource
{
    /**
     * Oggetto link type mapping, used for references and validation
     *
     * @var array
     */
    protected $_typeMap = array(
        'related'       => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_RELATED,
        'up_sell'       => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_UPSELL,
        'cross_sell'    => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_CROSSSELL,
        'grouped'       => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'oggetto_store_id';
    }

    /**
     * Retrieve oggetto link associations
     *
     * @param string $type
     * @param int|sku $oggettoId
     * @param  string $identifierType
     * @return array
     */
    public function items($type, $oggettoId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $oggetto = $this->_initOggetto($oggettoId, $identifierType);

        $link = $oggetto->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $oggetto);

        $result = array();

        foreach ($collection as $linkedOggetto) {
            $row = array(
                'oggetto_id' => $linkedOggetto->getId(),
                'type'       => $linkedOggetto->getTypeId(),
                'set'        => $linkedOggetto->getAttributeSetId(),
                'sku'        => $linkedOggetto->getSku()
            );

            foreach ($link->getAttributes() as $attribute) {
                $row[$attribute['code']] = $linkedOggetto->getData($attribute['code']);
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Add oggetto link association
     *
     * @param string $type
     * @param int|string $oggettoId
     * @param int|string $linkedOggettoId
     * @param array $data
     * @param  string $identifierType
     * @return boolean
     */
    public function assign($type, $oggettoId, $linkedOggettoId, $data = array(), $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $oggetto = $this->_initOggetto($oggettoId, $identifierType);

        $link = $oggetto->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $oggetto);
        $idBySku = $oggetto->getIdBySku($linkedOggettoId);
        if ($idBySku) {
            $linkedOggettoId = $idBySku;
        }

        $links = $this->_collectionToEditableArray($collection);

        $links[(int)$linkedOggettoId] = array();

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data[$attribute['code']])) {
                $links[(int)$linkedOggettoId][$attribute['code']] = $data[$attribute['code']];
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($oggetto, $links, $typeId);
            } else {
                $link->getResource()->saveOggettoLinks($oggetto, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('score/oggetto_link');
            $_linkInstance->saveOggettoRelations($oggetto);

            $indexerStock = Mage::getModel('cataloginventory/stock_status');
            $indexerStock->updateStatus($oggettoId);

            $indexerPrice = Mage::getResourceModel('score/oggetto_indexer_price');
            $indexerPrice->reindexOggettoIds($oggettoId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', Mage::helper('score')->__('Link oggetto does not exist.'));
        }

        return true;
    }

    /**
     * Update oggetto link association info
     *
     * @param string $type
     * @param int|string $oggettoId
     * @param int|string $linkedOggettoId
     * @param array $data
     * @param  string $identifierType
     * @return boolean
     */
    public function update($type, $oggettoId, $linkedOggettoId, $data = array(), $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $oggetto = $this->_initOggetto($oggettoId, $identifierType);

        $link = $oggetto->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $oggetto);

        $links = $this->_collectionToEditableArray($collection);

        $idBySku = $oggetto->getIdBySku($linkedOggettoId);
        if ($idBySku) {
            $linkedOggettoId = $idBySku;
        }

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data[$attribute['code']])) {
                $links[(int)$linkedOggettoId][$attribute['code']] = $data[$attribute['code']];
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($oggetto, $links, $typeId);
            } else {
                $link->getResource()->saveOggettoLinks($oggetto, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('score/oggetto_link');
            $_linkInstance->saveOggettoRelations($oggetto);

            $indexerStock = Mage::getModel('cataloginventory/stock_status');
            $indexerStock->updateStatus($oggettoId);

            $indexerPrice = Mage::getResourceModel('score/oggetto_indexer_price');
            $indexerPrice->reindexOggettoIds($oggettoId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', Mage::helper('score')->__('Link oggetto does not exist.'));
        }

        return true;
    }

    /**
     * Remove oggetto link association
     *
     * @param string $type
     * @param int|string $oggettoId
     * @param int|string $linkedOggettoId
     * @param  string $identifierType
     * @return boolean
     */
    public function remove($type, $oggettoId, $linkedOggettoId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $oggetto = $this->_initOggetto($oggettoId, $identifierType);

        $link = $oggetto->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $oggetto);

        $idBySku = $oggetto->getIdBySku($linkedOggettoId);
        if ($idBySku) {
            $linkedOggettoId = $idBySku;
        }

        $links = $this->_collectionToEditableArray($collection);

        if (isset($links[$linkedOggettoId])) {
            unset($links[$linkedOggettoId]);
        }

        try {
            $link->getResource()->saveOggettoLinks($oggetto, $links, $typeId);
        } catch (Exception $e) {
            $this->_fault('not_removed');
        }

        return true;
    }

    /**
     * Retrieve attribute list for specified type
     *
     * @param string $type
     * @return array
     */
    public function attributes($type)
    {
        $typeId = $this->_getTypeId($type);

        $attributes = Mage::getModel('score/oggetto_link')
            ->getAttributes($typeId);

        $result = array();

        foreach ($attributes as $attribute) {
            $result[] = array(
                'code'  => $attribute['code'],
                'type'  => $attribute['type']
            );
        }

        return $result;
    }

    /**
     * Retrieve link types
     *
     * @return array
     */
    public function types()
    {
        return array_keys($this->_typeMap);
    }

    /**
     * Retrieve link type id by code
     *
     * @param string $type
     * @return int
     */
    protected function _getTypeId($type)
    {
        if (!isset($this->_typeMap[$type])) {
            $this->_fault('type_not_exists');
        }

        return $this->_typeMap[$type];
    }

    /**
     * Initialize and return oggetto model
     *
     * @param int $oggettoId
     * @param  string $identifierType
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _initOggetto($oggettoId, $identifierType = null)
    {
        $oggetto = Mage::helper('score/oggetto')->getOggetto($oggettoId, null, $identifierType);
        if (!$oggetto->getId()) {
            $this->_fault('oggetto_not_exists');
        }

        return $oggetto;
    }

    /**
     * Initialize and return linked oggettos collection
     *
     * @param Shaurmalab_Score_Model_Oggetto_Link $link
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Link_Oggetto_Collection
     */
    protected function _initCollection($link, $oggetto)
    {
        $collection = $link
            ->getOggettoCollection()
            ->setIsStrongMode()
            ->setOggetto($oggetto);

        return $collection;
    }

    /**
     * Export collection to editable array
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Link_Oggetto_Collection $collection
     * @return array
     */
    protected function _collectionToEditableArray($collection)
    {
        $result = array();

        foreach ($collection as $linkedOggetto) {
            $result[$linkedOggetto->getId()] = array();

            foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
                $result[$linkedOggetto->getId()][$attribute['code']] = $linkedOggetto->getData($attribute['code']);
            }
        }

        return $result;
    }
} // Class Shaurmalab_Score_Model_Oggetto_Link_Api End
