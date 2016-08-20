<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events comment form block
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author Ultimate Module Creator
 */
class Ultimate_Events_Block_Events_Comment_Form
    extends Mage_Core_Block_Template {
    /**
     * initialize
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct() {
        $customerSession = Mage::getSingleton('customer/session');
        parent::__construct();
        $data =  Mage::getSingleton('customer/session')->getEventsCommentFormData(true);
        $data = new Varien_Object($data);
        // add logged in customer name as nickname
        if (!$data->getName()) {
            $customer = $customerSession->getCustomer();
            if ($customer && $customer->getId()) {
                $data->setName($customer->getFirstname());
                $data->setEmail($customer->getEmail());
            }
        }
        $this->setAllowWriteCommentFlag($customerSession->isLoggedIn() || Mage::getStoreConfigFlag('ultimate_events/events/allow_guest_comment'));
        if (!$this->getAllowWriteCommentFlag()) {
            $this->setLoginLink(
                Mage::getUrl('customer/account/login/', array(
                    Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME => Mage::helper('core')->urlEncode(
                        Mage::getUrl('*/*/*', array('_current' => true)) .
                        '#comment-form')
                    )
                )
            );
        }
        $this->setCommentData($data);
    }
    /**
     * get current Events
     * @access public
     * @return Ultimate_Events_Model_Events
     * @author Ultimate Module Creator
     */
    public function getEvents() {
        return Mage::registry('current_events');
    }
    /**
     * get form action
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getAction() {
        return Mage::getUrl('ultimate_events/events/commentpost', array('id' => $this->getEvents()->getId()));
    }
}
