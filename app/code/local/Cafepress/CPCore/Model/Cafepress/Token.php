<?php

class Cafepress_CPCore_Model_Cafepress_Token extends Cafepress_CPCore_Model_Cafepress
{
    private static $userToken = false;
    
    public function getUserToken()
    {
        if (!self::$userToken){
            self::$userToken = $this->get();
        }
        return self::$userToken;
    }
    
    public function createToken()
    {
        $storeId = Mage::app()->getStore()->getId();
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
        if (strpos($userToken,'>')!==false){
            return false;
        } 
        self::$userToken = $userToken;
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
        self::$userToken = false;
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
        if (!self::$userToken){
            $cacheId = 'CAFEPRESS_USER_TOKEN_STORE_'.Mage::app()->getStore()->getId();
            $token = Mage::app()->getCache()->load($cacheId);
            if(!$token){
                $token = $this->createToken();
                if(!$token){
                    return false;
                }
                Mage::app()->getCache()->save($token, $cacheId,array(self::CACHE_TAG_CPCORE),1500);
            }
            self::$userToken = $token;
        }
        return self::$userToken;
    }

    public function uploadImage($image){
        $url = 'http://upload.cafepress.com/image.upload.cp';
        $post = array();
        $post['cpFile1'] = '@'.$image;
        $post['appKey'] = Mage::getStoreConfig('cafepress_common/partner/apikey');
        $post['userToken'] = $this->get();
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

    public function createUserToken($appKey, $email, $password){
        $url = 'http://open-api.cafepress.com/authentication.getUserToken.cp?appKey='.$appKey.'&email='.$email.'&password='.$password;
        $sxml = simplexml_load_file($url);
        return (string)$sxml;
    }
}
