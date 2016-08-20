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
 * Score oggetto options api
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Option_Api_V2 extends Shaurmalab_Score_Model_Oggetto_Option_Api
{

    /**
     * Add custom option to oggetto
     *
     * @param string $oggettoId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function add($oggettoId, $data, $store = null)
    {
        Mage::helper('api')->toArray($data);
        return parent::add($oggettoId, $data, $store);
    }

    /**
     * Update oggetto custom option data
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($optionId, $data, $store = null)
    {
        Mage::helper('api')->toArray($data);
        return parent::update($optionId, $data, $store);
    }

    /**
     * Retrieve list of oggetto custom options
     *
     * @param string $oggettoId
     * @param int|string|null $store
     * @return array
     */
    public function items($oggettoId, $store)
    {
        $result = parent::items($oggettoId, $store);
        foreach ($result as $key => $option) {
            $result[$key] = Mage::helper('api')->wsiArrayPacker($option);
        }
        return $result;
    }

}
