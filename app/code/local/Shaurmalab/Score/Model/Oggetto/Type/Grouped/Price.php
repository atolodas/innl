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
 * Grouped oggetto price model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Type_Grouped_Price extends Shaurmalab_Score_Model_Oggetto_Type_Price
{
    /**
     * Returns oggetto final price depending on options chosen
     *
     * @param   double $qty
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  double
     */
    public function getFinalPrice($qty=null, $oggetto)
    {
        if (is_null($qty) && !is_null($oggetto->getCalculatedFinalPrice())) {
            return $oggetto->getCalculatedFinalPrice();
        }

        $finalPrice = parent::getFinalPrice($qty, $oggetto);
        if ($oggetto->hasCustomOptions()) {
            /* @var $typeInstance Shaurmalab_Score_Model_Oggetto_Type_Grouped */
            $typeInstance = $oggetto->getTypeInstance(true);
            $associatedOggettos = $typeInstance->setStoreFilter($oggetto->getStore(), $oggetto)
                ->getAssociatedOggettos($oggetto);
            foreach ($associatedOggettos as $childOggetto) {
                /* @var $childOggetto Shaurmalab_Score_Model_Oggetto */
                $option = $oggetto->getCustomOption('associated_oggetto_' . $childOggetto->getId());
                if (!$option) {
                    continue;
                }
                $childQty = $option->getValue();
                if (!$childQty) {
                    continue;
                }
                $finalPrice += $childOggetto->getFinalPrice($childQty) * $childQty;
            }
        }

        $oggetto->setFinalPrice($finalPrice);
        Mage::dispatchEvent('score_oggetto_type_grouped_price', array('oggetto' => $oggetto));

        return max(0, $oggetto->getData('final_price'));
    }
}
