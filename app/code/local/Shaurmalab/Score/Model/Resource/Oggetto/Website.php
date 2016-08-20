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
 * Score Oggetto Website Resource Model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Website extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define resource table
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_website', 'oggetto_id');
    }

    /**
     * Get score oggetto resource model
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    protected function _getOggettoResource()
    {
        return Mage::getResourceSingleton('score/oggetto');
    }

    /**
     * Removes oggettos from websites
     *
     * @param array $websiteIds
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Website
     * @throws Exception
     */
    public function removeOggettos($websiteIds, $oggettoIds)
    {
        if (!is_array($websiteIds) || !is_array($oggettoIds)
            || count($websiteIds) == 0 || count($oggettoIds) == 0)
        {
            return $this;
        }

        $adapter   = $this->_getWriteAdapter();
        $whereCond = array(
            $adapter->quoteInto('website_id IN(?)', $websiteIds),
           $adapter->quoteInto('oggetto_id IN(?)', $oggettoIds)
        );
        $whereCond = join(' AND ', $whereCond);

        $adapter->beginTransaction();
        try {
            $adapter->delete($this->getMainTable(), $whereCond);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Add oggettos to websites
     *
     * @param array $websiteIds
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Website
     * @throws Exception
     */
    public function addOggettos($websiteIds, $oggettoIds)
    {
        if (!is_array($websiteIds) || !is_array($oggettoIds)
            || count($websiteIds) == 0 || count($oggettoIds) == 0)
        {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        // Before adding of oggettos we should remove it old rows with same ids
        $this->removeOggettos($websiteIds, $oggettoIds);
        try {
            foreach ($websiteIds as $websiteId) {
                foreach ($oggettoIds as $oggettoId) {
                    if (!$oggettoId) {
                        continue;
                    }
                    $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                        'oggetto_id' => (int) $oggettoId,
                        'website_id' => (int) $websiteId
                    ));
                }

                // Refresh oggetto enabled index
                $storeIds = Mage::app()->getWebsite($websiteId)->getStoreIds();
                foreach ($storeIds as $storeId) {
                    $store = Mage::app()->getStore($storeId);
                    $this->_getOggettoResource()->refreshEnabledIndex($store, $oggettoIds);
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve oggetto(s) website ids.
     *
     * @param array $oggettoIds
     * @return array
     */
    public function getWebsites($oggettoIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('oggetto_id', 'website_id'))
            ->where('oggetto_id IN (?)', $oggettoIds);
        $rowset  = $this->_getReadAdapter()->fetchAll($select);

        $result = array();
        foreach ($rowset as $row) {
            $result[$row['oggetto_id']][] = $row['website_id'];
        }

        return $result;
    }
}
