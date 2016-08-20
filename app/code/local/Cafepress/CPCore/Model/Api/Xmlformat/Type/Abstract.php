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
 * Xml format type instance abstract model
 *
 * @method array                                getConfig
 * @method Cafepress_CPCore_Model_Api_Xmlformat  getXmlFormat
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPCore_Model_Api_Xmlformat_Type_Abstract extends Mage_Core_Model_Abstract
{
    protected $_processedData = array(
        'request_body'  => '',
        'response_body' => '',
    );
    /**
     * get request_body
     *
     * @return string
     */
    public function getRequestBody()
    {
        return (string) $this->_processedData['request_body'];
    }

    /**
     * get response body
     *
     * @return string
     */
    public function getResponseBody()
    {
        return (string) $this->_processedData['response_body'];
    }

    /**
     * get additional data
     *
     * @return array
     */
    protected function _getAdditional()
    {
        return array();
    }

    /**
     * get additional data
     *
     * @return string Serialized array
     */
    public function getAdditional()
    {
        return serialize($this->_getAdditional());
    }

    /**
     * process xml format
     * from xml format template to xml format with values
     *
     * @param null $object
     * @return array|bool
     */
    public function process($object = null)
    {
        /** @var $object Cafepress_CPCore_Model_Api_Xmlformat */
        if (!$object) {
            $object = $this->getXmlFormat();
        }

        $errors = array();
        $error = $this->_processRequest($object);
        if ($error !== true) {
            $errors[] = $error;
        }
        $error = $this->_processResponse($object);
        if ($error !== true) {
            $errors[] = $error;
        }

        if (empty($errors)) {
            return true;
        } else {
            return $errors;
        }
    }

    /**
     * process request
     *
     * @param Cafepress_CPCore_Model_Api_Xmlformat $object
     * @return string|bool
     */
    protected function _processRequest(Cafepress_CPCore_Model_Api_Xmlformat $object)
    {
        return 'process request method is not implemented';
    }

    /**
     * process response
     *
     * @param Cafepress_CPCore_Model_Api_Xmlformat $object
     * @return string|bool
     */
    protected function _processResponse(Cafepress_CPCore_Model_Api_Xmlformat $object)
    {
        return true;
    }

    /**
     * @return array
     */
    public function getEncodedData()
    {
        $data = $this->_processedData;
        foreach ($data as &$_value) {
            $_value = base64_encode($_value);
        }
        return $data;
    }
}
