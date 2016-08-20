<?php
/**
 * Block for displaying Categories block
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Categories extends Mage_Catalog_Block_Product_Abstract  implements Mage_Widget_Block_Interface
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
            $template = $this->getData('template_id');
            $categories = explode(',',$this->getData('categories'));
            if (empty($template) || empty($categories)) {
                return $output;
            }
            $processor = Mage::getModel('core/email_template_filter');
            $template = Mage::getModel('dcontent/templates')->load($template);
            $per_line = 100000; // Here we have hardcode. But not sure that this variable will be required for now TODO: unhardcode ?
            $output = $this->getCategoriesDropdown();
            $output.=$template->getBeforeProducts();
            $selected = isset($_POST['category'])?$_POST['category']:'';
            foreach($categories as $category) {
                $i = 1;
                if(($selected && $category['id']==$selected) || (!$selected && $i==1)) {
                    $category = Mage::getModel('catalog/category')->load($category);
                    $products = $category->getProductCollection(); // TODO:  and sorting
                    $search=array("\n", "\r");
                    $_collectionSize = $products->getSize();
                    foreach ($products as $product):
                        $_product = Mage::getModel('catalog/product')->load($product->getId());
                        if($_product->getId() && $_product->isVisibleInCatalog() ) {
                            $product_template = str_replace($search,"", $template->getProduct());
                            //$product_template = $processor->filter(&$product_template);
                            $product_template = $this->replacePatterns($template,$product_template,$_product,'');
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
                }
            }
            //$output.=$template->getAdditionalData();
    		$output.=$template->getAfterProducts();
			return $processor->filter($output);

        } catch (Exception $e) { return $e->getMessage(); }
	}

    protected function replacePatterns($template_object,$template,$_product,$data) {
        $pattern = '/^(.*)\{\{([a-zA-Z0-9\.\(\)\ \-\_]*)\}\}(.*)/i';
        $matches = null;
        while(preg_match_all($pattern,$template,$matches)) {
            foreach($matches[2] as $attribute_text) {
                $format_type = array();
                if(preg_match_all('/^(.*)\.format\((.*)\)/i',$attribute_text,$format)) {
                    $format_type = explode(' ',$format[2][0]);
                    $attribute = trim($format[1][0]);
                } else {
                    $attribute = $attribute_text;
                }

                $replace = $this->getReplacement($_product,$attribute,$data);

                //if(is_array($replace)) { print_r($replace); }
                if(is_object($replace)) {
                    $object_replace = '';
                    foreach($replace as $r) {
                        $data = $r->getData();
                        $object_replace  .=   str_replace('{{url}}',$data['url'], $this->replacePatterns($template_object,$template_object->getAdditionalData(), $_product, $r->getData())); // OMG HARDCODE
                    }
                    $replace = $object_replace;
                }
                if(!empty($format_type)) {
                    foreach ($format_type as $format) {
                        $replace = Mage::helper('dcontent')->formatValue($replace,$format,$_product,$attribute);
                    }
                }
                $template = str_replace('{{'.$attribute_text.'}}',$replace,$template);
            }
        }

        return $template;
    }

    protected function getReplacement($_product,$attribute,$data) {
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
        return $replace;
    }

    public function getCategories() {

        $categories = explode(',',$this->getData('categories'));
        if (empty($categories)) {
            return '';
        }

        $categoriesCollection = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToFilter('is_active',1)
            ->addAttributeToFilter('entity_id',$categories)
            ->addAttributeToSelect('name')
            ->addOrder('name','asc');
        return $categoriesCollection;
    }

    public function getCategoriesDropdown() {
        $categories = $this->getCategories();
        $html = '';
        if(count($categories)>1) {
            $selected = isset($_POST['category'])?$_POST['category']:'';
            $html .= '<div class="filter">
				<b class="title">FILTER BY:</b>
				<form method="post" action="">
				<select onchange="this.form.submit();" name="category">';
            foreach($categories as $category) {
                $select = '';
                if($selected && $selected==$category->getId()) $select = 'selected';
                $html.="<option value='{$category->getId()}' {$select}>{$category->getName()}</option>";
            }
            $html .= '</select>
            </form>';
        }
        return $html;
    }
}
