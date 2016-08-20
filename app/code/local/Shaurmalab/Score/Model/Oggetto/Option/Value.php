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
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Score oggetto option select type model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Option_Value _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Option_Value getResource()
 * @method int getOptionId()
 * @method Shaurmalab_Score_Model_Oggetto_Option_Value setOptionId(int $value)
 * @method string getSku()
 * @method Shaurmalab_Score_Model_Oggetto_Option_Value setSku(string $value)
 * @method int getSortOrder()
 * @method Shaurmalab_Score_Model_Oggetto_Option_Value setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Option_Value extends Mage_Core_Model_Abstract
{
    protected $_values = array();

    protected $_oggetto;

    protected $_option;

    protected function _construct()
    {
        $this->_init('score/oggetto_option_value');
    }

    public function addValue($value)
    {
        $this->_values[] = $value;
        return $this;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function setValues($values)
    {
        $this->_values = $values;
        return $this;
    }

    public function unsetValues()
    {
        $this->_values = array();
        return $this;
    }

    public function setOption(Shaurmalab_Score_Model_Oggetto_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    public function unsetOption()
    {
        $this->_option = null;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function setOggetto($oggetto)
    {
        $this->_oggetto = $oggetto;
        return $this;
    }

    public function getOggetto()
    {
        if (is_null($this->_oggetto)) {
            $this->_oggetto = $this->getOption()->getOggetto();
        }
        return $this->_oggetto;
    }

    public function saveValues()
    {
        foreach ($this->getValues() as $value) {
            $this->setData($value)
                ->setData('option_id', $this->getOption()->getId())
                ->setData('store_id', $this->getOption()->getStoreId());

            if ($this->getData('option_type_id') == '-1') {//change to 0
                $this->unsetData('option_type_id');
            } else {
                $this->setId($this->getData('option_type_id'));
            }

            if ($this->getData('is_delete') == '1') {
                if ($this->getId()) {
                    $this->deleteValues($this->getId());
                    $this->delete();
                }
            } else {
                $this->save();
            }
        }//eof foreach()
        return $this;
    }

    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return float|int
     */
    public function getPrice($flag=false)
    {
        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getOption()->getOggetto()->getFinalPrice();
            $price = $basePrice*($this->_getData('price')/100);
            return $price;
        }
        return $this->_getData('price');
    }

    /**
     * Enter description here...
     *
     * @param Shaurmalab_Score_Model_Oggetto_Option $option
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Option_Value_Collection
     */
    public function getValuesCollection(Shaurmalab_Score_Model_Oggetto_Option $option)
    {
        $collection = Mage::getResourceModel('score/oggetto_option_value_collection')
            ->addFieldToFilter('option_id', $option->getId())
            ->getValues($option->getStoreId());

        return $collection;
    }

    public function getValuesByOption($optionIds, $option_id, $store_id)
    {
        $collection = Mage::getResourceModel('score/oggetto_option_value_collection')
            ->addFieldToFilter('option_id', $option_id)
            ->getValuesByOption($optionIds, $store_id);

        return $collection;
    }

    public function deleteValue($option_id)
    {
        $this->getResource()->deleteValue($option_id);
        return $this;
    }

    public function deleteValues($option_type_id)
    {
        $this->getResource()->deleteValues($option_type_id);
        return $this;
    }

    /**
     * Prepare array of option values for duplicate
     *
     * @return array
     */
    public function prepareValueForDuplicate()
    {
        $this->setOptionId(null);
        $this->setOptionTypeId(null);

        return $this->__toArray();
    }

    /**
     * Duplicate oggetto options value
     *
     * @param int $oldOptionId
     * @param int $newOptionId
     * @return Shaurmalab_Score_Model_Oggetto_Option_Value
     */
    public function duplicate($oldOptionId, $newOptionId)
    {
        $this->getResource()->duplicate($this, $oldOptionId, $newOptionId);
        return $this;
    }
}