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
 * Customer's scoretags edit block
 * This functionality was removed
 *
 * @deprecated  after 1.3.2.3
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_Block_Customer_Edit extends Mage_Core_Block_Template
{
    protected $_scoretag;

    public function getScoretag()
    {
        if( !$this->_scoretag ) {
            $this->_scoretag = Mage::registry('scoretagModel');
        }

        return $this->_scoretag;
    }

    public function getFormAction()
    {
        return $this->getUrl('*/*/save', array('scoretagId' => $this->getScoretag()->getScoretagId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/view', array('scoretagId' => $this->getScoretag()->getScoretagId()));
    }
}