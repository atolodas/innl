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
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Swms_Optionimage_Block_Html_Select extends Mage_Core_Block_Html_Select
{
    private $_mageVersion = 13;

    /**
     * Add an option to HTML select
     *
     * @param string $value  HTML value
     * @param string $label  HTML label
     * @param string $title  HTML title
     * @param string $image  HTML image
     * @param array  $params HTML attributes
     * @return Mage_Core_Block_Html_Select
     */
    public function addOption($value, $label, $title, $image, $params=array())
    {
        $this->_options[] = array('value'=>$value, 'label'=>$label, 'title'=>$title, 'image'=>$image, 'params' => $params);
        return $this;
    }
    
    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        $helper = $this->helper('optionimage');
        $this->_mageVersion = $helper->checkMagentoVersion();
        $html = '<select name="'.$this->getName().'" id="'.$this->getId().'" class="'
            .$this->getClass().'" title="'.$this->getTitle().'" '.$this->getExtraParams().'>';
        $values = $this->getValue();

        if (!is_array($values)){
            if (!is_null($values)) {
                $values = array($values);
            } else {
                $values = array();
            }
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            $isArrayOption = true;
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = (string)$option['label'];
                $title = (string)$option['title'];
                $image = (string)$option['image'];
                $params = (!empty($option['params'])) ? $option['params'] : array();
            }
            else {
                $value = (string)$key;
                $label = (string)$option;
                $title = (string)$option['title'];
                $image = (string)$option['image'];
                $isArrayOption = false;
                $params = array();
            }

            if (is_array($value)) {
                $html.= '<optgroup label="'.$label.'">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = array(
                            'value' => $keyGroup,
                            'label' => $optionGroup,
                            'title' => $option['title'],
                            'image' => $option['image'],
                            'params' => (!empty($option['params'])) ? $option['params'] : array()
                        );
                    }
                    $html.= $this->_optionToHtml(
                        $optionGroup,
                        in_array($optionGroup['value'], $values)
                    );
                }
                $html.= '</optgroup>';
            } else {
               $html.= $this->_optionToHtml(array(
                    'value' => $value,
                    'label' => $label,
                    'title' => $title,
                    'image' => $image,
                    'params' => $params
                ),
                    in_array($value, $values)
                );
            }
        }
        $html.= '</select>';
        return $html;
    }

    /**
     * Return option HTML node
     *
     * @param array $option
     * @param boolean $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected=false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        if($this->_mageVersion >= 15) {
            if (!empty($option['params']) && is_array($option['params'])) {
                foreach ($option['params'] as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $keyMulti => $valueMulti) {
                            $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                        }
                    } else {
                        $params .= sprintf(' %s="%s" ', $key, $value);
                    }
                }
            }
            if($this->_mageVersion >= 17) {
                $html =  sprintf('<option value="%s"%s %s %s %s>%s</option>',
                    $this->escapeHtml($option['value']),
                    $selectedHtml,
                    'image="'.$this->escapeHtml($option['image']).'"',
                    'title="'.$this->escapeHtml($option['title']).'"',
                    $params,
                    $this->escapeHtml($option['label']));
            }
            else {
                $html = sprintf('<option value="%s"%s %s %s %s>%s</option>',
                    $this->htmlEscape($option['value']),
                    $selectedHtml,
                    'image="'.$this->htmlEscape($option['image']).'"',
                    'title="'.$this->htmlEscape($option['title']).'"',
                    $params,
                    $this->htmlEscape($option['label']));
            }
        }
        else {
            $html = '<option title="'.$this->htmlEscape($option['title']).'"'.
                        ' value="'.$this->htmlEscape($option['value']).'"'.
                        $selectedHtml.
                        'image="'.$this->htmlEscape($option['image']).'">'.
                        'title="'.$this->htmlEscape($option['title']).'"'.
                        $this->htmlEscape($option['label']).'</option>';
        }
        return $html;
    }

}