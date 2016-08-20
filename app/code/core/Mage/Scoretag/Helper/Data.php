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
 * Scoretag data helper
 */
class Mage_Scoretag_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStatusesArray()
    {
        return array(
            Mage_Scoretag_Model_Scoretag::STATUS_DISABLED => Mage::helper('scoretag')->__('Disabled'),
            Mage_Scoretag_Model_Scoretag::STATUS_PENDING  => Mage::helper('scoretag')->__('Pending'),
            Mage_Scoretag_Model_Scoretag::STATUS_APPROVED => Mage::helper('scoretag')->__('Approved')
        );
    }

    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('scoretag')->__('Disabled'),
                'value' => Mage_Scoretag_Model_Scoretag::STATUS_DISABLED
            ),
            array(
                'label' => Mage::helper('scoretag')->__('Pending'),
                'value' => Mage_Scoretag_Model_Scoretag::STATUS_PENDING
            ),
            array(
                'label' => Mage::helper('scoretag')->__('Approved'),
                'value' => Mage_Scoretag_Model_Scoretag::STATUS_APPROVED
            )
        );
    }

    /**
     * Check scoretags on the correctness of symbols and split string to array of scoretags
     *
     * @param string $scoretagNamesInString
     * @return array
     */
    public function extractScoretags($scoretagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $scoretagNamesInString));
    }

    /**
     * Clear scoretag from the separating characters
     *
     * @param array $scoretagNamesArr
     * @return array
     */
    public function cleanScoretags(array $scoretagNamesArr)
    {
        foreach ($scoretagNamesArr as $key => $scoretagName) {
            $scoretagNamesArr[$key] = trim($scoretagNamesArr[$key], '\'');
            $scoretagNamesArr[$key] = trim($scoretagNamesArr[$key]);
            if ($scoretagNamesArr[$key] == '') {
                unset($scoretagNamesArr[$key]);
            }
        }
        return $scoretagNamesArr;
    }

}
