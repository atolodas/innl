<?php
/**
 * Diglin
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
 * @category    Diglin
 * @package     Diglin_Username
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Diglin_Username_Model_Customer extends Mage_Customer_Model_Customer {

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {   

        if(Zend_Validate::is($login, 'EmailAddress')){
            $this->loadByEmail($login);
        } else {
            $this->loadByUsername($login);   
        }
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if(Zend_Validate::is($login, 'EmailAddress') || $password) {
            if (!$this->validatePassword($password)) {
                throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                    self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
                );
            }
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
            'model'    => $this,
            'password' => $password,
        ));
        

        return true;
    }
        
    /**
     * Load customer by username
     *
     * @param   string $customerUsername
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByUsername($customerUsername)
    {
        $this->_getResource()->loadByUsername($this, $customerUsername);
        return $this;
    }
    
    /**
     * Test if username already exists
     * 
     * @param string $username
     * @param int $websiteId
     * @return Diglin_Username_Model_Customer|boolean
     */
    public function customerUsernameExists($username, $websiteId = null)
    {
        if(!is_null($websiteId)){
            $this->setWebsiteId($websiteId);
        }
        
        $this->loadByUsername($username);
        if ($this->getId()) {
            return $this;
        }
        return false;
    }

    public function getUsername() {
        $username = $this->getData('username');
        $names = explode(',',$username);
        $new_names = array();
        foreach($names as $name) {
            $arr = explode('-',$name);
            if(count($arr)>1) array_pop($arr);
            $new = implode('-',$arr);
            $new_names[] = $new;
        }

        return implode(',',array_unique($new_names));
    }
}