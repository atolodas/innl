<?php

/**
 * Product images
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cafepress_CPCore_Model_Cafepress_Image extends Mage_Core_Model_Abstract
{
    public function getUserToken(){
//        return Mage::getModel('cpcore/cafepress_token')->getUserToken();
        return Mage::getSingleton('cpcore/cafepress_token')->getUserToken();
    }

    public function uploadImage($image){
        $url = 'http://upload.cafepress.com/image.upload.cp';
        $post = array();
        $post['cpFile1'] = '@'.$image;
        $post['appKey'] = Mage::getStoreConfig('cafepress_common/partner/apikey');
        $post['userToken'] = $this->getUserToken();
        $post['folder'] = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        $sxml = simplexml_load_string($response);
        return (string)$sxml[0]->value;
    }

    public function multiUploadImages(array $imagesPath){
        set_time_limit(600);
        $url = 'http://upload.cafepress.com/image.upload.cp';
        $post = array();
//        $post['cpFile1'] = '@'.$image;
        $post['appKey'] = Mage::getStoreConfig('cafepress_common/partner/apikey');
        $post['userToken'] = $this->getUserToken();
        $post['folder'] = '';

        $curly = array();
        $result = array();
        $mh = curl_multi_init();

        $iCount = 0;
        foreach ($imagesPath as $image) {
            if ($image){
                $post['cpFile1'] = '@'.$image;

                $curly[$iCount] = curl_init();

                curl_setopt($curly[$iCount], CURLOPT_URL, $url);
                curl_setopt($curly[$iCount], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$iCount], CURLOPT_POSTFIELDS, $post);

                curl_multi_add_handle($mh, $curly[$iCount]);
            } else {
                $curly[$iCount] = false;
            }
            $iCount ++;
        }
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            usleep(50000);
        }
        while($running > 0);

        foreach($curly as $id=>$val) {
            if (!$val){
                $result[$id] = false;
            } else {
                $data = curl_multi_getcontent($val);
                $sxml = simplexml_load_string($data);
                $result[$id] = (string)$sxml[0]->value;
                unset($sxml);

                curl_multi_remove_handle($mh, $val);
            }
        }
        curl_multi_close($mh);

        return $result;
    }
    
}
