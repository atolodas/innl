<?php
/*
NOTICE OF LICENSE

This source file is subject to the NekloEULA that is bundled with this package in the file ICENSE.txt.

It is also available through the world-wide-web at this URL: http://store.neklo.com/LICENSE.txt

Copyright (c)  Neklo (http://store.neklo.com/)
*/

class Neklo_ShareButtons_Block_Widget_Button_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getConfig()->isEnabled() && $this->getData('is_enabled');
    }

    public function getTitleType()
    {
        if (!$this->hasData('title_page')) {
            return Neklo_ShareButtons_Model_Source_Widget_Title_Type::PAGE_CODE;
        }
        return $this->getData('title_page');
    }

    public function getTitle()
    {
        if (!$this->hasData('title') || $this->getData('title') == Neklo_ShareButtons_Model_Source_Widget_Title_Type::PAGE_CODE) {
            return $this->getPageTitle();
        }
        return $this->getData('title');
    }

    public function getPageTitle()
    {
        return $this->getLayout()->getBlock('head')->getTitle();
    }

    /**
     * @return bool
     */
    public function isFacebookEnabled()
    {
        if (!$this->hasData('is_facebook_enabled')) {
            return false;
        }
        return !!$this->getData('is_facebook_enabled');
    }

    /**
     * @return bool
     */
    public function isTwitterEnabled()
    {
        if (!$this->hasData('is_twitter_enabled')) {
            return false;
        }
        return !!$this->getData('is_twitter_enabled');
    }

    public function getTwitterHashtags()
    {
        if (!$this->hasData('twitter_hashtags')) {
            return null;
        }
        return $this->getData('twitter_hashtags');
    }

    public function getTwitterVia()
    {
        if (!$this->hasData('twitter_via')) {
            return null;
        }
        return $this->getData('twitter_via');
    }

    /**
     * @return bool
     */
    public function isGooglePlusEnabled()
    {
        if (!$this->hasData('is_googleplus_enabled')) {
            return false;
        }
        return !!$this->getData('is_googleplus_enabled');
    }

    /**
     * @return bool
     */
    public function isPinterestEnabled()
    {
        if (!$this->hasData('is_pinterest_enabled')) {
            return false;
        }
        return !!$this->getData('is_pinterest_enabled');
    }

    /**
     * @return bool
     */
    public function isRedditEnabled()
    {
        if (!$this->hasData('is_reddit_enabled')) {
            return false;
        }
        return !!$this->getData('is_reddit_enabled');
    }

    /**
     * @return bool
     */
    public function isDeliciousEnabled()
    {
        if (!$this->hasData('is_delicious_enabled')) {
            return false;
        }
        return !!$this->getData('is_delicious_enabled');
    }

    public function getDeliciousProvider()
    {
        if (!$this->hasData('delicious_provider')) {
            return null;
        }
        return $this->getData('delicious_provider');
    }

    /**
     * @return bool
     */
    public function isEvernoteEnabled()
    {
        if (!$this->hasData('is_evernote_enabled')) {
            return false;
        }
        return !!$this->getData('is_evernote_enabled');
    }

    /**
     * @return bool
     */
    public function isStumbleUponEnabled()
    {
        if (!$this->hasData('is_stumbleupon_enabled')) {
            return false;
        }
        return !!$this->getData('is_stumbleupon_enabled');
    }

    /**
     * @return bool
     */
    public function isDiggEnabled()
    {
        if (!$this->hasData('is_digg_enabled')) {
            return false;
        }
        return !!$this->getData('is_digg_enabled');
    }

    public function getShareUrl()
    {
        return Mage::helper('core/url')->getCurrentUrl();
    }

    /**
     * @return Neklo_ShareButtons_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('neklo_sharebuttons/config');
    }
}