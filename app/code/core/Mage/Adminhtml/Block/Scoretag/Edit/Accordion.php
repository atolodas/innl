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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml scoretag accordion
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Scoretag_Edit_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add oggettos and customers accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('current_scoretag')->getId())) {
            return $this;
        }

        $scoretagModel = Mage::registry('current_scoretag');

        $this->setId('scoretag_customer_and_oggetto_accordion');

        $this->addItem('scoretag_customer', array(
            'title'         => Mage::helper('scoretag')->__('Customers Submitted this Scoretag'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/customer', array('ret' => 'all', 'scoretag_id'=>$scoretagModel->getId(), 'store'=>$scoretagModel->getStoreId())),
        ));

        $this->addItem('scoretag_oggetto', array(
            'title'         => Mage::helper('scoretag')->__('Oggettos Scoretagged by Customers'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/oggetto', array('ret' => 'all', 'scoretag_id'=>$scoretagModel->getId(), 'store'=>$scoretagModel->getStoreId())),
        ));
        return parent::_prepareLayout();
    }
}
