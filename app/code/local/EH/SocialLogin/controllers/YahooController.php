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

class EH_SocialLogin_YahooController extends Mage_Core_Controller_Front_Action
{
    protected $referer = null;
    protected $flag = null;

    public function requestAction()
    {
        $client = Mage::getSingleton('ehut_sociallogin/yahoo_client');
        if(!($client->isEnabled())) {
            Mage::helper('ehut_sociallogin')->redirect404($this);
        }

        $client->fetchRequestToken();
    }

    public function connectAction()
    {
        try {
            $this->_connectCallback();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if(!empty($this->referer)) {
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

        } else {
            Mage::helper('ehut_sociallogin')->redirect404($this);
        }
    }

    public function disconnectAction()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        try {
            $this->_disconnectCallback($customer);
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        if(!empty($this->referer)) {
            $this->_redirectUrl($this->referer);
        } else {
            Mage::helper('ehut_sociallogin')->redirect404($this);
        }
    }

    protected function _disconnectCallback(Mage_Customer_Model_Customer $customer) {
        $this->referer = Mage::getUrl('ehut_sociallogin/account/yahoo');

        Mage::helper('ehut_sociallogin/yahoo')->disconnect($customer);

        Mage::getSingleton('core/session')
            ->addSuccess(
                $this->__('You have successfully disconnected your %s account from our store account.', $this->__('Yahoo'))
            );
    }

    protected function _connectCallback() {
        $attributeModel = Mage::getModel('eav/entity_attribute');
        $attributegId = $attributeModel->getIdByCode('customer', 'ehut_sociallogin_yid');
        $attributegtoken = $attributeModel->getIdByCode('customer', 'ehut_sociallogin_ytoken');
        if($attributegId == false || $attributegtoken == false){
            echo "Attribute `ehut_sociallogin_yid` or `ehut_sociallogin_ytoken` not exist";
            exit();
        }

        if (!($params = $this->getRequest()->getParams())
            ||
            !($requestToken = unserialize(Mage::getSingleton('core/session')
                ->getYahooRequestToken()))
        ) {
            // Direct route access - deny
            return;
        }

        $this->referer = Mage::getSingleton('core/session')->getYahooRedirect();

        if(isset($params['denied'])) {
            unset($this->referer);
            $this->flag = "noaccess";
            echo '<script type="text/javascript">window.close();</script>';
            return;
        }

        $client = Mage::getSingleton('ehut_sociallogin/yahoo_client');

        $token = $client->getAccessToken();
        $userInfo = $client->api($client->getXoauthYahooGuid());
        $customersByYahooId = Mage::helper('ehut_sociallogin/yahoo')
            ->getCustomersByYahooId($userInfo->id);
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            // Logged in user
            if($customersByYahooId->count()) {
                // Yahoo account already connected to other account - deny
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Your %s account is already connected to one of our store accounts.', $this->__('Yahoo'))
                    );

                return;
            }

            // Connect from account dashboard - attach
            $customer = Mage::getSingleton('customer/session')->getCustomer();

            Mage::helper('ehut_sociallogin/yahoo')->connectByYahooId(
                $customer,
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Your %1$s account is now connected to your store account. You can now login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Yahoo'))
            );

            return;
        }

        if($customersByYahooId->count()) {
            // Existing connected user - login
            $customer = $customersByYahooId->getFirstItem();

            Mage::helper('ehut_sociallogin/yahoo')->loginByCustomer($customer);

            Mage::getSingleton('core/session')
                ->addSuccess(
                    $this->__('You have successfully logged in using your %s account.', $this->__('Yahoo'))
                );

            return;
        }

        $customersByEmail = Mage::helper('ehut_sociallogin/yahoo')
            ->getCustomersByEmail($userInfo->profile->emails[0]->handle);
        if($customersByEmail->count()) {
            // Email account already exists - attach, login
            $customer = $customersByEmail->getFirstItem();
            Mage::helper('ehut_sociallogin/yahoo')->connectByYahooId(
                $customer,
                $userInfo->id,
                $token
            );

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('We have discovered you already have an account at our store. Your %s account is now connected to your store account.', $this->__('Yahoo'))
            );

            return;
        }

        // New connection - create, attach, login
        if(empty($userInfo->profile->familyName)) {
            throw new Exception(
                $this->__('Sorry, could not retrieve your %s last name. Please try again.', $this->__('Yahoo'))
            );
        }
        if(empty($userInfo->profile->givenName)) {
            throw new Exception(
                $this->__('Sorry, could not retrieve your %s first name. Please try again.', $this->__('Yahoo'))
            );
        }

        Mage::helper('ehut_sociallogin/yahoo')->connectByCreatingAccount(
            $userInfo->profile->emails[0]->handle,
            $userInfo->profile->givenName,
            $userInfo->profile->familyName,
            $userInfo->id,
            $token
        );

        Mage::getSingleton('core/session')->addSuccess(
            $this->__('Your %1$s account is now connected to your new user accout at our store. Now you can login using our %1$s Connect button or using store account credentials you will receive to your email address.', $this->__('Yahoo'))
        );
    }


}
