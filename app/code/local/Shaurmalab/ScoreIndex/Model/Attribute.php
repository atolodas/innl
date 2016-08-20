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
 * @package     Shaurmalab_ScoreIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Attribute index model
 *
 * @method Shaurmalab_ScoreIndex_Model_Resource_Attribute _getResource()
 * @method Shaurmalab_ScoreIndex_Model_Resource_Attribute getResource()
 * @method Shaurmalab_ScoreIndex_Model_Attribute setEntityId(int $value)
 * @method int getAttributeId()
 * @method Shaurmalab_ScoreIndex_Model_Attribute setAttributeId(int $value)
 * @method int getStoreId()
 * @method Shaurmalab_ScoreIndex_Model_Attribute setStoreId(int $value)
 * @method int getValue()
 * @method Shaurmalab_ScoreIndex_Model_Attribute setValue(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Attribute extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('scoreindex/attribute');
        $this->_getResource()->setStoreId(Mage::app()->getStore()->getId());
    }

    public function getFilteredEntities($attribute, $filter, $entityFilter)
    {
        return $this->_getResource()->getFilteredEntities($attribute, $filter, $entityFilter);
    }

    public function getCount($attribute, $entityFilter)
    {
        return $this->_getResource()->getCount($attribute, $entityFilter);
    }

    public function checkCount($optionIds, $attribute, $entityFilter)
    {
        return $this->_getResource()->checkCount($optionIds, $attribute, $entityFilter);
    }

    public function applyFilterToCollection($collection, $attribute, $value)
    {
        $this->_getResource()->applyFilterToCollection($collection, $attribute, $value);
        return $this;
    }
}
