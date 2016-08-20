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
 * Product options text type block
 */
class Swms_Optionimage_Block_Product_View_Options_Type_Select
    extends Mage_Catalog_Block_Product_View_Options_Type_Select
{

    private $_mageVersion = 13;
    private $_mage13notactiv = false;
    /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml()
    {
        if(!$this->helper('optionimage')->isActiv()) {
            return parent::getValuesHtml();
        }

        $helper = $this->helper('optionimage');
        $this->_mageVersion = $helper->checkMagentoVersion();
        $_option = $this->getOption();
        if($this->_mageVersion >= 15) {
            $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
            $store = $this->getProduct()->getStore();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            if($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN &&
                    !$this->helper('optionimage')->isActivDropdown()) {
                if($this->_mageVersion > 13) {
                    return parent::getValuesHtml();
                }
                else {
                    $this->_mage13notactiv = true;
                }
            }
            elseif ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE &&
                    !$this->helper('optionimage')->isActivMultiple()) {
                if($this->_mageVersion > 13) {
                    return parent::getValuesHtml();
                }
                else {
                    $this->_mage13notactiv = true;
                }
            }
            $extraParams = '';
            if($this->_mageVersion > 13) {
                $select = $this->getLayout()->createBlock('core/html_select')
                    ->setData(array(
                        'id' => 'select_'.$_option->getId(),
                        'class' => $require.' product-custom-option showoptionimage'
                    ));
            }
            else {
                $this->setData(array(
                        'id' => 'select_'.$_option->getId(),
                        'class' => $require.' product-custom-option showoptionimage'
                    ));
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                if($this->_mageVersion > 13) {
                    $select->setName('options['.$_option->getid().']');
                    $select->addOption('', $this->__('-- Please Select --'),'');
                }
                else {
                    $this->setName('options['.$_option->getid().']');
                    if($this->_mage13notactiv)
                        $this->setClass2($require.' product-custom-option');
                    else
                        $this->setClass2($require.' product-custom-option showoptionimage');

                    $this->addOption2('', $this->__('-- Please Select --'),'',array());
               }
            } else {
                if($this->_mageVersion > 13) {
                    $select->setName('options['.$_option->getid().'][]');
                    $select->setClass('multiselect'.$require.' product-custom-option showoptionimage');
                }
                else {
                    $this->setName('options['.$_option->getid().'][]');
                    if($this->_mage13notactiv)
                        $this->setClass2('multiselect'.$require.' product-custom-option');
                    else
                        $this->setClass2('multiselect'.$require.' product-custom-option showoptionimage');
                }
            }
            foreach ($_option->getValues() as $_value) {
                $filename = $_value->getTitle();
                if(strcmp('sku',$helper->getSubfolderValue()) == 0){
                    $filename = $this->getProduct()->getSku()."/".$filename;
                }
                elseif(strcmp('optiontitle',$helper->getSubfolderValue()) == 0){
                    $filename = $_option->getTitle()."/".$filename;
                }
                elseif(strcmp('skuoptiontitle',$helper->getSubfolderValue()) == 0){
                    $filename = $this->getProduct()->getSku()."/".$_option->getTitle()."/".$filename;
                }
                $filename = $helper->overrideFilename($filename);
                $fileurl = $helper->getImageUrl($filename);

                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent'),// ? true : false,
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                ), false);

                if($helper->isDisplayText() && strcmp('onlyimage',$helper->getDisplayorder()) != 0){
                    $displayText = $_value->getTitle() . ' ' . $priceStr . '';
                }
                else {
                    $displayText = ' ' . $priceStr;
                }

                if($this->_mageVersion >= 15) {
                    $select->addOption(
                        $_value->getOptionTypeId(),
                        $displayText,
                        $_value->getTitle(),
                        $fileurl,
                        array('price' => $this->helper('core')->currencyByStore($_value->getPrice($_value->getPriceType() == 'percent'), $store, false))
                    );
                }
                else if ($this->_mageVersion == 14) {
                    $select->addOption(
                        $_value->getOptionTypeId(),
                        $displayText,
                        $_value->getTitle(),
                        $fileurl,
                        array('price' => $this->helper('core')->currencyByStore($_value->getPrice($_value->getPriceType() == 'percent'), $store, false))
                    );

                }
                else {//$this->_mageVersion <= 13
                    $this->addOption2(
                        $_value->getOptionTypeId(),
                        $displayText,
                        $_value->getTitle(),
                        $fileurl,
                        array('price' => $_value->getPrice($_value->getPriceType() == 'percent'))
                    );
                }
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"' . 'style="display:block;"' . ' displayorder="'.$helper->getDisplayorder().'"';
            }
            else {
                $extraParams = ' displayorder="'.$helper->getDisplayorder().'"';
            }

            if($this->_mageVersion >= 15) {
                if (!$this->getSkipJsReloadPrice()) {
                    $extraParams .= ' onchange="opConfig.reloadPrice()"';
                }
                $select->setExtraParams($extraParams);

                if ($configValue) {
                    $select->setValue($configValue);
                }
                return $select->getHtml();
            }
            else if($this->_mageVersion == 14) {
                $select->setExtraParams('onchange="opConfig.reloadPrice()"'.$extraParams);
                return $select->getHtml();

            }
            else {//$this->_mageVersion <= 13
                $this->setExtraParams('onchange="opConfig.reloadPrice()"'.$extraParams);
                return $this->getHtml2();

            }
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
        )
        {
            if($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO &&
                    !$this->helper('optionimage')->isActivRadio()) {
                return parent::getValuesHtml();
            }
            elseif ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX &&
                    !$this->helper('optionimage')->isActivCheckbox()) {
                return parent::getValuesHtml();
            }
            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$_option->getIsRequire()) {
                        if($this->_mageVersion >= 15) {
                            $selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
                        }
                        else {
                            $selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']" onclick="opConfig.reloadPrice()" value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
                        }
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
            foreach ($_option->getValues() as $_value) {
                $count++;
                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent'),// ? true : false,
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                ));
                if($this->_mageVersion >= 15) {
                    $htmlValue = $_value->getOptionTypeId();
                    if ($arraySign) {
                        $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
                    } else {
                        $checked = $configValue == $htmlValue ? 'checked' : '';
                    }
                }

                $filename = $_value->getTitle();
                if(strcmp('sku',$helper->getSubfolderValue()) == 0){
                    $filename = $this->getProduct()->getSku()."/".$filename;
                }
                elseif(strcmp('optiontitle',$helper->getSubfolderValue()) == 0){
                    $filename = $_option->getTitle()."/".$filename;
                }
                elseif(strcmp('skuoptiontitle',$helper->getSubfolderValue()) == 0){
                    $filename = $this->getProduct()->getSku()."/".$_option->getTitle()."/".$filename;
                }
                $filename = $helper->overrideFilename($filename);
                $fileurl = $helper->getImageUrl($filename);

                $selectHtml .= '<li>';
                if($this->_mageVersion >= 15) {
                    $selectHtml .= '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice($_value->getPriceType() == 'percent'), $store, false) . '" />';
                }
                else {
                    $selectHtml .= '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" onclick="opConfig.reloadPrice()" name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="'.$_value->getOptionTypeId().'" />';
                }
                $selectHtml .= '<span class="label">';
                if($helper->isFileExists($filename)) {
                    $optionimageHtml = "";
                    if($this->helper('optionimage')->useDefaultSize()) {
                        $width = $helper->getDefaultWidth();
                        $height = $helper->getDefaultHeight();
                        $optionimageHtml =
                                    '<img class="optionimage" src="'.$fileurl.'" title="'.$_value->getTitle().'" alt="'.$_value->getTitle().'" style="width:'.$width.'px; height:'.$height.'px;"/>';
                    }
                    else {
                        $optionimageHtml =
                                    '<img src="'.$fileurl.'" title="'.$_value->getTitle().'" alt="'.$_value->getTitle().'" />';
                    }

                    if(strcmp('image',$helper->getDisplayorder()) == 0 || strcmp('onlyimage',$helper->getDisplayorder()) == 0) {
                       $selectHtml .= $optionimageHtml;
                    }

                    if($helper->isDisplayText() && strcmp('onlyimage',$helper->getDisplayorder()) != 0){
                        $selectHtml .=
                                   '<label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().' '.$priceStr.'</label>';
                    }
                    else {
                        $selectHtml .=
                                   '<label for="options_'.$_option->getId().'_'.$count.'">'.' '.$priceStr.'</label>';
                    }

                    if(strcmp('text',$helper->getDisplayorder()) == 0) {
                       $selectHtml .= $optionimageHtml;
                    }
                }
                else {
                    $selectHtml .=
                        '<label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().' '.$priceStr.'</label>';
                }
                $selectHtml .= '</span>';
                if ($_option->getIsRequire()) {
                    $selectHtml .= '<script type="text/javascript">' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').advaiceContainer = \'options-'.$_option->getId().'-container\';' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').callbackFunction = \'validateOptionsCallback\';' .
                                   '</script>';
                }
                $selectHtml .= '</li>';
            }
            $selectHtml .= '</ul>';
            return $selectHtml;
        }
    }


    /*for Magento 1.3 start*/
    protected $_options2 = array();

    public function getOptions2()
    {
        return $this->_options2;
    }

    public function setOptions2($options)
    {
        $this->_options2 = $options;
        return $this;
    }

    public function addOption2($value, $label, $title, $image, $params=array())
    {
        $this->_options2[] = array('value'=>$value, 'label'=>$label, 'title'=>$title, 'image'=>$image,'params'=>$params);
        return $this;
    }

    public function getHtml2()
    {
        Mage::dispatchEvent('core_block_abstract_to_html_before', array('block' => $this));

        if (Mage::getStoreConfig('advanced/modules_disable_output/'.$this->getModuleName())) {
            return '';
        }

        if (!($html = $this->_loadCache())) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline($this->getData('translate_inline'));
            }

            $this->_beforeToHtml();
            $html = $this->_toHtml2();
            $this->_saveCache($html);

            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline(true);
            }
        }

        $html = $this->_afterToHtml($html);
        Mage::dispatchEvent('core_block_abstract_to_html_after', array('block' => $this));
        return $html;
    }



    protected function _toHtml2()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        $html = '<select name="'.$this->getName().'" id="'.$this->getId().'" class="'
            .$this->getClass2().'" title="'.$this->getTitle().'" '.$this->getExtraParams().'>';
        $values = $this->getValue();

        if (!is_array($values)){
            if (!is_null($values)) {
                $values = array($values);
            } else {
                $values = array();
            }
        }

        $isArrayOption = true;
        foreach ($this->getOptions2() as $key => $option) {
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = $option['label'];
                $title = $option['title'];
                $image = $option['image'];
            }
            else {
                $value = $key;
                $label = $option;
		$title = $option['title'];
		$image = $option['image'];
                $isArrayOption = false;
            }

            if (is_array($value)) {
                $html.= '<optgroup label="'.$label.'">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = array(
                            'value' => $keyGroup,
                            'label' => $optionGroup,
                            'title' => $option['title'],
                            'image' => $option['image']
                        );
                    }
                    $html.= $this->_optionToHtml2(
                        $optionGroup,
                        in_array($optionGroup['value'], $values)
                    );
                }
                $html.= '</optgroup>';
            } else {
                $html.= $this->_optionToHtml2(array(
                    'value' => $value,
                    'label' => $label,
                    'title' => $title,
                    'image' => $image
                ),
                    in_array($value, $values)
                );
            }
        }
        $html.= '</select>';
        return $html;
    }

    protected function _optionToHtml2($option, $selected=false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        $html = '<option title="'.$option['title'].'" value="'.$option['value'].'"'.$selectedHtml.'image="'.$option['image'].'">'.$option['label'].'</option>';

        return $html;
    }
    /*for Magento 1.3 end*/
    public function getDoReloadPrice() {
        //return 1;
        if($this->_mageVersion >= 15) {
            return !$this->getSkipJsReloadPrice();
        }
        else {
            return 1;
        }
    }
}