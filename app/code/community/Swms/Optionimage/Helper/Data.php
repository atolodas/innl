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

class Swms_Optionimage_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_options = null;
    
     /**
     * Return the major and minor number of the Magento as integer
     * @return integer 
     */
    public function checkMagentoVersion() {
        $mageVersionList = explode(".", Mage::getVersion());
        return intval($mageVersionList[0].$mageVersionList[1]);
    }
    
    /**
     * Return the major and minor number of the Magento as integer
     * @return integer 
     */
    public function getMagentoVersion() {
        $mageVersionList =  Mage::getVersionInfo();
        return intval($mageVersionList['major'].$mageVersionList['minor']);
    }
    
    /**
     * check if images of options is active
    */
     public function isActiv(){
        return Mage::getStoreConfig('catalog/optionimage/isactiv');
    }

    /**
     * image should be used for Radiobuttons
    */
    public function isActivDropdown(){
        return(in_array('dropdown',$this->getOptions()));
    }

    /**
     * image should be used for Radiobuttons
    */
    public function isActivMultiple(){
        return(in_array('multiple',$this->getOptions()));
    }

    /**
     * image should be used for Radiobuttons
    */
    public function isActivRadio(){
        return(in_array('radio',$this->getOptions()));
    }

    /**
     * image should be used for Checkboxes
    */
    public function isActivCheckbox(){
        return(in_array('checkbox',$this->getOptions()));
    }

    /**
     * imge should be displayed after the optiontext
    */
    public function isDisplayTextFirst(){
        return Mage::getStoreConfig('catalog/optionimage/displaytextfirst');
    }

    /**
     * optiontext should be displayed
    */
    public function isDisplayText(){
        return Mage::getStoreConfig('catalog/optionimage/displaytext');
    }

    /**
     * filename of the image is stored with lowercase character
    */
    public function isLowercase(){
        return Mage::getStoreConfig('catalog/optionimage/islowercase');
    }

    /**
     * space-character in the displaytext of the option shall be replace in the filename with the underline-character
    */
    public function isReplaceSpace(){
        return Mage::getStoreConfig('catalog/optionimage/replacespace');
    }

    /**
     * url of the folder where the optionimages are stored
    */
    public function getUrl(){
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).Mage::getStoreConfig('catalog/optionimage/urlpath');
        $url = str_replace("/index.php","",$url);
        return $url;
    }

    /**
     * url of the image from an option
     * @param string $filename
     * @param string $filetyp
    */
    public function getImageUrl($filename,$filetype=null){
        if(is_null($filetype)){
            $filetype = $this->getTypes();
        }
        if($this->useDefaultSize() && $this->isResizeImage()){
            $url = $this->getResizedUrl($this->getImageRelUrl($filename,$filetype),$this->getDefaultWidth(),$this->getDefaultHeight());
        }
        else {
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).Mage::getStoreConfig('catalog/optionimage/urlpath')."/".$filename.".".$filetype;
        }
        $url = str_replace("/index.php","",$url);
        return $url;
    }

    /**
     * relative url-path of the image
     * @param string $filename
     * @param string $filetyp
    */
    public function getImageRelUrl($filename,$filetype=null){
        if(is_null($filetype)){
            $filetype = $this->getTypes();
        }
        $url = Mage::getStoreConfig('catalog/optionimage/urlpath')."/".$filename.".".$filetype;
        return $url;
    }
    
    /**
     * if a file from an option exists
     * @param string $filename
     * @param string $filetyp
    */
    public function isFileExists($filename,$filetype=null){
        if(is_null($filetype)){
            $filetype = $this->getTypes();
        }
        $imgPathFull=Mage::getBaseDir("media").DS.Mage::getStoreConfig('catalog/optionimage/urlpath').DS.$filename.".".$filetype;
        if(file_exists($imgPathFull)) {
            return true;
        }
        return false;
    }

    /**
     * subpath/subfolder of the optionimages
    */
    public function getRelPath(){
        return Mage::getStoreConfig('catalog/optionimage/urlpath');
    }

    /**
     * file types of the images
    */
    public function getSubfolderValue(){
        return strtolower(Mage::getStoreConfig('catalog/optionimage/subfolders'));
    }
    
    /**
     * file types of the images
    */
    public function getTypes(){
        return strtolower(Mage::getStoreConfig('catalog/optionimage/types'));
    }

    /**
     * display order
    */
    public function getDisplayorder(){
        return strtolower(Mage::getStoreConfig('catalog/optionimage/displayorder'));
    }

    /**
     * List of types which shall be displayed with images
    */
   public function getOptions(){
       if(is_null($this->_options)) {
        $this->_options = explode(',',Mage::getStoreConfig('catalog/optionimage/allowed_options'));
       }
        return $this->_options;
    }

    /**
     * defines if title of the options are used for subfolders
    */
    public function getSubfolderList()
    {
        $options = Array(
        "root"=>$this->__("Only Rootfolder"),
        "sku"=>$this->__("SKU"),
        "optiontitle"=>$this->__("Title of Option"),
        "skuoptiontitle"=>$this->__("SKU and Title of Option"));
        return $options;
    }

    /**
     * List of allowed filetypes
    */
    public function getTypesList()
    {
        $options = Array(
        "gif"=>"GIF",
        "jpg"=>"JPG",
        "png"=>"PNG");
        return $options;
    }

    /**
     * List of supported options
    */
    public function getDisplayorderList()
    {
        $options = Array(
        "image"=>$this->__("Image"),
        "text"=>$this->__("Text"),
        "onlyimage"=>$this->__("Only Image"));
        return $options;
    }

    /**
     * List of supported options
    */
    public function getOptionsList() {
        $options = array();
        //$options[] = array('value'=>'none', 'label'=>$this->__('None'));
        $options[] = array('value'=>'dropdown', 'label'=>$this->__('Dropdown'));
        $options[] = array('value'=>'multiple', 'label'=>$this->__('Multiple'));
        $options[] = array('value'=>'radio', 'label'=>$this->__('Radio'));
        $options[] = array('value'=>'checkbox', 'label'=>$this->__('Checkbox'));
        return $options;
    }

    /**
     * image shall be display with the defaultsize
    */
    public function useDefaultSize(){
        return Mage::getStoreConfig('catalog/optionimage/use_defaultsize');
    }

    /**
     * resized images shall be stored in the cachefolder
    */
    public function useProductImageCache(){
        return Mage::getStoreConfig('catalog/optionimage/use_productimagecache');
    }

    /**
     * image shall be resized to defaultsize
    */
    public function isResizeImage(){
        return Mage::getStoreConfig('catalog/optionimage/resizeimage');
    }

    /**
     * default height of the images
    */
    public function getDefaultHeight(){
        return Mage::getStoreConfig('catalog/optionimage/height');
    }

    /**
     * default width of the images
    */
    public function getDefaultWidth(){
        return Mage::getStoreConfig('catalog/optionimage/width');
    }

    /**
     * Returns the renamed optionname as filename of the image
     * @param string $filename (displaytext from the option)
    */
    public function overrideFilename($filename) {
        if($this->isReplaceSpace()){
           $filename = str_replace(" ","_",$filename);
        }
        if($this->isLowercase()){
            mb_internal_encoding('UTF-8');
            $filename = mb_strtolower($filename);
        }

        $replaceStr = Mage::getStoreConfig('catalog/optionimage/replacecharacter');
        $replaceStr = trim($replaceStr);
        if(strlen($replaceStr) > 0) {
            $replacesList = explode(',',$replaceStr);
            $filename = str_replace(array("\r\n", "\n", "\r"),"",$filename);
            $listfrom = array();
            $listto = array();
            foreach ($replacesList as $replaceitem) {
                $itemList = explode('=',$replaceitem);
                $listfrom[]= $itemList[0];
                $listto[] = $itemList[1];
            }
            $filename = str_replace($listfrom,$listto,$filename);
        }
        return $filename;
    }

    /**
     * Returns the resized Image URL
     * http://subesh.com.np/2009/11/image-resize-magento-cache-resized-image/
     * @param string $imgUrl - This is relative to the the media folder (custom/module/images/example.jpg)
     * @param int $x Width
     * @param int $y Height
     */
     public function getResizedUrl($imgUrl,$x,$y=NULL){
        $imgPath=$this->splitImageValue($imgUrl,"path");
        $imgOptionTitle = "";
        $imgName=$this->splitImageValue($imgUrl,"name");
        if(strcmp('optiontitle',$this->getSubfolderValue()) == 0){
            $imgTitle=$this->splitImageValue($imgUrl,"optiontitle");
            $imgName = $imgTitle."/".$imgName;
        }

        $imgName=str_replace("/",DS,$imgName);
        /**
         * Path with Directory Separator
         */
        $imgPath=str_replace("/",DS,$imgPath);

        /**
         * Absolute full path of Image
         */
        $imgPathFull=Mage::getBaseDir("media").DS.$imgPath.DS.$imgName;

        /**
         * If Y is not set, then set it to as X
         */
        $widht=$x;
        $y?$height=$y:$height=$x;

        /**
         * Resize folder is widthXheight
         */
        $resizeFolder=$widht."X".$height;

        /**
         * Image resized path will then be
         */
         if($this->useProductImageCache()) {
            $imageResizedPath=Mage::getBaseDir("media").DS."catalog".DS."product".DS."cache".DS.$imgPath.DS.$resizeFolder.DS.$imgName;
         }
         else {
            $imageResizedPath=Mage::getBaseDir("media").DS.$imgPath.DS.$resizeFolder.DS.$imgName;
         }
        /**
         * First check in the cache i.e image resized path
         * If not in cache then create image of the width=X and height = Y
         */
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) {
                $imageObj = new Varien_Image($imgPathFull);
                $imageObj->constrainOnly(FALSE);
                $imageObj->keepAspectRatio(FALSE);
                $imageObj->keepFrame(TRUE);
                $imageObj->resize($widht,$height);
                $imageObj->save($imageResizedPath);
        }

        /**
         * If the image is in cache replace the Image Path with / for http path.
         */
         if($this->useProductImageCache()) {
            $imgUrl=str_replace(DS,"/","catalog".DS."product".DS."cache".DS.$imgPath);
         }
         else {
             $imgUrl=str_replace(DS,"/",$imgPath);
         }
         $imgName = str_replace(DS,"/",$imgName);
        /**
         * Return full http path of the image
         */
        return Mage::getBaseUrl("media").$imgUrl."/".$resizeFolder."/".$imgName;
    }

    /**
     * Split image into Path and Name
     * http://subesh.com.np/2009/11/image-resize-magento-cache-resized-image/
     * Path=custom/module/images/
     * Name=example.jpg
     *
     * @param string $imageValue
     * @param string $attr
     * @return string
     */
    public function splitImageValue($imageValue,$attr="name"){
        $imArray=explode("/",$imageValue);
        $name=$imArray[count($imArray)-1];
        $path=implode("/",array_diff($imArray,array($name)));
        $optiontitle ="";
        if(strcmp('optiontitle',$this->getSubfolderValue()) == 0){
            $optiontitle=$imArray[count($imArray)-2];
            $path = str_replace("/".$optiontitle, "", $path);
        }

        if($attr=="path"){
            return $path;
        }
        elseif($attr=="optiontitle"){
           return $optiontitle;
        }
        else
            return $name;
   }
/*
But remember your base image or big image must be in Root/media/custom/module/images/example.jpg
    echo Mage::helper('yourmodulehelper')->getResizedUrl("custom/module/images/example.jpg",101,65)
By doing this new images will be created in the Root/media/custom/module/images/101X65/example.jpg
*/
}