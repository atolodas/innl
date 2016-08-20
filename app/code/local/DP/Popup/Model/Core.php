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
 * @copyright  Copyright (C) 2012 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Easy ajax core model
 *
 * @category   DP
 * @package    DP_Popup
 * @subpackage Model
 * @author     
 */
class DP_Popup_Model_Core
{
    /**
     * is easy ajax request
     *
     * @var bool
     */
    protected $_isPopup = null;

    /**
     * Is easy ajax event processed
     *
     * @var bool
     */
    protected $_proceed = false;


    /**
     * Is Easy Ajax Request
     *
     * @return bool
     */
    public function isPopup()
    {
        if ($this->_isPopup === null) {
            $this->_isPopup = Mage::app()->getRequest()->isXmlHttpRequest()
                && Mage::app()->getRequest()->getParam('popup', false);
        }
        return (bool) $this->_isPopup;
    }

    /**
     * Set that is easy ajax request or not
     *
     * @param bool $value
     */
    public function setPopup($value = true)
    {
        $this->_isPopup = (bool) $value;
    }

    /**
     * Is event processed
     *
     * @return bool
     */
    public function isProceed()
    {
        return (bool) $this->_proceed;
    }

    /**
     * Set that event processed
     *
     * @return $this
     */
    public function setProceed()
    {
        $this->_proceed = true;

        return $this;
    }

}
