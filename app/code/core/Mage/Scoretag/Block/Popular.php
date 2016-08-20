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
 * Popular scoretags block
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_Block_Popular extends Mage_Core_Block_Template
{

    protected $_scoretags;
    protected $_minPopularity;
    protected $_maxPopularity;

    protected function _loadScoretags()
    {
        if (empty($this->_scoretags)) {
            $this->_scoretags = array();

            $scoretags = Mage::getModel('scoretag/scoretag')->getPopularCollection()
                ->joinFields(Mage::app()->getStore()->getId())
                ->limit(20)
                ->load()
                ->getItems();

            if( count($scoretags) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($scoretags)->getPopularity();
            $this->_minPopularity = end($scoretags)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ($range == 0) ? 1 : $range;
            foreach ($scoretags as $scoretag) {
                $scoretag->setRatio(($scoretag->getPopularity()-$this->_minPopularity)/$range);
                $this->_scoretags[$scoretag->getName()] = $scoretag;
            }
            ksort($this->_scoretags);
        }
        return $this;
    }

    public function getScoretags()
    {
        $this->_loadScoretags();
        return $this->_scoretags;
    }

    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }

    protected function _toHtml()
    {
        if (count($this->getScoretags()) > 0) {
            return parent::_toHtml();
        }
        return '';
    }
}
