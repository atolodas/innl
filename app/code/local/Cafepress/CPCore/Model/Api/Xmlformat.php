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
 *
 * @method int                                  getType
 * @method Cafepress_CPCore_Model_Api_Xmlformat  setType
 * @method object                               getFormat
 * @method Cafepress_CPCore_Model_Api_Xmlformat  setFormat
 * @method array                                getProcessedData
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPCore_Model_Api_Xmlformat extends Varien_Object
{
    /**
     * Product type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract
     */
    protected $_typeInstance            = null;

    /**
     * Product type instance as singleton
     */
    protected $_typeInstanceSingleton   = null;

    /**
     * Retrieve type instance
     *
     * Type instance implement type depended logic
     *
     * @param  bool $singleton
     * @return Cafepress_CPCore_Model_Api_Xmlformat_Type_Abstract
     */
    public function getTypeInstance($singleton = false)
    {
        if ($singleton === true) {
            if (is_null($this->_typeInstanceSingleton)) {
                $this->_typeInstanceSingleton = Mage::getSingleton('cpcore/api_xmlformat_type')
                    ->factory($this, true);
            }
            return $this->_typeInstanceSingleton;
        }

        if ($this->_typeInstance === null) {
            $this->_typeInstance = Mage::getSingleton('cpcore/api_xmlformat_type')
                ->factory($this);
        }
        return $this->_typeInstance;
    }

    /**
     * process template
     *
     * @return array|bool
     */
    public function process()
    {
        $errors = $this->getTypeInstance()->process();
        if ($errors !== true) {
            $this->setProcessedData(array('request_body' => '', 'response_body' => ''));
            return $errors;
        } else {
            $this->setProcessedData($this->getTypeInstance()->getEncodedData());
            return true;
        }
    }

    public function setAdditional($data)
    {
        if (is_string($data)) {
            $data = unserialize($data);
        }
        $this->setData('additional', $data);
        return $this;
    }

    /**
     * get additional data
     *
     * @param null $key
     * @return mixed|array|null
     */
    public function getAdditional($key = null)
    {
        $additional = $this->getData('additional');
        if (!$key) {
            return (array) $additional;
        } else {
            if (isset($additional[$key])) {
                return $additional[$key];
            } else {
                return null;
            }
        }
    }
}
