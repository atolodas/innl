<?php
/**
 * Cafepress extension for Magento
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
 * the Cafepress CPWms module to newer versions in the future.
 * If you wish to customize the Cafepress CPWms module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Cafepress
 * @package    Cafepress_CPWms
 * @copyright  Copyright (C) 2012 Cafepress
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Transformer xml format type
 *
 * @category   Cafepress
 * @package    Cafepress_CPWms
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPWms_Model_Api_Xmlformat_Type_Transformer extends Cafepress_CPWms_Model_Api_Xmlformat_Type_Abstract
{
    /**
     * process request
     *
     * @param Cafepress_CPWms_Model_Api_Xmlformat $object
     * @return string|bool
     */
    protected function _processRequest(Cafepress_CPWms_Model_Api_Xmlformat $object)
    {
        $format = $object->getFormat();
        /** @var $template Cafepress_CPWms_Model_Template */
        $template = Mage::getModel('wms/template');
        $template->setXmlformat(new Varien_Object(array('out_xml' => $format->request_body)));
        $this->_processedData['request_body'] = $template->getProcessedTemplate(
            array('customer_id' => 'CUSTOMER_ID'), true);
        return true;
    }
}
