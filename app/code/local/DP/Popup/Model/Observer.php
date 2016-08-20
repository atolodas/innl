<?php
/**
 * Vladimir Fishchenko extension
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
 * Popup Event Observer
 *
 * @category   DP
 * @package    DP_Popup
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir@fishchenko.com>
 */
class DP_Popup_Model_Observer {
	/**
	 * Add Json to response instead of default data
	 */
	public function getJson() {
		$core = Mage::getSingleton('popup/core');
		if ($core->isPopup() && !$core->isProceed()) {
			$core->setProceed();
			/** @var $messages DP_Popup_Model_Message_Storage */
			$messages = Mage::getSingleton('popup/message_storage');
			/** @var $response DP_Popup_Model_Response */
			$response = Mage::getModel('popup/response');
			$response->setMessages($messages->getMessages());
			$response->loadContent(
				(array) Mage::app()->getRequest()->getParam('action_content', array()),
				(array) Mage::app()->getRequest()->getParam('custom_content', array())
			);
			$response->sendResponse();
		}
	}
}
