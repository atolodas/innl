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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Oggetto Scoretag API
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Api_V2 extends Mage_Scoretag_Model_Api
{
    /**
     * Retrieve list of scoretags for specified oggetto as array of objects
     *
     * @param int $oggettoId
     * @param string|int $store
     * @return array
     */
    public function items($oggettoId, $store)
    {
        $result = parent::items($oggettoId, $store);
        foreach ($result as $key => $scoretag) {
            $result[$key] = Mage::helper('api')->wsiArrayPacker($scoretag);
        }
        return array_values($result);
    }

    /**
     * Add scoretag(s) to oggetto.
     * Return array of objects
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $result = array();
        foreach (parent::add($data) as $key => $value) {
            $result[] = array('key' => $key, 'value' => $value);
        }

        return $result;
    }

    /**
     * Retrieve scoretag info as object
     *
     * @param int $scoretagId
     * @param string|int $store
     * @return object
     */
    public function info($scoretagId, $store)
    {
        $result = parent::info($scoretagId, $store);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->oggettos as $key => $value) {
            $result->oggettos[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }

    /**
     * Convert data from object to array before add
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForAdd($data);
    }

    /**
     * Convert data from object to array before update
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForUpdate($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForUpdate($data);
    }
}
