<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\   Social Login    \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   EH                            ///////
 \\\\\\\                      * @package    EH_SocialLogin                \\\\\\\
 ///////    * @author     Suneet Kumar <suneet64@gmail.com>               ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\* @copyright  Copyright 2013 © www.extensionhut.com All right reserved\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class EH_SocialLogin_VkController extends Mage_Core_Controller_Front_Action
{
    protected $referer = null;
    protected $flag = null;

    public function connectAction()
    {
        try {
            $this->_connectCallback();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if (!empty($this->referer)) {
            if(empty($this->flag)){
                echo '
                <script type="text/javascript">
                    window.opener.location.reload(true);
                    window.close();
                </script>
                ';
            }else{
                echo '
                <script type="text/javascript">
                    window.close();
                </script>
                ';
            }
          

        } 
        $this->_redirectReferer(); 
    }

    public function disconnectAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        try {
            $this->_disconnectCallback($customer);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if (!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            Mage::helper('ehut_sociallogin')->redirect404($this);
        }
    }

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer)
    {
        $this->referer = Mage::getUrl('ehut_sociallogin/account/vk');

        Mage::helper('ehut_sociallogin/vk')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your %s account from our store account.', $this->__('Vkontakte'))
            );
    }

    protected function _connectCallback()
    {
        $params = $this->getRequest()->getParams();
        ini_set('allow_url_fopen', 1);
        if(isset($params['code']) && !isset($params['email'])) { 
           $url = "https://oauth.vk.com/access_token?client_id=".(Mage::getStoreConfig('ehut_sociallogin/vk/app_id'))."&client_secret=".(Mage::getStoreConfig('ehut_sociallogin/vk/secret'))."&code=".$params['code']."&redirect_uri=".(Mage::getBaseUrl().'ehut_sociallogin/vk/connect/').""; 
           $params = json_decode(file_get_contents($url), true);

        }
        
        $uid = $params['user_id']; 
        $email = $params['email'];
        $token = $params['access_token'];
         $url = "https://api.vk.com/method/users.get?user_id=".$uid."&v=5.31&access_token=".$params['access_token']."&fields=first_name,last_name,sex,bdate,photo_100,online,status";
        $params = json_decode(file_get_contents($url), true);
            $params = $params['response'][0];

                    $attributeModel = Mage::getModel('eav/entity_attribute');
                    $attributegId = $attributeModel->getIdByCode('customer', 'ehut_sociallogin_vkid');
                    $attributegtoken = $attributeModel->getIdByCode('customer', 'ehut_sociallogin_vktoken');

                    if($attributegId == false || $attributegtoken == false){
                        echo "Attribute `ehut_sociallogin_vkid` or `ehut_sociallogin_vktoken` not exist !";
                        exit();
                    }
                    // Facebook API green light - proceed
                    $client = Mage::getSingleton('ehut_sociallogin/vk_client');

                    $customersByVkId = Mage::helper('ehut_sociallogin/vk')
                        ->getCustomersByVkId($uid);

                    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                        // Logged in user
                        if ($customersByVkId->count()) {
                            // Facebook account already connected to other account - deny
                            Mage::getSingleton('core/session')
                                ->addNotice(
                                    $this->__('Your %s account is already connected to one of our store accounts.', $this->__('Vkontakte'))
                                );

                            return;
                        }

                        // Connect from account dashboard - attach
                        $customer = Mage::getSingleton('customer/session')->getCustomer();

                        Mage::helper('ehut_sociallogin/vk')->connectByVkId(
                            $customer,
                            $uid,
                             $token
                        );

                        Mage::getSingleton('core/session')->addSuccess(
                            $this->__('Your %1$s account is now connected to your store account. You can now login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Vkontakte'))
                        );

                        return;
                    }

                    if ($customersByVkId->count()) {
                        // Existing connected user - login
                        $customer = $customersByVkId->getFirstItem();

                        Mage::helper('ehut_sociallogin/vk')->loginByCustomer($customer);

                        Mage::getSingleton('core/session')
                            ->addSuccess(
                                $this->__('You have successfully logged in using your %s account.', $this->__('Vkontakte'))
                            );

                        return;
                    }

                    $customersByEmail = Mage::helper('ehut_sociallogin/vk')
                        ->getCustomersByEmail($email);

                    if($customersByEmail->count()) {
                        // Email account already exists - attach, login
                        $customer = $customersByEmail->getFirstItem();

                        Mage::helper('ehut_sociallogin/vk')->connectByVkId(
                            $customer,
                            $uid,
                            $token
                        );

                        Mage::getSingleton('core/session')->addSuccess(
                            $this->__('We have discovered you already have an account at our store. Your %s account is now connected to your store account.', $this->__('Vkontakte'))
                        );

                        return;
                    }

                    // New connection - create, attach, login
                    if (empty($params['first_name'])) {
                        // throw new Exception(
                        //     $this->__('Sorry, could not retrieve your %s first name. Please try again.', $this->__('Vkontakte'))
                        // );
                        $params['first_name'] = "";
                    }

                    if (empty($params['last_name'])) {
                        // throw new Exception(
                        //     $this->__('Sorry, could not retrieve your %s last name. Please try again.', $this->__('Vkontakte'))
                        // );

                        $params['last_name'] = "";
                    }

                    Mage::helper('ehut_sociallogin/vk')->connectByCreatingAccount(
                        $email,
                        $params['first_name'],
                        $params['last_name'],
                        $uid,
                        $token
                    );

                    Mage::getSingleton('core/session')->addSuccess(
                        $this->__('Your %1$s account is now connected to your new user accout at our store. Now you can login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Vkontakte'))
                    );
        
          $this->_redirectReferer(); 
      
    }

}
