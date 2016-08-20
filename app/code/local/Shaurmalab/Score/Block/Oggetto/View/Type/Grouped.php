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
 * Score grouped oggetto info block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_View_Type_Grouped extends Shaurmalab_Score_Block_Oggetto_View_Abstract
{
    public function getAssociatedOggettos()
    {
        return $this->getOggetto()->getTypeInstance(true)
            ->getAssociatedOggettos($this->getOggetto());
    }


    /**
     * Set preconfigured values to grouped associated oggettos
     *
     * @return Shaurmalab_Score_Block_Oggetto_View_Type_Grouped
     */
    public function setPreconfiguredValue() {
        $configValues = $this->getOggetto()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedOggettos = $this->getAssociatedOggettos();
            foreach ($associatedOggettos as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }
}
