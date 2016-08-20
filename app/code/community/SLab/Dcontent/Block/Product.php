<?php
/**
 * Block for displaying product page
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Product extends Mage_Core_Block_Template
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getProductPageTemplate() {
        try {
            $output='';
            $id = Mage::app()->getRequest()->getParam('id');
            $template = 2; // Here we have hardcoded template for product // TODO: unhardcode later
            if (empty($template)) {
                return $output;
            }
            $template = Mage::getModel('dcontent/templates')->load($template);
            $_product = Mage::getModel('catalog/product')->load($id);
            $processor = Mage::getModel('core/email_template_filter');

            $output.=$template->getBeforeProducts();
            $i = 1;
            $search=array("\n", "\r");

                if($_product->getId() && $_product->isVisibleInCatalog() ) {
                    $product_template = str_replace($search,"", $template->getProduct());
                    //$product_template = $processor->filter(&$product_template);
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
                    $i++;
                }
            $output.=$template->getAdditionalData();
            $output.=$template->getAfterProducts();
            return $processor->filter($output);
        } catch (Exception $e) { return $e->getMessage(); }
    }
}
