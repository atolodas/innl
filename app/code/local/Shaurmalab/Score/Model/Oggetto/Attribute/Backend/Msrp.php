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
 * Oggetto attribute for `Apply MAP` enable/disable option
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Attribute_Backend_Msrp extends Shaurmalab_Score_Model_Oggetto_Attribute_Backend_Boolean
{
    /**
     * Disable MAP if it's bundle with dynamic price type
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function beforeSave($oggetto)
    {
        if (!($oggetto instanceof Shaurmalab_Score_Model_Oggetto)
            || $oggetto->getTypeId() != Shaurmalab_Score_Model_Oggetto_Type::TYPE_BUNDLE
            || $oggetto->getPriceType() != Mage_Bundle_Model_Oggetto_Price::PRICE_TYPE_DYNAMIC
        ) {
            return parent::beforeSave($oggetto);
        }

        parent::beforeSave($oggetto);
        $attributeCode = $this->getAttribute()->getName();
        $value = $oggetto->getData($attributeCode);
        if (empty($value)) {
            $value = Mage::helper('score')->isMsrpApplyToAll();
        }
        if ($value) {
            $oggetto->setData($attributeCode, 0);
        }
        return $this;
    }
}
