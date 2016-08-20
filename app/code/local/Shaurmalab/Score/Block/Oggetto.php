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


class Shaurmalab_Score_Block_Oggetto extends Mage_Core_Block_Template
{
    protected $_finalPrice = array();

    public function getOggetto()
    {
        if (!$this->getData('oggetto') instanceof Shaurmalab_Score_Model_Oggetto) {
            if ($this->getData('oggetto')->getOggettoId()) {
                $oggettoId = $this->getData('oggetto')->getOggettoId();
            }
            if ($oggettoId) {
                $oggetto = Mage::getModel('score/oggetto')->load($oggettoId);
                if ($oggetto) {
                    $this->setOggetto($oggetto);
                }
            }
        }
        return $this->getData('oggetto');
    }

    public function getPrice()
    {
        return $this->getOggetto()->getPrice();
    }

    public function getFinalPrice()
    {
        if (!isset($this->_finalPrice[$this->getOggetto()->getId()])) {
            $this->_finalPrice[$this->getOggetto()->getId()] = $this->getOggetto()->getFinalPrice();
        }
        return $this->_finalPrice[$this->getOggetto()->getId()];
    }

    public function getPriceHtml($oggetto)
    {
        $this->setTemplate('score/oggetto/price.phtml');
        $this->setOggetto($oggetto);
        return $this->toHtml();
    }
}
