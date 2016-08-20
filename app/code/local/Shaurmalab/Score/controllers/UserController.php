<?php

/**
 * User controller
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 */
class Shaurmalab_Score_UserController extends Mage_Core_Controller_Front_Action
{

    /**
     * Initialize requested customer object
     *
     * @return Shaurmalab_Score_Model_Customer
     */
    protected function _initCustomer()
    {
        //  $categoryId = (int) $this->getRequest()->getParam('category', false);
        $id = (int)$this->getRequest()->getParam('id');
        return Mage::getModel('score/customer')->load($id);
    }

    public function viewAction()
    {


        if ($customer = $this->_initCustomer()) {
            $title = Mage::helper('core')->__('View ' . $customer->getGroup());
            if (Mage::registry('head_title')) Mage::unregister('head_title');
            Mage::register('head_title', $title);
            if ($customer->availableForSave()) {


                $this->loadLayout();
                $this->getLayout()->getBlock('profile')->setCustomer($customer);
                $this->_initLayoutMessages('score/session');
                $this->_initLayoutMessages('customer/session');
                $this->renderLayout();
            } else {
                $this->_getSession()->addError($this->__('User can not be edited by you'));
                $this->_redirectReferer();
                return;

            }
        } else {
            $this->_getSession()->addError($this->__('Something went wrong'));
            $this->_redirectReferer();
            return;

        }
    }

    public function assignAction()
    {
        if ($customer = $this->_initCustomer()) {
            if ($customer->availableForSave()) {
                $this->loadLayout();
                $this->getLayout()->getBlock('assignForm')->setCustomer($customer);
                $this->getLayout()->getBlock('assignForm')->setAssignTo(Mage::app()->getRequest()->getParam('to'));
                $this->_initLayoutMessages('score/session');
                $this->_initLayoutMessages('customer/session');
                $this->renderLayout();
            } else {
                $this->_getSession()->addError($this->__('User can not be edited by you'));
                $this->_redirectReferer();
                return;

            }
        } else {
            $this->_getSession()->addError($this->__('Something went wrong'));
            $this->_redirectReferer();
            return;

        }
    }

    public function editAction()
    {
        if ($customer = $this->_initCustomer()) {
            if ($customer->availableForSave()) {
                $this->loadLayout();
                $this->getLayout()->getBlock('editForm')->setFormCode($customer->getGroup() . "_reg")->setCustomer($customer);
                $this->_initLayoutMessages('score/session');
                $this->_initLayoutMessages('customer/session');
                $this->renderLayout();
            } else {
                $this->_getSession()->addError($this->__('User can not be edited by you'));
                $this->_redirectReferer();
                return;

            }
        } else {
            $this->_getSession()->addError($this->__('Something went wrong'));
            $this->_redirectReferer();
            return;

        }


    }

    public function addAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('score/session');
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Display customer image action
     *
     * @deprecated
     */
    public function imageAction()
    {
        /*
         * All logic has been cut to avoid possible malicious usage of the method
         */
        $this->_forward('noRoute');
    }


