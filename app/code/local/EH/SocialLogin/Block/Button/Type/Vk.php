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

class EH_SocialLogin_Block_Button_Type_Vk extends EH_SocialLogin_Block_Button_Type{

    protected $_class = 'fa-vk';
    protected $_title = 'Vkontakte';
    protected $_name = 'vk';
    protected $_width = 500;
    protected $_height = 270;
    protected $_disconnect = 'ehut_sociallogin/vk/disconnect';

    public function __construct($name = null, $class = null,$title=null){
        parent::__construct();

        $this->client = Mage::getSingleton('ehut_sociallogin/vk_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('ehut_sociallogin_vk_userinfo');

        // CSRF protection
        Mage::getSingleton('core/session')->setVkCsrf($csrf = md5(uniqid(rand(), TRUE)));
        $this->client->setState($csrf);

        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {
            $redirect = Mage::helper('core/url')->getCurrentUrl();
        }

        // Redirect uri
        Mage::getSingleton('core/session')->setVkRedirect($redirect);

    }

}
