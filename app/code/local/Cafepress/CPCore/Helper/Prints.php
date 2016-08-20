<?php
class Cafepress_CPCore_Helper_Prints extends Mage_Core_Helper_Abstract
{
    protected $printsPathFILE      = null;
    protected $printsPathURL    = null;


    public function getPrintsPath($type = 'FILE'){
        $namePath = 'printsPath'.strtoupper($type);
        if (!$this->$namePath){
            $this->$namePath = '/cafepress/prints/'.Mage::app()->getStore()->getId().'/';
            Mage::helper('cpcore')->checkDir(Mage::getBaseDir('media').$this->$namePath);
            if ('URL' == strtoupper($type)){
                $this->$namePath = Mage::getBaseUrl('media').$this->$namePath;
            } else {
                $this->$namePath = Mage::getBaseDir('media').$this->$namePath;
            }
        }
        return $this->$namePath;
    }
    
    public function createDesignId($imageFilePath){
//        $cacheId = 'CAFEPRESS_CREATE_DESIGN_ID'.  md5($imageFilePath);
//        $token = Mage::app()->getCache()->load($cacheId);
////        $token = Mage::getModel('core/cookie')->get('cp_user_token');
//        if(!$token){
//            $token = $this->createToken();
//            if(!$token){
//                return false;
//            }
//            Mage::getModel('core/cookie')->set('cp_user_token', $token, time() + 1800);
//            Mage::app()->getCache()->save($token, $cacheId,array(self::CACHE_TAG_CPCORE),1800);
//        }
//        
//        
        $url = 'http://upload.cafepress.com/image.upload.cp';
        $post = array();
        $post['cpFile1'] = '@'.$imageFilePath;
        $post['appKey'] = Mage::getStoreConfig('cafepress_common/partner/apikey');
        $post['userToken'] = Mage::getModel('cpcore/cafepress_token')->get();
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
}
