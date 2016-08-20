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
 * the Cafepress CPCore module to newer versions in the future.
 * If you wish to customize the Cafepress CPCore module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @copyright  Copyright (C) 2012 Cafepress
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default type model
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPCore_Model_Api_Xmlformat_Type_Default extends Cafepress_CPCore_Model_Api_Xmlformat_Type_Abstract
{
    /**
     * process request
     *
     * @param Cafepress_CPCore_Model_Api_Xmlformat $object
     * @return string|bool
     */
    protected function _processRequest(Cafepress_CPCore_Model_Api_Xmlformat $object)
    {
        $this->_processedData['request_body'] = $object->getFormat()->request_body;
        return true;
    }
}
