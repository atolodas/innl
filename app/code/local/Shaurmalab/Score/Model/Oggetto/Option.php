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
 * Score oggetto option model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Option _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Option getResource()
 * @method int getOggettoId()
 * @method Shaurmalab_Score_Model_Oggetto_Option setOggettoId(int $value)
 * @method string getType()
 * @method Shaurmalab_Score_Model_Oggetto_Option setType(string $value)
 * @method int getIsRequire()
 * @method Shaurmalab_Score_Model_Oggetto_Option setIsRequire(int $value)
 * @method string getSku()
 * @method Shaurmalab_Score_Model_Oggetto_Option setSku(string $value)
 * @method int getMaxCharacters()
 * @method Shaurmalab_Score_Model_Oggetto_Option setMaxCharacters(int $value)
 * @method string getFileExtension()
 * @method Shaurmalab_Score_Model_Oggetto_Option setFileExtension(string $value)
 * @method int getImageSizeX()
 * @method Shaurmalab_Score_Model_Oggetto_Option setImageSizeX(int $value)
 * @method int getImageSizeY()
 * @method Shaurmalab_Score_Model_Oggetto_Option setImageSizeY(int $value)
 * @method int getSortOrder()
 * @method Shaurmalab_Score_Model_Oggetto_Option setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Option extends Mage_Core_Model_Abstract
{
    /**
     * Option group text
     */
    const OPTION_GROUP_TEXT   = 'text';

    /**
     * Option group file
     */
    const OPTION_GROUP_FILE   = 'file';

    /**
     * Option group select
     */
    const OPTION_GROUP_SELECT = 'select';

    /**
     * Option group date
     */
    const OPTION_GROUP_DATE   = 'date';

    /**
     * Option type field
     */
    const OPTION_TYPE_FIELD     = 'field';

    /**
     * Option type area
     */
    const OPTION_TYPE_AREA      = 'area';

    /**
     * Option group file
     */
    const OPTION_TYPE_FILE      = 'file';

    /**
     * Option type drop down
     */
    const OPTION_TYPE_DROP_DOWN = 'drop_down';

    /**
     * Option type radio
     */
    const OPTION_TYPE_RADIO     = 'radio';

    /**
     * Option type checkbox
     */
    const OPTION_TYPE_CHECKBOX  = 'checkbox';

    /**
     * Option type multiple
     */
    const OPTION_TYPE_MULTIPLE  = 'multiple';

    /**
     * Option type date
     */
    const OPTION_TYPE_DATE      = 'date';

    /**
     * Option type date/time
     */
    const OPTION_TYPE_DATE_TIME = 'date_time';

    /**
     * Option type time
     */
    const OPTION_TYPE_TIME      = 'time';

    /**
     * Oggetto instance
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggetto;

    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Value instance
     *
     * @var Shaurmalab_Score_Model_Oggetto_Option_Value
     */
    protected $_valueInstance;

    /**
     * Values
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_option');
    }

    /**
     * Add value of option to values array
     *
     * @param Shaurmalab_Score_Model_Oggetto_Option_Value $value
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function addValue(Shaurmalab_Score_Model_Oggetto_Option_Value $value)
    {
        $this->_values[$value->getId()] = $value;
        return $this;
    }

    /**
     * Get value by given id
     *
     * @param int $valueId
     * @return Shaurmalab_Score_Model_Oggetto_Option_Value
     */
    public function getValueById($valueId)
    {
        if (isset($this->_values[$valueId])) {
            return $this->_values[$valueId];
        }

        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Retrieve value instance
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option_Value
     */
    public function getValueInstance()
    {
        if (!$this->_valueInstance) {
            $this->_valueInstance = Mage::getSingleton('score/oggetto_option_value');
        }
        return $this->_valueInstance;
    }

    /**
     * Add option for save it
     *
     * @param array $option
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function addOption($option)
    {
        $this->_options[] = $option;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options for array
     *
     * @param array $options
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options to empty array
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function unsetOptions()
    {
        $this->_options = array();
        return $this;
    }

    /**
     * Retrieve oggetto instance
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        return $this->_oggetto;
    }

    /**
     * Set oggetto instance
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function setOggetto(Shaurmalab_Score_Model_Oggetto $oggetto = null)
    {
        $this->_oggetto = $oggetto;
        return $this;
    }

    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if (is_null($type)) {
            $type = $this->getType();
        }
        $optionGroupsToTypes = array(
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        );

        return isset($optionGroupsToTypes[$type])?$optionGroupsToTypes[$type]:'';
    }

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return Shaurmalab_Score_Model_Oggetto_Option_Group_Abstract
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);
        if (!empty($group)) {
            return Mage::getModel('score/oggetto_option_type_' . $group);
        }
        Mage::throwException(Mage::helper('score')->__('Wrong option type to get group instance.'));
    }

    /**
     * Save options.
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function saveOptions()
    {
        foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('oggetto_id', $this->getOggetto()->getId())
                ->setData('store_id', $this->getOggetto()->getStoreId());

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');

                    /**
                     * if previous option has different group from one is came now
                     * need to remove all data of previous group
                     */
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();            }
        }//eof foreach()
        return $this;
    }

    /**
     * After save
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $this->getValueInstance()->unsetValues();
        if (is_array($this->getData('values'))) {
            foreach ($this->getData('values') as $value) {
                $this->getValueInstance()->addValue($value);
            }

            $this->getValueInstance()->setOption($this)
                ->saveValues();
        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
            Mage::throwException(Mage::helper('score')->__('Select type options required values rows.'));
        }

        return parent::_afterSave();
    }

    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return decimal
     */
    public function getPrice($flag = false)
    {
        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getOggetto()->getFinalPrice();
            $price = $basePrice * ($this->_getData('price')/100);
            return $price;
        }
        return $this->_getData('price');
    }

    /**
     * Delete prices of option
     *
     * @param int $option_id
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function deletePrices($option_id)
    {
        $this->getResource()->deletePrices($option_id);
        return $this;
    }

    /**
     * Delete titles of option
     *
     * @param int $option_id
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function deleteTitles($option_id)
    {
        $this->getResource()->deleteTitles($option_id);
        return $this;
    }

    /**
     * get Oggetto Option Collection
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Option_Collection
     */
    public function getOggettoOptionCollection(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('oggetto_id', $oggetto->getId())
            ->addTitleToResult($oggetto->getStoreId())
            ->addPriceToResult($oggetto->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc');

        if ($this->getAddRequiredFilter()) {
            $collection->addRequiredFilter($this->getAddRequiredFilterValue());
        }

        $collection->addValuesToResult($oggetto->getStoreId());
        return $collection;
    }

    /**
     * Get collection of values for current option
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Option_Value_Collection
     */
    public function getValuesCollection()
    {
        $collection = $this->getValueInstance()
            ->getValuesCollection($this);

        return $collection;
    }

    /**
     * Get collection of values by given option ids
     *
     * @param array $optionIds
     * @param int $store_id
     * @return unknown
     */
    public function getOptionValuesByOptionId($optionIds, $store_id)
    {
        $collection = Mage::getModel('score/oggetto_option_value')
            ->getValuesByOption($optionIds, $this->getId(), $store_id);

        return $collection;
    }

    /**
     * Prepare array of options for duplicate
     *
     * @return array
     */
    public function prepareOptionForDuplicate()
    {
        $this->setOggettoId(null);
        $this->setOptionId(null);
        $newOption = $this->__toArray();
        $_values = $this->getValues();
        if ($_values) {
            $newValuesArray = array();
            foreach ($_values as $_value) {
                $newValuesArray[] = $_value->prepareValueForDuplicate();
            }
            $newOption['values'] = $newValuesArray;
        }

        return $newOption;
    }

    /**
     * Duplicate options for oggetto
     *
     * @param int $oldOggettoId
     * @param int $newOggettoId
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    public function duplicate($oldOggettoId, $newOggettoId)
    {
        $this->getResource()->duplicate($this, $oldOggettoId, $newOggettoId);

        return $this;
    }

    /**
     * Retrieve option searchable data
     *
     * @param int $oggettoId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($oggettoId, $storeId)
    {
        return $this->_getResource()->getSearchableData($oggettoId, $storeId);
    }

    /**
     * Clearing object's data
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    protected function _clearData()
    {
        $this->_data = array();
        $this->_values = array();
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return Shaurmalab_Score_Model_Oggetto_Option
     */
    protected function _clearReferences()
    {
        if (!empty($this->_values)) {
            foreach ($this->_values as $value) {
                $value->unsetOption();
            }
        }
        return $this;
    }

    /**
     * Check whether custom option could have multiple values
     *
     * @return bool
     */
    public function isMultipleType()
    {
        switch ($this->getType()) {
            case self::OPTION_TYPE_MULTIPLE:
            case self::OPTION_TYPE_CHECKBOX:
                return true;
        }
        return false;
    }
}
