<?php

class Snowcommerce_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    function textWrapper($text, $wordCount = 50, $delimiter = '...', $seoContent = false)
    {
        $result['short'] = '';
        $words = explode(' ',$text);
        foreach($words as $key=>$word)
        {
            if($key < $wordCount) $result['short'] .= $word.' ';
        }
        $result['short'] .= $delimiter;
        $result['long'] = $text;
        return $result;
    }

    public function replacePatterns($data) {
        $search=array("\n", "\r");
        $data = str_replace($search,"", $data);

        $processor = Mage::getModel('core/email_template_filter');
        $filters = Mage::getBlockSingleton('catalog/layer_state')->getActiveFilters();
        $active = array();
        foreach($filters as $filter) {
            $active[$filter->getVar()] = trim(strip_tags($filter->getLabel()));
        }

        $category = Mage::registry('current_category');
        $product =  Mage::registry('current_product');
		$oggetto = Mage::registry('current_oggetto');
        $obj = '';
        if(is_object($product) && $product->getId()) {
            $obj = $product;
        } elseif(is_object($category) && $category->getId()) {
            $obj = $category;
        } else {
        	$obj = $oggetto;
			
        }
        while(preg_match_all('/^(.*)\{\{([a-zA-Z0-9\.\(\)\-\_]*)\}\}(.*)/i',$data,$matches)) {
            foreach($matches[2] as $attribute_text) {
                $replace = '';

                if($attribute_text != 'entity_id' && Mage::helper('score/oggetto')->isRelatedAttribute($attribute_text)) {
                    $id = Mage::helper('score/oggetto')->isRelatedAttribute($attribute_text);
                    $oggetto = Mage::getModel('score/oggetto')->getAvailableObjects($id)->addAttributeToFilter('entity_id',$obj->getData($attribute_text))->getFirstItem();
                    $replace = $oggetto->getTitle().$oggetto->getName();
                } elseif(in_array($attribute_text,array_keys($active))) {
                    $replace = $active[$attribute_text];
                } else {
                    if(is_object($obj)) {
                        $replace = $obj->getData($attribute_text);
                    }

                    if(!$replace && $attribute_text=='all_attributes') {
                        $replace = implode(' ',$active);
                    }

                    if(!$replace && $attribute_text=='default') {
                        $replace = Mage::app()->getLayout()->getBlock('head')->getData($attribute_text);
					}

                    if(!$replace) {
                        $replace = Mage::app()->getRequest()->getParam($attribute_text);
						
						
						if ($attribute_text == 'ptype')
						
						{
						$attribute = Mage::getModel('eav/config')->getAttribute(4,'ptype'); // 4 - means that it's a Product attribute // also you can try ->getAttribute('catalog_product','type');
							
							if ($attribute->usesSource()) {
							
							$options = $attribute->getSource()->getAllOptions(false);
							
							
								foreach ($options as $value) {
									if($replace == $value['value']) {
									$replace = $value['label'];
									}
								}
								
							}
						
					    }
					}
				}
                $data = str_replace('{{'.$attribute_text.'}}',$replace,$data);

            }

        }

        $data = $processor->filter($data);
        return $data;
    }
}