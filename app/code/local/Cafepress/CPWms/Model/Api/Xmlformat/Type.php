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
 * Xmlformat Type Model
 *
 * @category   Cafepress
 * @package    Cafepress_CPWms
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPWms_Model_Api_Xmlformat_Type
{
    const TYPE_ORDER         = 1;
    const TYPE_CREDITMEMO    = 2;
    const TYPE_ORDERSTATUS   = 3;
    const TYPE_DOWNLOAD_FILE = 4;
    const TYPE_TRANSFORMER   = 5;

    const DEFAULT_TYPE       = 0;
    const DEFAULT_TYPE_MODEL = 'cpwms/api_xmlformat_type_default';

    protected static $_types = array(
        self::TYPE_ORDER => array(
            'model' => 'cpwms/api_xmlformat_type_order'
        ),
        self::TYPE_CREDITMEMO => array(
            'model' => 'cpwms/api_xmlformat_type_order'
        ),
        self::TYPE_ORDERSTATUS => array(
            'model' => 'cpwms/api_xmlformat_type_orderstatus'
        ),
        self::TYPE_DOWNLOAD_FILE => array(
            'model' => 'cpwms/api_xmlformat_type_default'
        ),
        self::TYPE_TRANSFORMER => array(
            'model' => 'cpwms/api_xmlformat_type_transformer'
        ),
    );

    /**
     * Product type instance factory
     *
     * @param   Cafepress_CPWms_Model_Api_Xmlformat $format
     * @param   bool $singleton
     * @return  Cafepress_CPWms_Model_Api_Xmlformat_Type_Abstract
     */
    public static function factory(Cafepress_CPWms_Model_Api_Xmlformat $format, $singleton = false)
    {
        $types = self::getTypes();
        $typeId = $format->getType();

        if (!empty($types[$typeId]['model'])) {
            $typeModelName = $types[$typeId]['model'];
        } else {
            $typeModelName = self::DEFAULT_TYPE_MODEL;
            $typeId = self::DEFAULT_TYPE;
        }

        if ($singleton === true) {
            $typeModel = Mage::getSingleton($typeModelName);
        }
        else {
            $typeModel = Mage::getModel($typeModelName);
            $typeModel->setXmlFormat($format);
        }
        $typeModel->setConfig($types[$typeId]);
        return $typeModel;
    }

    static public function getTypes()
    {
        return self::$_types;
    }
}
