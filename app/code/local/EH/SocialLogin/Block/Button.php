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

class EH_SocialLogin_Block_Button extends Mage_Core_Block_Template{

    protected $_buttons;

    protected function _construct(){
        parent::_construct();
        $this->_addButtons();
        $this->setTemplate('ehut_sociallogin/button.phtml');
    }

    protected function _addButtons(){
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Facebook());
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Vk());
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Google());
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Linkedin());
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Twitter());
        $this->_addButton(new EH_SocialLogin_Block_Button_Type_Yahoo());
    }

    protected function _addButton(EH_SocialLogin_Block_Button_Type $button){
        $this->_buttons[] = $button;
    }

    protected function getButtons(){
        return $this->_buttons;
    }

}
