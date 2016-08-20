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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Messages block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Messages extends Mage_Core_Block_Template {
	/**
	 * Messages collection
	 *
	 * @var Mage_Core_Model_Message_Collection
	 */
	protected $_messages;

	/**
	 * Store first level html tag name for messages html output
	 *
	 * @var string
	 */
	protected $_messagesFirstLevelTagName = 'ul';

	/**
	 * Store second level html tag name for messages html output
	 *
	 * @var string
	 */
	protected $_messagesSecondLevelTagName = 'li';

	/**
	 * Store content wrapper html tag name for messages html output
	 *
	 * @var string
	 */
	protected $_messagesContentWrapperTagName = 'span';

	/**
	 * Flag which require message text escape
	 *
	 * @var bool
	 */
	protected $_escapeMessageFlag = false;

	/**
	 * Storage for used types of message storages
	 *
	 * @var array
	 */
	protected $_usedStorageTypes = array('core/session','customer/session','catalog/session');

	public function _prepareLayout() {
		$this->addMessages(Mage::getSingleton('core/session')->getMessages(true));
		parent::_prepareLayout();
	}

	/**
	 * Set message escape flag
	 * @param bool $flag
	 * @return Mage_Core_Block_Messages
	 */
	public function setEscapeMessageFlag($flag) {
		$this->_escapeMessageFlag = $flag;
		return $this;
	}

	/**
	 * Set messages collection
	 *
	 * @param   Mage_Core_Model_Message_Collection $messages
	 * @return  Mage_Core_Block_Messages
	 */
	public function setMessages(Mage_Core_Model_Message_Collection $messages) {
		$this->_messages = $messages;
		return $this;
	}

	/**
	 * Add messages to display
	 *
	 * @param Mage_Core_Model_Message_Collection $messages
	 * @return Mage_Core_Block_Messages
	 */
	public function addMessages(Mage_Core_Model_Message_Collection $messages) {
		foreach ($messages->getItems() as $message) {
			$this->getMessageCollection()->add($message);
		}
		return $this;
	}

	/**
	 * Retrieve messages collection
	 *
	 * @return Mage_Core_Model_Message_Collection
	 */
	public function getMessageCollection() {
		if (!($this->_messages instanceof Mage_Core_Model_Message_Collection)) {
			$this->_messages = Mage::getModel('core/message_collection');
		}
		return $this->_messages;
	}

	/**
	 * Adding new message to message collection
	 *
	 * @param   Mage_Core_Model_Message_Abstract $message
	 * @return  Mage_Core_Block_Messages
	 */
	public function addMessage(Mage_Core_Model_Message_Abstract $message) {
		$this->getMessageCollection()->add($message);
		return $this;
	}

	/**
	 * Adding new error message
	 *
	 * @param   string $message
	 * @return  Mage_Core_Block_Messages
	 */
	public function addError($message) {
		$this->addMessage(Mage::getSingleton('core/message')->error($message));
		return $this;
	}

	/**
	 * Adding new warning message
	 *
	 * @param   string $message
	 * @return  Mage_Core_Block_Messages
	 */
	public function addWarning($message) {
		$this->addMessage(Mage::getSingleton('core/message')->warning($message));
		return $this;
	}

	/**
	 * Adding new nitice message
	 *
	 * @param   string $message
	 * @return  Mage_Core_Block_Messages
	 */
	public function addNotice($message) {
		$this->addMessage(Mage::getSingleton('core/message')->notice($message));
		return $this;
	}

	/**
	 * Adding new success message
	 *
	 * @param   string $message
	 * @return  Mage_Core_Block_Messages
	 */
	public function addSuccess($message) {
		$this->addMessage(Mage::getSingleton('core/message')->success($message));
		return $this;
	}

	/**
	 * Retrieve messages array by message type
	 *
	 * @param   string $type
	 * @return  array
	 */
	public function getMessages($type = null) {
		return $this->getMessageCollection()->getItems($type);
	}

	/**
	 * Retrieve messages in HTML format
	 *
	 * @param   string $type
	 * @return  string
	 */
	public function getHtml($type = null) {
		$html = '';
		return $html;
	}

	/**
	 * Retrieve messages in HTML format grouped by type
	 *
	 * @param   string $type
	 * @return  string
	 */
	public function getGroupedHtml() {
		$types = array(
			Mage_Core_Model_Message::ERROR,
			Mage_Core_Model_Message::WARNING,
			Mage_Core_Model_Message::NOTICE,
			Mage_Core_Model_Message::SUCCESS,
		);
		$html = '';
		$html .= '<div class="messages">';
		foreach ($types as $type) {
			if ($messages = $this->getMessages($type)) {
				if(!Mage::app()->getStore()->isAdmin()) $html .= '<div class="' . $type . '-dialog" style="z-index: 1000;display:none;" id="dialog-message-' . $type . '">';
				else $html .= '<div class="' . $type . ' ' . $type . '-msg" style="padding:10px 30px; margin:5px 0; font-size:15px " id="' . $type . '">';
				$html .= '<span>';

				foreach ($messages as $message) {
					$html .= ($this->_escapeMessageFlag) ? $this->escapeHtml($message->getText()) : $message->getText();
				}
				$html .= '</span>';
			
					$html .= '</div>';
				if(!Mage::app()->getStore()->isAdmin()) { 

				if ($type == 'success') {
					$html .= "<script type='text/javascript'>
                                 jQuery(document).ready(function () {
                                        confirmDialogSuccess = jQuery('#dialog-message-success').dialog({
                                                autoOpen: false,
                                                modal: true,
                                                minWidth: 400,
                                                buttons: {},
                                                dialogClass: 'success-dialog success-dialog2',
                                                create: function () {
                                                    jQuery('.ui-dialog .btn').removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                                                    jQuery('.ui-dialog-titlebar-close').html('<i class=\'ui-dialog-titlebar-close white fa fa-close\'></i>');
                                                },
                                            });
                                        confirmDialogSuccess.dialog('open').css({'min-height':'15px','padding':'0px'});
                                        setTimeout('confirmDialogSuccess.dialog(\'close\');', 6000);
                                    });
                                </script>";
				}
				if ($type == 'error') {
					$html .= "<script type='text/javascript'>
                            jQuery(document).ready(function () {
                                confirmDialog = jQuery('#dialog-message-error').dialog({
                                        autoOpen: false,
                                        modal: true,
                                        minWidth: 400,
                                        buttons: {},
                                        dialogClass: 'error-dialog',
                                        create: function () {
                                            jQuery('.ui-dialog .btn').removeClass(' ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover');
                                            jQuery('.ui-dialog-titlebar-close').html('<i class=\'ui-dialog-titlebar-close white fa fa-close\'></i>');
                                        },
                                    });
                                confirmDialog.dialog('open').css('min-height','15px');
                                setTimeout('confirmDialog.dialog(\'close\');', 3000);
                            });
                        </script>";
				}
				}
			}

		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Set messages first level html tag name for output messages as html
	 *
	 * @param string $tagName
	 */
	public function setMessagesFirstLevelTagName($tagName) {
		$this->_messagesFirstLevelTagName = $tagName;
	}

	/**
	 * Set messages first level html tag name for output messages as html
	 *
	 * @param string $tagName
	 */
	public function setMessagesSecondLevelTagName($tagName) {
		$this->_messagesSecondLevelTagName = $tagName;
	}

	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo() {
		return array(
			'storage_types' => serialize($this->_usedStorageTypes),
		);
	}

	/**
	 * Add used storage type
	 *
	 * @param string $type
	 */
	public function addStorageType($type) {
		$this->_usedStorageTypes[] = $type;
	}
}
