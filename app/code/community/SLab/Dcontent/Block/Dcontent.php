<?php
/**
 * Block for displaying product block
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Dcontent extends Mage_Catalog_Block_Product_Abstract  implements Mage_Widget_Block_Interface
{
	/**
	 * Display product block
	 *
	 * @return string html
	 */
	protected function _toHtml()
	{
        try {
		         $output='';
                        $id = $this->getData('block_id');
                        $template = $this->getData('template_id');
                        if (empty($id) || empty($template)) {
                            return $output;
                        }
                        $template = Mage::getModel('dcontent/templates')->load($template);
                        $block = Mage::getModel('dcontent/dcontent')->getBlockById($id);
                        if($block->getOptions()->getStatus()!='2' AND $block->getOptions()->getTitle()!='') {
                            $per_line = $block->getOptions()->getProductsPerLine();
                            if($per_line==0) { $per_line = 100000; }
                            $products = $block->getProducts();
                            $content = $block->getOptions()->getTitle();
                            $processor = Mage::getModel('core/email_template_filter');

                            //$output.= str_replace('{{title}}',$processor->filter($content),$template->getHeader());
                            $output.= $processor->filter($template->getBeforeProducts());
                            $i = 1;
                            $_collectionSize = count($products);

                            if($_collectionSize>0):
                                $_products = array();
                                foreach ($products as $id=>$product){
                                    $_products[$id]['id']=$id;//$product->getId();
                                    $_products[$id]['position']=$product;
                                    $position[$id] = $_products[$id]['position'];
                                    $ids[$id] = $id;
                                }

                                array_multisort($position, SORT_ASC,$_products,$ids);
                                $search=array("\n", "\r");

                                foreach ($_products as $product):
                                    $_product = Mage::getModel('catalog/product')->load($product['id']);
                                    if($_product->getId() && $_product->isVisibleInCatalog() ) {
                                        $product_template = str_replace($search,"", $template->getProduct());
                                        if(!Mage::registry('current_product')) Mage::register('current_product',$_product);
                                        $product_template = $processor->filter($product_template);

                                        while(preg_match_all('/^(.*)\{\{([a-zA-Z0-9\.\(\)\ \-\_]*)\}\}(.*)/i',$product_template,$matches)) {
                                            foreach($matches[2] as $attribute_text) {


                                                $format_type = array();
                                                if(preg_match_all('/^(.*)\.format\((.*)\)/i',$attribute_text,$format)) {
                                                    $format_type = explode(' ',$format[2][0]);
                                                    $attribute = trim($format[1][0]);
                                                } else {
                                                    $attribute = $attribute_text;
                                                }


                                                if($_product->getData($attribute)) {
                                                    if(Mage::getModel('eav/config')->getAttribute('catalog_product',$attribute)->usesSource()) {
                                                        $replace = $_product->getAttributeText($attribute);
                                                    } else {
                                                        $replace = $_product->getData($attribute);

                                                    }

                                                } elseif($attribute=='price_html') {
                                                    if($_product->getTypeId()!='grouped') {
                                                        $design = Mage::getDesign();
                                                        $oldArea = $design->getArea();
                                                        $design->setArea('frontend');
                                                        $replace = 	strip_tags(Mage::getBlockSingleton('core/template')->getLayout()
                                                            ->createBlock('catalog/product_list')->setTemplate('catalog/product/price.phtml')->getPriceHtml($_product,true));
                                                        $design->setArea($oldArea);
                                                    } else {
                                                        $prices = array();
                                                        $associated = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
                                                        foreach($associated as $assoc) {
                                                            $prices[] = $assoc->getPrice();
                                                        }
                                                        $price = min($prices);
                                                        $replace = Mage::helper('core')->formatPrice($price,true);
                                                    }
                                                } elseif(substr_count($attribute,' ')==0) {
                                                    $arr = explode('_',$attribute);
                                                    $str = '';
                                                    foreach($arr as $part) {
                                                        $str.=ucfirst($part);
                                                    }
                                                    $replace = 'get'.$str;
                                                    $replace = $_product->$replace();

                                                } else {
                                                    // nothing to do. we have static block or variable
                                                    $replace = '';
                                                }

                                                if(!empty($format_type)) {
                                                    foreach ($format_type as $format) {
                                                        $replace = Mage::helper('dcontent')->formatValue($replace,$format,$_product,$attribute);
                                                    }
                                                }
                                                $product_template = str_replace('{{'.$attribute_text.'}}',$replace,$product_template);
                                            }

                                        }
                                        $output.=$processor->filter($product_template);
                                        if ($i%$per_line!=0 && $i!=$_collectionSize):
                                            $output.=$template->getSeparator();
                                        endif;
                                        if ($i%$per_line==0 && $i!=$_collectionSize):
                                            $output.='
							<div style="clear:both;">&nbsp;</div>';
                                        endif;
                                        $i++;
                                    }
                                endforeach;

                                $categories = explode(',',$this->getData('categories'));
                                foreach($categories as $category) {
                                    $category = Mage::getModel('catalog/category')->load($category);
                                    $cat_template = str_replace($search,"", $template->getCategory());
                                    //$product_template = $processor->filter(&$product_template);
                                    while(preg_match_all('/^(.*)\{\{([a-zA-Z0-9\.\(\)\ \-\_]*)\}\}(.*)/i',$cat_template,$matches)) {
                                        foreach($matches[2] as $attribute_text) {
                                            $format_type = array();
                                            if(preg_match_all('/^(.*)\.format\((.*)\)/i',$attribute_text,$format)) {
                                                $format_type = explode(' ',$format[2][0]);
                                                $attribute = trim($format[1][0]);
                                            } else {
                                                $attribute = $attribute_text;
                                            }
                                            if($category->getData($attribute)) {
                                                if(Mage::getModel('eav/config')->getAttribute('catalog_category',$attribute)->usesSource()) {
                                                    $replace = $category->getAttributeText($attribute);
                                                } else {
                                                    $replace = $category->getData($attribute);
                                                }
                                            } elseif(substr_count($attribute,' ')==0) {
                                                $arr = explode('_',$attribute);
                                                $str = '';
                                                foreach($arr as $part) {
                                                    $str.=ucfirst($part);
                                                }
                                                $replace = 'get'.$str;
                                                $replace = $category->$replace();
                                            } else {
                                                // nothing to do. we have static block or variable
                                                $replace = '';
                                            }
                                            if(!empty($format_type)) {
                                                foreach ($format_type as $format) {
                                                    $replace = Mage::helper('dcontent')->formatValue($replace,$format,$catecory,$attribute);
                                                }
                                            }
                                            $cat_template = str_replace('{{'.$attribute_text.'}}',$replace,$cat_template);
                                        }
                                    }
                    $output.=$processor->filter($cat_template);
                }

                foreach(explode(',',$this->getData('static')) as $static) {
                    $static = Mage::getModel('cms/block')->load($static);
                    $output.=$processor->filter($static->getContent());
                }

                $output.= $processor->filter($template->getAdditionalData());
				$output.= $processor->filter($template->getAfterProducts());
			endif;
			return $processor->filter($output);
		} else { 
			return $output;
		}
} catch (Exception $e) { return $e->getMessage(); } 
	}
}
