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
 * Score oggetto link api V2
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Link_Api_V2 extends Shaurmalab_Score_Model_Oggetto_Link_Api
{
    /**
     * Add oggetto link association
     *
     * @param string $type
     * @param int|string $oggettoId
     * @param int|string $linkedOggettoId
     * @param array $data
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
            if (isset($data->$attribute['code'])) {
                $links[(int)$linkedOggettoId][$attribute['code']] = $data->$attribute['code'];
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
            $this->_fault('data_invalid', $e->getMessage());
            //$this->_fault('data_invalid', Mage::helper('score')->__('Link oggetto does not exist.'));
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
            if (isset($data->$attribute['code'])) {
                $links[(int)$linkedOggettoId][$attribute['code']] = $data->$attribute['code'];
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
}