    /**
     * Initialize customer from request parameters
     *
     * @return Shaurmalab_Score_Model_customer
     */
    protected function _initcustomerobj()
    {
        $customerId = (int)$this->getRequest()->getParam('id');
        $customer = Mage::helper('score/customer')->resetCustomer($customerId);

        if ($customerId) {
            try {
                $customer->load($customerId);
                if ($customer->getOwner() != Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                    $customer = Mage::helper('score/customer')->resetCustomer();
                }
            } catch (Exception $e) {
                $customer->setTypeId(Shaurmalab_Score_Model_Customer_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');

        return $customer;
    }


    /**
     * Save customer action
     */
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store');
        $data = $this->getRequest()->getPost();
        if (!isset($data['id'])) $data['id'] = 0;
        $customerId = $data['id'];

        if (!isset($data['email'])) {
            $data['email'] = date('Ymdhis') . '@mail.com';
        }

        if (!isset($data['password']) || !$data['password']) {
            $data['password'] = '123123123';
            $data['confirmation'] = '123123123';
        }

        if (isset($data['username']) && $data['username']) {
            $username = explode('-', $data['username']);
            $lastLetter = array_pop($username);
            if (strlen($lastLetter) != 1) {
                $this->_addSessionError(array('Please add "-letter of questionaire" at the end of Pupil ID'));
                return false;
            }

            $username = implode('-', $username);
            $users = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('group_id', 6)->addAttributeToFilter('username', array('like' => $username . '-%'));
            if (count($users)) {
                $oldUser = $users->getFirstItem();
                $data['inusername'] = $data['username'];
                $data['username'] = trim(str_replace($data['username'], '', $oldUser->getData('username')), ',') . ',' . $data['username'];
                $user = Mage::getModel('customer/customer')->load($oldUser->getId());
                if ($user->getSchoolId() != $data['school_id']) {
                    $this->_getSession()->addError($this->__('Pupil with similar ID is already registered in other school'));
                    return;
                }
                $user->addData($data);
                $user->save();
//echo "insert into register values (id,'{$data['inusername']}',{$user->getSchoolId()},NOW(),NOW())"; die;
                Mage::getModel('score/oggetto')->getCollection()->getConnection()->query("insert into register values (id,'{$data['inusername']}',{$user->getSchoolId()},NOW(),NOW())");
                $this->_getSession()->addSuccess($this->__('New questionnarie shared to pupil ID "' . $user->getUsername() . '"'));
                $this->_redirectReferer();
                return true;
            }
        }

        if (isset($data['mobile']) && $data['mobile']) {

            if (!preg_match('/\d\d\d\d\d \d\d\d\d\d\d/', $data['mobile'])) {
                $this->_addSessionError(array('Mobile number format is wrong'));
                return false;
            }
        }

        if ($data) {
            $customer = Mage::getModel('score/customer')->load($data['id']);

            unset($data['id']);

            $initialEmail = $customer->getEmail();
            $customer->addData($data);
            try {
                $errors = $this->_getCustomerErrors($customer, $this->getRequest()->getPost('form_code'));

                if (empty($errors)) {
                    $redirectBack = false;
                    $customer->setSendemailStoreId(Mage::app()->getStore()->getId());
                    if (isset($data['is_active']) && $data['is_active'] !== 0) {
                        $customer->setForceConfirmed(true);
                    }
                    $customer->save();
                    if (isset($data['username']) && $data['username']) {
                        Mage::getModel('score/oggetto')->getCollection()->getConnection()->query("insert into register values (id,'{$data['username']}',{$customer->getSchoolId()},NOW(),NOW())");
                    }


                    if (isset($data['is_active']) && !$data['is_active'] && !$customerId) {
                        $customer->sendNewAccountEmail('confirmation', '', $customer->getSendemailStoreId());
                    } elseif (!$customerId) {
                        if ((isset($data['email']) && $data['email'] != $initialEmail) || isset($data['password'])) {
                            $customer->sendNewAccountEmail(
                                'registered',
                                '',
                                $customer->getSendemailStoreId()
                            );
                        }
                    }

                    if (!$customerId) {
                        Mage::dispatchEvent(
                            'score_customer_create',
                            array('customer' => $customer, 'request' => $this->getRequest())
                        );
                    }

                    $data = $this->getRequest()->getPost();
                    $groupName = $customer->getGroupName();
                    if (isset($data['id']) && $data['id'] > 0) {
                        $this->_getSession()->addSuccess($groupName . ' account was saved');
                    } else {
                        $this->_getSession()->addSuccess($this->__($groupName . ' account was created'));

                    }

                    $customerId = $customer->getId();
                    if ($parentId = $this->getRequest()->getParam('parent_id')) {
                    } else {
                    }
                } else {
                    $this->_addSessionError($errors);
                }
            } catch (Mage_Core_Exception $e) {

                $this->_getSession()->addError($e->getMessage())
                    ->setcustomerData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }


        $this->_redirectReferer();
    }

    public function deleteAction()
    {
        $customerId = $this->getRequest()->getParam('id');

        $ogg = Mage::getModel('score/customer')->load($customerId);
        $groupName = $ogg->getGroupName();
        $redirectUrl = '';
        if ($ogg->getSchoolId()) {
            $redirectUrl = Mage::getBaseUrl() . 'score/oggetto/view/id/' . $ogg->getSchoolId();
        }
        try {
            $success = 0;
            if ($ogg->availableForSave()) {
                $ogg->delete();
                $this->_getSession()->addSuccess($this->__($groupName . ' account was deleted'));
                $success = 1;
            } else {
                $this->_getSession()->addError($this->__('This ' . $groupName . ' account can not be deleted by you'));

            }
        } catch (Exception $e) {
            $success = 0;
            $this->_getSession()->addError($this->__($groupName . ' account is NOT deleted. ' . $e->getMessage()));
            Mage::log($e->getMessage(), null, 'system.log');
        }
        if (!$success || !$redirectUrl) {
            $this->_redirectReferer();
        } else {
            $this->_redirectSuccess($redirectUrl);
        }
    }


    private function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Validate customer data and return errors if they are
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array|string
     */
    protected function _getCustomerErrors($customer, $form_code)
    {
        $errors = array();
        $request = $this->getRequest();
        $customerData = $customer->getData();
        $customerErrors = $customer->validate();
        if (is_array($customerErrors)) {
            $errors = array_merge($customerErrors, $errors);
        }
        return $errors;
    }

    /**
     * Add session error method
     *
     * @param string|array $errors
     */
    protected function _addSessionError($errors)
    {
        $session = $this->_getSession();
        $session->setCustomerFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($errorMessage);
            }
        } else {
            $session->addError($this->__('Invalid customer data'));
        }
    }

    /**
     * Send confirmation link to specified email
     */
    public function confirmationAction()
    {
        $customer = Mage::getModel('customer/customer');


        // try to confirm by email
        $email = $this->getRequest()->getParam('email');
        if ($email) {
            try {
                $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
                $customer->setConfirmation(date('his'))->save();
                if (!$customer->getId()) {
                    throw new Exception('');
                }

                $customer->sendNewAccountEmail('confirmation', '', Mage::app()->getStore()->getId());
                $this->_getSession()->addSuccess($this->__('User will get email with confirmation key.'));

                $this->_getSession()->setUsername($email);
                $this->_redirectReferer();
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Wrong email.'));
                $this->_redirectReferer();
            }
            return;
        }
    }

    public function loginAction() { 
        $params = $this->getRequest()->getParams();
        if(isset($params['email']) && isset($params['pass']) && isset($params['app'])) { 
            if(!$params['email']) { echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Username is required.'))); return; } 
            if(!$params['pass']) { echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Password is required.'))); return; } 
            if($params['app'] != 'android') {  echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('This app is not authorized.'))); return; }

            $customerModel = Mage::getModel('customer/customer');
            $customer = Mage::getModel('customer/customer')->loadByEmail($params['email']);

            if(!$customer->validatePassword($params['pass'])) { 
                    echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Wrong customer account specified.'))); return;
            }

            if(!$customer->getId()) {
                echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Wrong customer account specified.'))); return;
            } else {
                echo Mage::helper('core')->jsonEncode(array('id'=>$customer->getId())); return;
            }
        } else { 
            echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Something went wrong.'))); return;
        }
    }

    public function createAction() { 
        $params = $this->getRequest()->getParams();
        try { 
            if(isset($params['email']) && isset($params['password']) && isset($params['app'])) { 
                if($params['app'] != 'android') {  echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('This app is not authorized.'))); return; }
                if(!$params['email']) { echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Username is required.'))); return; } 
                if(!$params['password']) { echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Password is required.'))); return; } 
                
                $customer = Mage::getModel('customer/customer')->load(0);
                $customer->addData($params);

                $errors = $this->_getCustomerErrors($customer, 'customer_account_create');
               
                $existed = Mage::getModel('customer/customer')->loadByEmail($params['email']);
                if($existed->getId()) { 
                       $url = $this->_getUrl('customer/account/forgotpassword');
                     echo Mage::helper('core')->jsonEncode(array('error'=> $this->__('There is already an account with this email address.', $url)
               )); return;
                }

                if (empty($errors)) {
                    $redirectBack = false;
                    $customer->setSendemailStoreId(Mage::app()->getStore()->getId());
                    if (isset($data['is_active']) && $data['is_active'] !== 0) {
                        $customer->setForceConfirmed(true);
                    }
                    
                    if($customer->save()) { 
                        if ((isset($data['email']) && $data['email'] != $initialEmail) || isset($data['password'])) {
                            $customer->sendNewAccountEmail(
                                'registered',
                                '',
                                $customer->getSendemailStoreId()
                            );
                        }
                   
                        if (!$customerId) {
                            Mage::dispatchEvent(
                                'score_customer_create',
                                array('customer' => $customer, 'request' => $this->getRequest())
                            );
                        }

                        $customerId = $customer->getId();
                        echo Mage::helper('core')->jsonEncode(array('id'=>$customer->getId())); return;
                    } else { 
                        Mage::helper('core')->jsonEncode(array('error'=>$this->__('Something went wrong.'))); return;
                    }
                } else {
                    echo Mage::helper('core')->jsonEncode(array('error'=>$errors));
                }
            } else { 
                echo Mage::helper('core')->jsonEncode(array('error'=>$this->__('Something went wrong.'))); return;
            }
        } catch (Exceptiuon $e) { 
              echo Mage::helper('core')->jsonEncode(array('error'=>$e->getMessage()));  return;
        }
    }


    /**
     * Get Url method
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }

    

}
