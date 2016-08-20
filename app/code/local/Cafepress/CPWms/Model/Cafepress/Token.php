<?php

class Cafepress_CPWms_Model_Cafepress_Token extends Cafepress_CPWms_Model_Cafepress_Abstract
{

    private $userToken = false;
    
    public function getUserToken()
    {
        return $this->userToken;
    }
    
    public function createToken()
    {
        $AuthUrl = Mage::getStoreConfig('cafepress_common/server/tokenurl').'?v=3'.
                    "&appKey=".Mage::getStoreConfig('cafepress_common/partner/apikey').
                    "&email=".Mage::getStoreConfig('cafepress_common/partner/email').
                    "&password=".Mage::getStoreConfig('cafepress_common/partner/password');
        
        $userTokenXml = $this->sendData($AuthUrl, FALSE);
        $userToken = substr(
                    $userTokenXml,
                    strpos($userTokenXml,'<value>')+strlen('<value>'),  
                    strlen($userTokenXml)-(strpos($userTokenXml,'</value>') - strpos($userTokenXml,'<value>')) -2+strlen('<value>') 
                );
        $this->userToken = $userToken;
        return $userToken;
    }
    
    public function sendData ($url, $verbose){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($verbose == TRUE)
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $result= curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function resetToken()
    {
        $this->userToken = false;
    }

    public function isAvailable($token){
        $url = 'http://open-api.cafepress.com/authentication.isValid.cp?appKey='.
            Mage::getStoreConfig('cafepress_common/partner/apikey').
            '&testUserToken='.$token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response= curl_exec($ch);
        $sxml = simplexml_load_string($response);
        $result = (string)$sxml[0];
        curl_close($ch);
        if($result == 'True'){
            return true;
        } else{
            return false;
        }
    }

    public function get(){
        $cacheId = 'CAFEPRESS_USER_TOKEN';
        $token = Mage::app()->getCache()->load($cacheId);
//        $token = Mage::getModel('core/cookie')->get('cp_user_token');
        if(!$token){
            $token = $this->createToken();
//            Mage::getModel('core/cookie')->set('cp_user_token', $token, time() + 1800);
            Mage::app()->getCache()->save($token, $cacheId,array(),1800);
        }
        return $token;
    }

    public function uploadImage($image){
        $url = 'http://upload.cafepress.com/image.upload.cp';
        $post = array();
        $post['cpFile1'] = '@'.$image;
        $post['appKey'] = Mage::getStoreConfig('cafepress_common/partner/apikey');
        $post['userToken'] = Mage::getModel('cpwms/cafepress_token')->get();
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
