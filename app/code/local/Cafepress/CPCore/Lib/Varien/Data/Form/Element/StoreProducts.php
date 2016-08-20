<?php

class Cafepress_CPCore_Lib_Varien_Data_Form_Element_StoreProducts extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $page = $_SESSION['cp_copy_products_page'];
        if(!$page){
            $page = 1;
        }

        $prev_page = $page - 1;
        $next_page = $page + 1;

        $products_on_page = 20;
//        $products_count = Mage::getModel('cpcore/cafepress_product')->getStoreProductsCount();
        $products_count = Mage::getModel('cpcore/cafepress_sections')->getSectionProductsCount($_SESSION['cp_copy_section']);
        $max_page = ceil($products_count / $products_on_page);

//        $products = Mage::getModel('cpcore/cafepress_product')->getStoreProducts($page -1);
        $products = Mage::getModel('cpcore/cafepress_sections')->getSectionProducts($_SESSION['cp_copy_section'], $page -1, $products_on_page);
//        $products = array();
        $session_products = array();

        $category_list = array();
        $category_ids = Mage::getModel('catalog/category')->getCollection()->getAllIds();
        foreach($category_ids as $category_id){
            $category = Mage::getModel('catalog/category')->load($category_id);
            if($category->getLevel() > 0){
                $category_list[$category->getId()] = $category->getName();
            }
        }

        $color_options = Mage::getModel('cpcore/cafepress_product')->getOptionCustomIds2('color');

        $html = '';

        $html .= '<script type="text/javascript">
            BASE = "'.Mage::getUrl('', array('_secure' => true)).'";
            cp_max_page = "'.$max_page.'";
        </script>';

        $html .= '<div class="cp_pager"> Page ';
        if($prev_page > 0){
            $url = Mage::helper('core/url')->getCurrentUrl();
            $new_url = preg_replace('/\/page\/(\d+)[\/]*/', '/page/'.$prev_page."/", $url);
            if($new_url == $url){
                $new_url = $url.'page/'.$prev_page."/";
            }
            $html .= '<a href="'.$new_url.'"><img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_left.gif"></a>';
        } else{
            $html .= '<img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_left_off.gif">';
        }
        $html .= '<input class="input-text cp_page" type="text" onkeypress="{if (event.keyCode==13)redirectToPage(this)}" value="'.$page.'">';
        if($next_page <= $max_page){
            $url = Mage::helper('core/url')->getCurrentUrl();
            $new_url = preg_replace('/\/page\/(\d+)[\/]*/', '/page/'.$next_page."/", $url);
            if($new_url == $url){
                $new_url = $url.'page/'.$next_page."/";
            }
            $html .= '<a href="'.$new_url.'"><img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_right.gif"></a>';
        } else{
            $html .= '<img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_right_off.gif">';
        }
        $html .= ' of '.$max_page.' pages</div>';

        $html .= '<table id="cp_store_products" cellpadding="0" cellspacing="0">';
        $html .= '<tr><th><input type="checkbox" checked="checked" onclick="toggleCheckboxes(this)"></th>
                    <th class="cp_store_products_center">Name</th>
                    <th class="cp_store_products_center">Color <input type="checkbox" name="use_color" checked="checked"></th>
                    <th class="cp_store_products_center">Size <input type="checkbox" name="use_size" checked="checked"></th>
                    <th class="cp_store_products_center">Category</th>
                    <th class="cp_store_products_center">Default Image</th></tr>';
        foreach($products as $product){
            $session_products[$product['id']] = $product;
            $html .= '<tr>';
            $html .= '<td class="cp_store_products_left"><input class="cp_product_checkboxes" name="products['.$product['id'].'][enabled]" type="checkbox" checked="checked"></td>';
            $html .= '<td class="cp_store_products_text"><span>'.$product['name'].'</span></td>';
            if(count($product['colors']) > 0){
                $html .= '<td  class="cp_store_products_center"><select name="products['.$product['id'].'][color]">';
                $html .= '<option value="">All</option>';
                foreach($product['colors'] as $color){
                    $html .= '<option value="'.$color['id'].'">'.$color['name'].'</option>';
                }
                $html .= '</select></td>';
            } else{
                $html .= '<td class="cp_store_products_center">&nbsp;</td>';
            }
            if(count($product['sizes']) > 0){
                $html .= '<td class="cp_store_products_center"><select name="products['.$product['id'].'][size]">';
                $html .= '<option value="">All</option>';
                foreach($product['sizes'] as $size){
                    $html .= '<option value="'.$size['id'].'">'.$size['name'].'</option>';
                }
                $html .= '</select></td>';
            } else{
                $html .= '<td class="cp_store_products_center">&nbsp;</td>';
            }
            $html .= '<td class="cp_store_products_center"><select name="products['.$product['id'].'][category]">';
            $html .= '<option value="">Remote Category</option>';
            foreach($category_list as $category_id => $category_name){
                $html .= '<option value="'.$category_id.'">'.$category_name.'</option>';
            }
            $html .= '</select></td>';

            $html .= '<td class="cp_store_products_center"><select name="products['.$product['id'].'][default_image]">';
            $used_colors = array();
            foreach($product['product_images'] as $product_image){
                if(!in_array($product_image['colorId'], $used_colors)){
                    $used_colors[] = $product_image['colorId'];
//                    $html .= '<option value="'.$product_image['colorId'].'">'.$color_options[Mage::getModel('cpcore/cafepress_product')->getLocalColorId($product_image['colorId'])].'</option>';
                    if(isset($color_options[$product_image['colorId']])){
                        $html .= '<option value="'.$product_image['colorId'].'">'.$color_options[$product_image['colorId']].'</option>';
                    } else{
                        $html .= '<option value="'.$product_image['colorId'].'">'.'Default'.'</option>';
                    }
                }
            }
            $html .= '</select></td>';

            $html .= '</tr>';
        }
        $_SESSION['cp_store_products'] = $session_products;
        $cacheId = 'CAFEPRESS_USER_STORE'.Mage::getModel('customer/session')->getId();
        Mage::app()->getCache()->save(serialize($page), $cacheId);

        $html .= '</table>';

        $html .= '<div class="cp_pager"> Page ';
        if($prev_page > 0){
            $url = Mage::helper('core/url')->getCurrentUrl();
            $new_url = preg_replace('/\/page\/(\d+)[\/]*/', '/page/'.$prev_page."/", $url);
            if($new_url == $url){
                $new_url = $url.'page/'.$prev_page."/";
            }
            $html .= '<a href="'.$new_url.'"><img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_left.gif"></a>';
        } else{
            $html .= '<img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_left_off.gif">';
        }
        $html .= '<input class="input-text cp_page" type="text" onkeypress="{if (event.keyCode==13)redirectToPage(this)}" value="'.$page.'">';
        if($next_page <= $max_page){
            $url = Mage::helper('core/url')->getCurrentUrl();
            $new_url = preg_replace('/\/page\/(\d+)[\/]*/', '/page/'.$next_page."/", $url);
            if($new_url == $url){
                $new_url = $url.'page/'.$next_page."/";
            }
            $html .= '<a href="'.$new_url.'"><img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_right.gif"></a>';
        } else{
            $html .= '<img class="cp_arrow" src="'.$this->getSkinUrl('', array('_secure' => true)).'adminhtml/default/default/images/pager_arrow_right_off.gif">';
        }
        $html .= ' of '.$max_page.' pages</div>';

        return $html;
    }
}