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
 * ScoreSearch Fulltext Index Engine resource model
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Engine extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('scoresearch/fulltext', 'oggetto_id');
    }

    /**
     * Add entity data to fulltext search table
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entity 'product'|'cms'
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entity = 'oggetto')
    {
        $this->_getWriteAdapter()->insert($this->getMainTable(), array(
            'oggetto_id'    => $entityId,
            'store_id'      => $storeId,
            'data_index'    => $index
        ));
        return $this;
    }

    /**
     * Multi add entities data to fulltext search table
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entity 'product'|'cms'
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'oggetto')
    {
        $data    = array();
        $storeId = (int)$storeId;
        foreach ($entityIndexes as $entityId => $index) {
            $data[] = array(
                'oggetto_id'    => (int)$entityId,
                'store_id'      => $storeId,
                'data_index'    => $index
            );
        }

        if ($data) {
            Mage::getResourceHelper('scoresearch')
                ->insertOnDuplicate($this->getMainTable(), $data, array('data_index'));
        }

        return $this;
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return Mage::getSingleton('score/oggetto_visibility')->getVisibleInSearchIds();
    }

    /**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return false;
    }

    /**
     * Remove entity data from fulltext search table
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entity 'product'|'cms'
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entity = 'oggetto')
    {
        $where = array();

        if (!is_null($storeId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if (!is_null($entityId)) {
            $where[] = $this->_getWriteAdapter()->quoteInto('oggetto_id IN (?)', $entityId);
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Prepare index array as a string glued by separator
     *
     * @param array $index
     * @param string $separator
     * @return string
     */
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return Mage::helper('scoresearch')->prepareIndexdata($index, $separator);
    }

    /**
     * Stub method for compatibility with other search engines
     *
     * @return null
     */
    public function getResourceName()
    {
        return null;
    }

    /**
     * Retrieve fulltext search result data collection
     *
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext_Collection
     */
    public function getResultCollection()
    {
        return Mage::getResourceModel('scoresearch/fulltext_collection');
    }

    /**
     * Retrieve advanced search result data collection
     *
     * @return Shaurmalab_ScoreSearch_Model_Resource_Advanced_Collection
     */
    public function getAdvancedResultCollection()
    {
        return Mage::getResourceModel('scoresearch/advanced_collection');
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Define if engine is avaliable
     *
     * @return bool
     */
    public function test()
    {
        return true;
    }
}
