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
 * CPWms Xml Format Api v2
 *
 * @category   Cafepress
 * @package    Cafepress_CPWms
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPWms_Model_Api_V2 extends Mage_Api_Model_Resource_Abstract
{
    public function processFormat($format, $type, $additional)
    {
        /** @var $xmlFormat Cafepress_CPWms_Model_Api_Xmlformat */
        $xmlFormat = Mage::getModel('cpwms/api_xmlformat');
        $xmlFormat->setFormat($format);
        $xmlFormat->setType($type);
        $xmlFormat->setAdditional($additional);

        $errors = $xmlFormat->process();
        $processedData = $xmlFormat->getProcessedData();
        if ($errors !== true) {
            $processedData['status'] = serialize($errors);
        } else {
            $processedData['status'] = 'OK';
        }
        return $processedData;
    }

    public function getAvailableOrders()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id');
        return serialize($collection->getData());
    }
}
