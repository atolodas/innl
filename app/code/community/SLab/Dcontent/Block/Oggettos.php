<?php

/**
 * Block for displaying product block
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Oggettos extends Shaurmalab_Score_Block_Oggetto_Abstract implements Mage_Widget_Block_Interface
{
    public function getTemplateForOggetto($oggetto, $kind, $part = 'product')
    {
        $key = $oggetto->getAttributeSetId() . '.' . $kind;
        if (Mage::registry($key)) {
            $template = unserialize(Mage::registry($key));
            if (!is_object($template) && empty($template)) return false;
        } else {
            $storeId = Mage::app()->getStore()->getId();
            $templates = Mage::getModel('dcontent/templates')->getCollection()->addFieldToFilter('type', $oggetto->getAttributeSetId())->addFieldToFilter('kind', array(array('like' => '%,' . $kind . ',%'), array('like' => '%,' . $kind), array('like' => $kind . ',%'), array('like' => $kind)))
                ->addFieldToFilter('store_id', array(array('like' => '%,' . $storeId . ',%'), array('like' => '%,' . $storeId), array('like' => $storeId . ',%'), array('like' => $storeId)));

            if (!count($templates)) {
                $templates = Mage::getModel('dcontent/templates')->getCollection()->addFieldToFilter('type', $oggetto->getAttributeSetId())->addFieldToFilter('kind', array(array('like' => '%,' . $kind . ',%'), array('like' => '%,' . $kind), array('like' => $kind . ',%'), array('like' => $kind)))
                    ->addFieldToFilter('store_id', array(array('like' => '0'), array('like' => '0,%'), array('like' => '')));

            }
		if (!count($templates)) {
                $templates = Mage::getModel('dcontent/templates')->getCollection()->addFieldToFilter('type', $oggetto->getAttributeSetId())->addFieldToFilter('kind', array(array('like' => '%,' . $kind . ',%'), array('like' => '%,' . $kind), array('like' => $kind . ',%'), array('like' => $kind)))
		;
            }
            if (!count($templates)) {
                if(!Mage::registry($oggetto->getAttributeSetId() . '.' . $kind)) Mage::register($oggetto->getAttributeSetId() . '.' . $kind, serialize(array()));
                return false;
            }
            $template = $templates->getFirstItem();
            Mage::register($key, serialize($template));
        }


        $processor = Mage::helper('dcontent')->getCustomProcessor();
//	$processor = Mage::helper('dcontent')->addVariablesToProcessor($processor);

        /*
        TODO: add all attributes from templete to collection
          if(Mage::registry('current_layer') && $collection =  Mage::registry('current_layer')->getOggettoCollection()) {
            $attributes = array();
            while (preg_match_all('/^(.*)\[\[([a-zA-Z0-9\.\(\)\ \-\_]*)\]\](.*)/i', $template->getProduct(), $matches)) {
                      foreach ($matches[2] as $attribute_text) {
                        if (preg_match_all('/^(.*)\.format\((.*)\)/i', $attribute_text, $format)) {
                          $attributes[] = trim($format[1][0]);
                        } else {
                          $attributes[] = $attribute_text;
                        }
                      }
            }
            $collection->addAttributeToSelect($attributes);
        }
        */

        $template = $this->replacePatterns($oggetto, $template, $processor, null, $part);
        if(Mage::helper('constructor')->isSuperAdmin()) {
            $template.=Mage::getBlockSingleton('core/template')->setTemplate('constructor/adminmode.phtml')->setOggetto($oggetto)->toHtml();
        }
        return $template;
    }

    public function previewTemplate($oggetto,$template) { 
        $processor = Mage::helper('dcontent')->getCustomProcessor();
        return $this->replacePatterns($oggetto, $template, $processor, null); // TODO: create 3 columns preview for object
    }

    public function getTemplateForCustomer($customer, $kind, $groupId = 0)
    {
        $key = 'customer.' . $kind.'.'.$groupId;
        if (Mage::app()->getCache()->load($key)) {
            $template = unserialize(Mage::app()->getCache()->load($key));
            if (!is_object($template) && empty($template)) return false;
        } else {
            $storeId = Mage::app()->getStore()->getId();
            $templates = Mage::getModel('dcontent/templates')->getCollection()
                ->addFieldToFilter('kind', array('like' => '%' . $groupId . '_' . $kind . '%'))
                ->addFieldToFilter('store_id', array(array('like' => '%,' . $storeId . ',%'), array('like' => '%,' . $storeId), array('like' => $storeId . ',%'), array('like' => $storeId)));

            if (!count($templates)) {
                $templates = Mage::getModel('dcontent/templates')->getCollection()
                    ->addFieldToFilter('kind', array('like' => '%' . $kind . '%'))
                    ->addFieldToFilter('store_id', array(array('like' => '%,' . $storeId . ',%'), array('like' => '%,' . $storeId), array('like' => $storeId . ',%'), array('like' => $storeId)));
            }
            if (!count($templates)) {
                $templates = Mage::getModel('dcontent/templates')->getCollection()
                    ->addFieldToFilter('kind', array(array('like' => '%' . $groupId . '_' . $kind . '%'), array('like' => '%' . $kind . '%')))
                    ->addFieldToFilter('store_id', array(array('like' => '0'), array('like' => '0,%'), array('like' => '')));
            }
            if (!count($templates)) {
                Mage::register($kind, serialize(array()));
                return false;
            }
            $template = $templates->getFirstItem();
            Mage::app()->getCache()->save($key, serialize($template));
        }


        $processor = Mage::helper('dcontent')->getCustomProcessor();
        return $this->replacePatternsCustomer($customer, $template, $processor);
    }

    public function replacePatternsCustomer($_products, $template, $processor, $block = null)
    {
        $output = '';
        $search = array("\n", "\r");
        $i = 1;
        $per_line = 1;
        $_collectionSize = count($_products);

        $preProcessor = Mage::getModel('widget/template_filter');

        if ($block) {
            $per_line = $block->getOptions()->getProductsPerLine();
        }
        if ($per_line == 0) {
            $per_line = 100000;
        }
        if (!is_array($_products)) $_products = array($_products);

        foreach ($_products as $product) :
            if (is_array($product)) {
                $_product = Mage::getModel('score/customer')->load($product['id']);
            } elseif (is_object($product)) {
                $_product = $product;
            }
            if ($_product->getId()) {

                $product_template = str_replace($search, "", $template->getProduct());
                $processor = Mage::helper('dcontent')->addVariablesToProcessor($processor);

                while (preg_match_all('/^(.*)\[\[([a-zA-Z0-9\.\(\)\ \-\_]*)\]\](.*)/i', $product_template, $matches)) {
                    foreach ($matches[2] as $attribute_text) {
                        $format_type = array();
                        if (preg_match_all('/^(.*)\.format\((.*)\)/i', $attribute_text, $format)) {
                            $format_type = explode(' ', $format[2][0]);
                            $attribute = trim($format[1][0]);
                        } else {
                            $attribute = $attribute_text;
                        }

                        if ($_product->getData($attribute) && $attribute != 'username') {
                            $attr = Mage::getResourceSingleton('customer/customer')->getAttribute($attribute);
                            if ($attr->usesSource()) {
                                $options = $attr->getSource()->getAllOptions();
                                $value = explode(',', $_product->getData($attribute));
                                $newVal = array();
                                foreach ($options as $option):
                                    if (in_array($option['value'], $value)) {
                                        $newVal[] = $option['label'];
                                    }
                                endforeach;
                                $replace = implode(', ', $newVal);
                                if ($replace == '' && $_product->getData($attribute) == 1) $replace = 'Yes';
                                else if ($replace == '' && $_product->getData($attribute) == 0) $replace = 'No';
                            } else {
                                $replace = $_product->getData($attribute);
                            }
                        } else {
                            $arr = explode('_', $attribute);
                            $str = '';
                            foreach ($arr as $part) {
                                $str .= ucfirst($part);
                            }
                            $replace = 'get' . $str;
                            $replace = $_product->$replace();
                        }

                        if (!empty($format_type)) {
                            foreach ($format_type as $format) {
                                $replace = Mage::helper('dcontent')->formatValue($replace, $format, $_product, $attribute);
                            }
                        }
                        $product_template = str_replace('[[' . $attribute_text . ']]', $replace, $product_template);
                    }
                }
                $template = $processor->filter($product_template);
                $product_template = $preProcessor->filter($template);

                $output .= $product_template; //$processor->filter($product_template);
                if ($i % $per_line != 0 && $i != $_collectionSize) :
                    $output .= $template->getSeparator();
                endif;
                if ($i % $per_line == 0 && $i != $_collectionSize) :
                    $output .= '
							<div style="clear:both;">&nbsp;</div>';
                endif;
                $i++;
            }
        endforeach;
        return $output;
    }

    /**
     * Display product block
     *
     * @return string html
     */
    protected function _toHtml()
    {
        try {
            $output = '';
            $id = $this->getData('block_id');
            $template = $this->getData('template_id');
            if (empty($id) || empty($template)) {
                return $output;
            }

            $template = Mage::getModel('dcontent/templates')->load($template);
            $block = Mage::getModel('dcontent/oggettos')->getBlockById($id);
            if ($block->getOptions()->getStatus() != '2') {

                $products = $block->getOggettos();

                $content = $block->getOptions()->getTitle();
                $processor = Mage::helper('dcontent')->getCustomProcessor();
                $preProcessor = Mage::getModel('core/email_template_filter');
                $output .= $processor->filter($preProcessor->filter($template->getBeforeProducts()));
                $_collectionSize = count($products);

                if ($_collectionSize > 0) :
                    $_products = array();
                    foreach ($products as $id => $product) {
                        $_products[$id]['id'] = $id;
                        //$product->getId();
                        $_products[$id]['position'] = $product;
                        $position[$id] = $_products[$id]['position'];
                        $ids[$id] = $id;
                    }

                    array_multisort($position, SORT_ASC, $_products, $ids);

                    $output .= $this->replacePatterns($_products, $template, $processor, $block);
                    $output .= $processor->filter($preProcessor->filter($template->getAfterProducts()));
                endif;
                return $processor->filter($output);
            } else {
                return $output;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function replacePatterns($_products, $template, $processor, $block = null, $part = 'product')
    {
        $output = '';
        $search = array("\n", "\r");
        $i = 1;
        $per_line = 1;
        $_collectionSize = count($_products);

        $preProcessor = Mage::getModel('widget/template_filter');

        if ($block) {
            $per_line = $block->getOptions()->getProductsPerLine();
        }
        if ($per_line == 0) {
            $per_line = 100000;
        }
        if (!is_array($_products)) $_products = array($_products);
        $product_template = str_replace($search, "", $template->getData($part));
        foreach ($_products as $product):
            $product_template1 = $product_template;
            if (is_array($product)) {
                $_product = Mage::getModel('score/oggetto')->load($product['id']);
            } elseif (is_object($product)) {
                $_product = $product;//Mage::getModel('score/oggetto')->load($product->getId());
            }
            if (is_object($template) && $_product->getId()) {

                if (Mage::registry('current_oggetto')) Mage::unregister('current_oggetto');
                Mage::register('current_oggetto', $_product);
                $processor = Mage::helper('dcontent')->addVariablesToProcessor($processor);

                while (preg_match_all('/^(.*)\[\[([a-zA-Z0-9\.\(\)\ \-\_]*)\]\](.*)/i', $product_template1, $matches)) {
                    foreach ($matches[2] as $attribute_text) {
                        $format_type = array();
                        if (preg_match_all('/^(.*)\.format\((.*)\)/i', $attribute_text, $format)) {
                            $format_type = explode(' ', $format[2][0]);
                            $attribute = trim($format[1][0]);
                        } else {
                            $attribute = $attribute_text;
                        }

                        if ($attribute != 'entity_id' && Mage::helper('score/oggetto')->isRelatedAttribute($attribute) && !in_array('id', $format_type)) {
                            $id = Mage::helper('score/oggetto')->isRelatedAttribute($attribute);
                            $oggetto = Mage::getModel('score/oggetto')->getAvailableObjects($id)->addAttributeToFilter('entity_id', $_product->getData($attribute))->getFirstItem();
                            $replace = $oggetto->getTitle() . $oggetto->getName();
                        } elseif($isDict = Mage::helper('score/oggetto')->isDictionaryAttribute($attribute)) { 

                            $replace = Mage::helper('score/dictionary')->getTextValue($isDict, $_product->getData($attribute));

                        } else {
                            if ($_product->getData($attribute)) {
                                $attr = Mage::getResourceSingleton('score/oggetto')->getAttribute($attribute);
                                if ($attr->usesSource()) {
                                    $options = $attr->getSource()->getAllOptions();
                                    $value = explode(',', $_product->getData($attribute));
                                    $newVal = array();
                                    foreach ($options as $option):
                                        if (in_array($option['value'], $value)) {
                                            $newVal[] = $option['label'];
                                        }
                                    endforeach;
                                    $replace = implode(', ', $newVal);
                                    if ($replace == '' && $_product->getData($attribute) == 1) $replace = 'Yes';
                                    else if ($replace == '' && $_product->getData($attribute) == 0) $replace = 'No';
                                } else {
                                    $replace = $_product->getData($attribute);
                                }
                            } elseif (substr_count($attribute, '_') != 0) {
                                $arr = explode('_', $attribute);
                                $str = '';
                                foreach ($arr as $part) {
                                    $str .= ucfirst($part);
                                }
                                $function = 'get' . $str;
                                $replace = $_product->$function();
                                if (!$replace) {
                                    try {
                                        $function = lcfirst($str);
                                        $replace = $_product->$function();
                                    } catch (Exception $e) {
                                    }
                                }
                            } else {
                                $replace = '';
                            }
                        }
                        if (!empty($format_type)) {
                            foreach ($format_type as $format) {
                                $replace = Mage::helper('dcontent')->formatValue($replace, $format, $_product, $attribute);
                            }
                        }

                        $product_template1 = str_replace('[[' . $attribute_text . ']]', $replace, $product_template1);

                    }
                }
                $template1 = $processor->filter($product_template1);
                $product_template1 = $preProcessor->filter($template1);

                $output .= $product_template1; //$processor->filter($product_template);
                if ($i % $per_line != 0 && $i != $_collectionSize) :
                endif;
                if ($i % $per_line == 0 && $i != $_collectionSize) :
                    $output .= '
							<div style="clear:both;">&nbsp;</div>';
                endif;
                $i++;
            }
        endforeach;
        if (Mage::registry('current_oggetto')) Mage::unregister('current_oggetto');
        return $output;
    }
}
