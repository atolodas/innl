<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * New Accounts Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Neklo_ABTesting_Model_Resource_Report_Collection extends Neklo_ABTesting_Model_Resource_Log_Collection
{

    public function getCollection() { 

        return $this;
    }
    /**
     * Join created_at and accounts fields
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    protected function _joinFields($from = '', $to = '')
    {

       
        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
             ->_joinFields($from, $to);
        return $this;
    }

    /**
     * Set store ids to final result
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Accounts_Collection
     */
    public function setStoreIds($storeIds)
    {
        return $this;
    }
}