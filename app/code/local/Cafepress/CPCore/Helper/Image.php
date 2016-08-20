<?php
class Cafepress_CPCore_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_defaultImageSize    = 480;
    protected $_pathOfImportImage   = null;

    public function getPathToImportImage(){
        if (!$this->_pathOfImportImage){
            $this->_pathOfImportImage = Mage::getBaseDir('media').'/cafepress/import/';
            if(!file_exists($this->_pathOfImportImage)){
                mkdir($this->_pathOfImportImage,0777,true);
            }
        }
        return $this->_pathOfImportImage;
    }


    public function getImagesForCreateProduct($cpProductData, $colors=false, $onlyAttributable =false){
        $images = array();
        $defaultPerspective = $cpProductData['attributes']['defaultPerspective'];
        if ($cpProductData['perspective']){
            foreach($cpProductData['perspective'] as $perspective){
                $images[$perspective['name']] = array(
                    'url'       => $perspective['attributes']['wildcardProductUri'],
                    'file'      => $onlyAttributable?false:$this->downloadImage($perspective['attributes']['wildcardProductUri'],true),
                    'default'   => ($defaultPerspective == $perspective['name'])?true:false,
                );
            }
        }

        if ($colors && $cpProductData['color']){
            $defaultImageUrlsByPerspectives = array();
            foreach($images as $key => $_perspective){
                $defaultImageUrlsByPerspectives[$key] = $this->changeSizeInUrl($_perspective['url']);
            }

            $images['by_attribute']['color'] = array();

            foreach($cpProductData['color'] as $_color){
                if (in_array($_color['id'],$colors)){
                    $colorName = $_color['attributes']['name'];
                    $images['by_attribute']['color'][$_color['id']] = array();
                    foreach($defaultImageUrlsByPerspectives as $perspName => $defaultUrl){
                        $url    = $this->changeColorInUrl($defaultUrl,$colorName);
                        $path   = $this->downloadImage($url);
                        $images['by_attribute']['color'][$_color['id']][$perspName] = array(
                            'file'      => $path,
                            'default'   => ($defaultPerspective == $perspName)?true:false,
                        );
                    }
                }
            }
        }

        return $images;
    }

    /**
     * For ex:
     * $url = 'http://.../product/700364957v3_240x240_Front_Color-AshGrey.jpg'
     * $color = 'Light Blue'
     * result == 'http://.../product/700364957v3_240x240_Front_Color-LightBlue.jpg'
     * @param $url
     * @param $color
     */
    public function changeColorInUrl($url, $color){
        $color = str_replace(' ','',$color);
        return preg_replace('/Color-([0-9a-zA-Z]*)/i', 'Color-'.$color,$url);
    }

    public function changeSizeInUrl($url, $size=false){
        if (!$size){
            $size = $this->_defaultImageSize;
        }
        return preg_replace('/(_[0-9]*x[0-9]*_)/i', '_'.$size.'x'.$size.'_',$url);
    }

    public function downloadImage($url,$resize=false){
        if ($resize){
            $url = $this->changeSizeInUrl($url);
        }
        $path = $this->getPathToImportImage().basename($url);
        if (!file_exists($path)){
            try {
                copy($url, $path);
            } catch (Exception $e) {
                Mage::log("Remote file not exist: {$url}", null, 'cafepress.log');
            }
        }
        return $path;
    }

    public function clearImportDirectory(){
        try{
            $path = $this->getPathToImportImage();
            $objs = glob($path."*");
            if ($objs)
            {
                foreach($objs as $obj)
                {
                    @unlink($obj);
                }
            }
        } catch (Exception $e){
            Mage::log($e->getMessage());
        }
        return true;
    }

}
