<?php
/**
 * Vladimir Fishchenko extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the DP Popup module to newer versions in the future.
 * If you wish to customize the DP Popup module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   DP
 * @package    DP_Popup
 * @copyright  Copyright (C) 2012 Vladimir Fishchenko (http://fishchenko.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Message collection model
 * Rewritten for store messages
 *
 * @category   DP
 * @package    DP_Popup
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir@fishchenko.com>
 */
class DP_Popup_Model_Core_Message_Collection extends Mage_Core_Model_Message_Collection
{
    /**
     * Adding new message to collection
     *
     * @param   Mage_Core_Model_Message_Abstract $message
     *
     * @return  Mage_Core_Model_Message_Collection
     */
    public function add(Mage_Core_Model_Message_Abstract $message)
    {
        if (!Mage::helper('core')->isModuleEnabled('DP_Popup') || !Mage::getSingleton('popup/core')->isPopup()) {
            $this->addMessage($message);
        } else {
            parent::addMessage($message);
        }

        return $this;
    }
}
