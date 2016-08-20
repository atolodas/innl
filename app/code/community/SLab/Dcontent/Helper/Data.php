<?php
/**
* Product blocks helper
*
* @category    SLab
* @package     SLab_Dcontent
* @author      SLabweb team
*/
class SLab_Dcontent_Helper_Data extends Mage_Core_Helper_Abstract
{
/**
* Label position
*
* @var string
*/
protected $_position;
/**
* Product entity
*
* @var Mage_Eav_Model_Entity_Product
*/
protected $_product;
/**
* Lable image size
*
* @var int
*/
protected $_sizeX;
protected $_sizeY;
/**
* Label image
*
* @var string
*/
protected $_image;
/**
* Current size
*
* @var string
*/
protected $_size;
/**
* Label type
*
* @var int
*/
protected $_labeltype;
/**
* Is label displayed
*
* @var bool
*/
protected $_display;
/**
* Label html
*
* @var string
*/
protected $_label;
/**
* Is label need to be shown
*
* @var int
*/
protected $_show = 0;
protected $_top = 0;
protected $_left = 0;
/**
* Format text
*
* @param string $value
* @param string $format_type
* @param Mage_Catalog_Model_Product $product
* @param string $attribute
* @return string
*/
public function formatValue($value, $format_type, $product,$attribute) {
    $data = explode('-',$format_type);
    $format = trim($data[0]);
    if(!$format) return $value;
    switch($format) {
        case 'price':
        $value = Mage::helper('core')->formatPrice($value,false);
        break;
        case 'int':
        $value = (int)$value;
        if($attribute == 'fprice') { 
            $value = number_format($value, 0, '.', ' ');
            $value.=" 000 р.";
        }
	break;
        case 'image':
        try {
            $image = Mage::helper('catalog/image')->init($product, $attribute);
            if(count($data)==2) $image->resize($data[1]);
            else if(count($data)==3) $image->resize($data[1],$data[2]);
            $value = $image;
        } catch (Exception $e) { $value = ''; }
        break;
        case 'oggimage':
        try {
            $image = Mage::helper('score/image')->init($product, $attribute);
            if(count($data)==2) $image->resize($data[1]);
            else if(count($data)==3) {
                if($data[1]==0) {
                    $image->resizeByHeight($data[2]);
                } else {
                    $image->resize($data[1],$data[2]);
                }
            }
            $value = $image;
        } catch (Exception $e) { $value = ''; }
        break;
        case 'absimage':
        try {
            $image = Mage::helper('score/image')->init($product, $attribute,$data[2]);
            if(count($data)==2) $image->resize($data[1]);
            else if(count($data)==3) { if($data[1]==0) { $data[1] = $data[2]; } $image->resize($data[1],$data[2]); }
            $value = $image;
        } catch (Exception $e) { $value = ''; }
        break;
        case 'upercase':
        $value = strtoupper($value);
        break;
        case 'lowercase':
        $value = strtolower($value);
        break;
        case 'capitalize':
        $value = ucfirst($value);
        break;
        case 'bold':
        case 'strong':
        $value = '<strong>'.$value.'</strong>';
        break;
        case 'italic':
        $value = '<i>'.$value.'</i>';
        break;
        case 'strike':
        $value = '<s>'.$value.'</s>';
        break;
        case 'underline':
        $value = '<u>'.$value.'</u>';
        break;
        case 'date':
        $dateFormat = 'd.m.Y';
        $value = date($dateFormat,strtotime($value));
        $value = Mage::helper('core')->formatDate($value, 'long', false);
        break;
        case 'dateformat':
        $dateFormat = trim($data[1]);
        $value = date($dateFormat,strtotime($value));
        break;
        case 'child_approx':
        $collection = Mage::getModel('score/oggetto')
        ->getCollection()
        ->setStoreId(0)
        ->addAttributeToFilter(strtolower(str_replace(' ','',$product->getSetName())).'_id',$product->getId())
        ->addAttributeToSelect($attribute)
        ->getColumnValues($attribute);
        if(count($collection)) {
            return $this->floorToFraction(array_sum($collection)/count($collection),2);
        } else {
            return 0;
        }
        break;
        case 'findurls':
        $rexProtocol = '(https?://)?';
        $rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $rexPort     = '(:[0-9]{1,5})?';
        $rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
// Solution 1:
        return preg_replace_callback("&\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))&",
            'SLab_Dcontent_Helper_Data::callbackUrl', htmlspecialchars($value));
        break;
        case 'findhashtags':
        return preg_replace('/#(\w+)/ui', '<a href="'.Mage::getBaseUrl().'oggetto/$1">#$1</a>', $value);
        break;
        case 'specialchars':
        return urlencode(htmlspecialchars($value));
        break;
        case 'striptags':
        return strip_tags($value);
        break;
        default:
        break;
    }
    return $value;
}
public function floorToFraction($number, $denominator = 1)
{
    $x = $number * $denominator;
    $x = floor($x);
    $x = $x / $denominator;
    return $x;
}
public function callbackUrl($match)
{
// Prepend http:// if no protocol specified
    $completeUrl = $match[1] ? $match[0] : "http://{$match[0]}";
    return '<a target="_blank" href="' . $completeUrl . '">'
    . $match[2] . $match[3] . $match[4] . '</a>';
}
/**
* Decode input from base64
*
* @param string $encoded
* @return array
*/
public function decodeInput($encoded)
{
    parse_str($encoded, $data);
    foreach($data as $key=>$value) {
        parse_str(base64_decode($value), $data[$key]);
    }
    return $data;
}
public function labeledProduct($product_id) {
    $products = $this->getAllProducts();
    if(substr_count($products,$product_id.'=')>0) {
        return true;
    }
    return false;
}
public function getAllProducts() {
    if(!(Mage::registry('dcontent/products'))) {
        $blocks= Mage::getModel('dcontent/dcontent')->getResourceCollection()->addFieldToSelect('products')->addFieldToFilter('status',1);
        foreach($blocks as $block)  {
            $products[] = $block->getProducts();
        }
        $products = implode('&',$products);
        Mage::register('dcontent/products',serialize($products));
    }
    return unserialize(Mage::registry('dcontent/products'));
}
public function getLabel($product, $size)
{
    $this->_product = $product;
    $this->_top = Mage::getStoreConfig('blocks/image_position/'.$size."_top", Mage::app()->getStore()->getId());
    $this->_left = Mage::getStoreConfig('blocks/image_position/'.$size."_left", Mage::app()->getStore()->getId());
    $size = explode('x',Mage::getStoreConfig('blocks/image_size/'.$size, Mage::app()->getStore()->getId()));
    if(!$size || empty($size)) { $size = 100; }
    $this->_size = $size;
    if (is_array($size)) {
        if (count($size) == 2) {
            $this->_sizeX = $size['0'];
            $this->_sizeY = $size['1'];
        } else {
            $this->_sizeX = $this->_sizeY = $size['0'];
        }
    } else {
        $this->_sizeX = $this->_sizeY = $size;
    }
    $this->getDefaultLabel();
    $this->toShow();
    $this->returnLabel();
    return $this->_label;
}
/**
* Get one of default labels
*/
public function getDefaultLabel()
{
    $this->_display = true;
    $this->setPosition('topright');
    $id = $this->_product->getId();
    $block =  Mage::getModel('dcontent/dcontent')->getResourceCollection()->addFieldToFilter('products',array(array('like'=>"$id=%"),array('like'=>"%&$id=%")))->addFieldToSelect('image')->getFirstItem();
    $this->getImage('dcontent/'.$block->getImage());
}
/**
* Set label position
*
* @param string $position
*/
public function setPosition($position)
{
    switch ($position) {
        case 'topleft':
        $this->_position = "top left";
        break;
        case 'topright':
        $this->_position = "top right";
        break;
        case 'center':
        $this->_position = "center center";
        break;
        case 'bottomleft':
        $this->_position = "bottom left";
        break;
        case 'bottomright':
        $this->_position = "bottom right";
        break;
    }
}
/**
* Return label image
*
* @param string $image
*/
public function getImage($image)
{
    $this->_image = Mage::helper('catalog/image')->init($this->_product, 'thumbnail', $image)->resize($this->_sizeX,$this->_sizeY);
}
/**
* Return full label code
*/
public function returnLabel()
{
    if ($this->_show && $this->_image)
        $this->_label = '<div class="product-img-label" style="position:absolute; height:' .
    $this->_sizeX . 'px; width: ' .
    $this->_sizeY . 'px; top:' . $this->_top . 'px; left:' . $this->_left . 'px; z-index: 70; pointer-events: none;background: url(\'' .
        $this->_image . '\') ' . $this->_position . ' no-repeat"></div>';
else
    $this->_label = null;
}
/**
* Check should we show label or not
*/
public function toShow()
{
    if ($this->_display == 1 || $this->_display == 3 && $this->_page == 'category')
        $this->_show = 1;
    elseif ($this->_display == 1 || $this->_display == 2 && $this->_page == 'product')
        $this->_show = 1;
    else
        $this->_show = 0;
}
public function getCustomProcessor() {
    $processor = Mage::getModel('dcontent/filter')
    ->setUseAbsoluteLinks(true)
    ->setStoreId(Mage::app()->getStore()->getId())
    ->setUseSessionInUrl(false)
    ->setPlainTemplateMode(false);
    return $processor;
}
public function addVariablesToProcessor($processor) {
    $variables = array();
    if(Mage::registry('current_oggetto')) $variables['oggetto'] = Mage::registry('current_oggetto');
    if(Mage::registry('product')) $variables['product'] = Mage::registry('product');
    if(Mage::registry('current_product')) $variables['product'] = Mage::registry('current_product');
    $variables['store'] = Mage::app()->getStore();
    $variables['customerSession'] = Mage::getSingleton('customer/session');
    $variables['customer'] = Mage::getSingleton('customer/session')->getCustomer();
    $processor->setVariables($variables);
    return $processor;
}
public function addCData($value){
    if($value != htmlspecialchars($value) || $value != addslashes($value)){
        return '<![CDATA['.$value.']]>';
    }
    return $value;
}
public function localise($string, $number) {
    $string = explode(',',$string);
    $lastChar = substr($number.'', -1);
    if($lastChar == '0') return($string[0]);
    if($lastChar == '1') return($string[1]);
    if(in_array($lastChar,array('2','3','4'))) return($string[2]);
    if(in_array($lastChar,array('5','6','7','8','9'))) return($string[3]);
    return '';
}
}
