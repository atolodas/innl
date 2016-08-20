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
 * @category   Swms
 * @package    Swms_Optionimage
 * @author     SWMS Systemtechnik Ingenieurgesellschaft mbH
 * @copyright  Copyright (c) 2011 WMS Systemtechnik Ingenieurgesellschaft mbH (http://www.swms.de)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml config system template source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Swms_Optionimage_Model_Config_Source_Options extends Varien_Object
{
    /**
     * Generate list of supported options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::helper('optionimage')->getOptionsList();
    }

}
