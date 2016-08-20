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
 * Helper for fetching properties by oggetto configurational item
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Oggetto_Configuration extends Mage_Core_Helper_Abstract
    implements Shaurmalab_Score_Helper_Oggetto_Configuration_Interface
{
    const XML_PATH_CONFIGURABLE_ALLOWED_TYPES = 'global/score/oggetto/type/configurable/allow_oggetto_types';

    /**
     * Retrieves oggetto configuration options
     *
     * @param Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item
     * @return array
     */
    public function getCustomOptions(Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item)
    {
        $oggetto = $item->getOggetto();
        $options = array();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $oggetto->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);

                    if ('file' == $option->getType()) {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }

                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $group->getFormattedOptionValue($itemOption->getValue()),
                        'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }

        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        return $options;
    }

    /**
     * Retrieves configuration options for configurable oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item
     * @return array
     */
    public function getConfigurableOptions(Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item)
    {
        $oggetto = $item->getOggetto();
        $typeId = $oggetto->getTypeId();
        if ($typeId != Shaurmalab_Score_Model_Oggetto_Type_Configurable::TYPE_CODE) {
             Mage::throwException($this->__('Wrong oggetto type to extract configurable options.'));
        }
        $attributes = $oggetto->getTypeInstance(true)
            ->getSelectedAttributesInfo($oggetto);
        return array_merge($attributes, $this->getCustomOptions($item));
    }

    /**
     * Retrieves configuration options for grouped oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item
     * @return array
     */
    public function getGroupedOptions(Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item)
    {
        $oggetto = $item->getOggetto();
        $typeId = $oggetto->getTypeId();
        if ($typeId != Shaurmalab_Score_Model_Oggetto_Type_Grouped::TYPE_CODE) {
             Mage::throwException($this->__('Wrong oggetto type to extract configurable options.'));
        }

        $options = array();
        /**
         * @var Shaurmalab_Score_Model_Oggetto_Type_Grouped
         */
        $typeInstance = $oggetto->getTypeInstance(true);
        $associatedOggettos = $typeInstance->getAssociatedOggettos($oggetto);

        if ($associatedOggettos) {
            foreach ($associatedOggettos as $associatedOggetto) {
                $qty = $item->getOptionByCode('associated_oggetto_' . $associatedOggetto->getId());
                $option = array(
                    'label' => $associatedOggetto->getName(),
                    'value' => ($qty && $qty->getValue()) ? $qty->getValue() : 0
                );

                $options[] = $option;
            }
        }

        $options = array_merge($options, $this->getCustomOptions($item));
        $isUnConfigured = true;
        foreach ($options as &$option) {
            if ($option['value']) {
                $isUnConfigured = false;
                break;
            }
        }
        return $isUnConfigured ? array() : $options;
    }

    /**
     * Retrieves oggetto options list
     *
     * @param Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item
     * @return array
     */
    public function getOptions(Shaurmalab_Score_Model_Oggetto_Configuration_Item_Interface $item)
    {
        $typeId = $item->getOggetto()->getTypeId();
        switch ($typeId) {
            case Shaurmalab_Score_Model_Oggetto_Type_Configurable::TYPE_CODE:
                return $this->getConfigurableOptions($item);
                break;
            case Shaurmalab_Score_Model_Oggetto_Type_Grouped::TYPE_CODE:
                return $this->getGroupedOptions($item);
                break;
        }
        return $this->getCustomOptions($item);
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     * @param array $params
     * All keys are options. Following supported:
     *  - 'maxLength': truncate option value if needed, default: do not truncate
     *  - 'cutReplacer': replacer for cut off value part when option value exceeds maxLength
     *
     * @return array
     */
    public function getFormattedOptionValue($optionValue, $params = null)
    {
        // Init params
        if (!$params) {
            $params = array();
        }
        $maxLength = isset($params['max_length']) ? $params['max_length'] : null;
        $cutReplacer = isset($params['cut_replacer']) ? $params['cut_replacer'] : '...';

        // Proceed with option
        $optionInfo = array();

        // Define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } else if (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // Render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('score/oggetto_option')->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // Truncate standard view
        $result = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            if ($maxLength) {
                $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, $maxLength, '');
            } else {
                $_truncatedValue = $optionValue;
            }
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $result = array('value' => $_truncatedValue);

        if ($maxLength && (Mage::helper('core/string')->strlen($optionValue) > $maxLength)) {
            $result['value'] = $result['value'] . $cutReplacer;
            $optionValue = nl2br($optionValue);
            $result['full_view'] = $optionValue;
        }

        return $result;
    }

    /**
     * Get allowed oggetto types for configurable oggetto
     *
     * @return SimpleXMLElement
     */
    public function getConfigurableAllowedTypes()
    {
        return Mage::getConfig()
                ->getNode(self::XML_PATH_CONFIGURABLE_ALLOWED_TYPES)
                ->children();
    }
}
