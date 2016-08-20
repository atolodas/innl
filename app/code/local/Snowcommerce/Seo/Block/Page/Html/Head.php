<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Snowcommerce_Seo_Block_Page_Html_Head extends Mage_Page_Block_Html_Head {
    public function getSearchedUrls() {
        $variants = array();
        $pageURL = 'http://';
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $variants[] = urlencode('/'.str_replace(Mage::getBaseUrl(),'',$pageURL));
        $variants[] = urlencode(str_replace(Mage::getBaseUrl(),'',$pageURL));

        return $variants;
    }

    public function getContent($type) {
        $urls = $this->getSearchedUrls();
        $content = Mage::getModel('seo/seo')->getResourceCollection()
                ->addFieldToFilter('url',array('in'=>$urls));

        if(count($content)>0) {
            foreach($content as $c) {
                return $c->getData($type);
            }
        }
        else {
            return '';
        }
    }


    public function getDescription() {
        $descr =  Mage::getBlockSingleton('seo/seo')->getContent('meta_description');
        $descr = $this->escapeHtml(trim($descr, ""));

        if($descr) {
            $this->_data['description'] = $descr;
        }

        if (empty($this->_data['description'])) {
            $this->_data['description'] = Mage::app()->getLayout()->getBlock('head')->getDescription();
        }
        return $this->_data['description'];
    }


    public function getKeywords() {
        $keywords =  Mage::getBlockSingleton('seo/seo')->getContent('meta_keyword');

        if($keywords) {
            $this->_data['keywords'] = $keywords;
        }
        if (empty($this->_data['keywords'])) {
            $this->_data['keywords'] = Mage::app()->getLayout()->getBlock('head')->getKeywords();
        }
        return $this->_data['keywords'];
    }



    public function getTitle() {
        $title =  Mage::getBlockSingleton('seo/seo')->getContent('meta_title');

        if(!$title) {
           $title = Mage::app()->getLayout()->getBlock('head')->getTitle();
        }
        return htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8'));
    }

    public function getRobots()
    {
        $robots =  Mage::getBlockSingleton('seo/seo')->getContent('robots');

        if(!$robots) {
            $robots =  Mage::app()->getLayout()->getBlock('head')->getRobots();
        }
        return $robots;
    }

    public function getCanonical()
    {
        $canonical =  Mage::getBlockSingleton('seo/seo')->getContent('canonical');

        return $canonical;
    }
}
