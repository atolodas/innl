<?php
/**
 * TinyBrick Commercial Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the TinyBrick Commercial Extension License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.delorumcommerce.com/license/commercial-extension
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tinybrick.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this package to newer
 * versions in the future.
 *
 * @category   TinyBrick
 * @package    TinyBrick_LightSpeed
 * @copyright  Copyright (c) 2010 TinyBrick Inc. LLC
 * @license    http://store.delorumcommerce.com/license/commercial-extension
 */

class TinyBrick_LightSpeed_Block_Page_Html extends Mage_Page_Block_Html
{
    const DEFAULT_CATALOG_LIMIT = 48;
    const DEFAULT_CATALOG_SORT_ORDER = 'position';
    static private $isCookieNew = true;
    static private $sessionType = '';
    static private $rawSession = '';
    static private $session = '';
    static private $sessionConfig = array();
    static private $cacheEngine = '';
    static private $cacheData = array();
    static private $database = array();
    static private $conditions = array(); // loggedin, cart
    static private $initConditions = false;
    static private $holeContent = array();
    static private $request_path = '';
    static private $debugMode = false;
    static private $multiCurrency = false;
    static private $storeCode = false;
    static private $defaultCurrencyCode = '';
    static private $limit = false;
    static private $sort = false;

    protected function _construct()
    {
        if (isset($_GET['debug_back']) && $_GET['debug_back'] == '1') {
            $this->setIsDebugMode(true);
        }
        return parent::_construct();
    }

    public function cachePage($x0b = '', $x0c = '', $x0d = '')
    {

        $pageUrl = $this->getRequest()->getRequestUri();

        foreach (Mage::getModel('cms/page')->getCollection() as $page) {
            if ($page->getIdentificator() == Mage::getBaseUrl()) {
                return false;
            }
            if (substr_count($pageUrl, $page->getIdentificator() . '.html') > 0) {
                return false;
            }
        }


        $this->setCachePage(true);
        $this->setExpires(($x0b) ? $x0b : false);
        $this->setDisqualifiers($x0c);
        $this->setDisqualifiedContentPath($x0d);
        $this->setAggregateTags(array('MAGE'));
        return $this;
    }

    protected function x0b()
    {
        if ($x0e = $this->getLayout()->getAllBlocks()) {
            $x0f = $this->getAggregateTags();
            foreach ($x0e as $x10) {
                $x11 = $x10->getCacheTags();
                if (!is_array($x11)) {
                    $x11 = array($x11);
                }
                foreach ($x11 as $x12) {
                    $x12 = strtoupper($x12);
                    if (is_array($x0f) && !in_array($x12, $x0f)) {
                        $x0f[] = $x12;
                    }
                }
            }
            $this->setAggregateTags($x0f);
        }
    }

    protected function _afterToHtml($x13)
    {
        $x14 = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (!isset($_GET['NOCACHE'])) { // && Mage::app()->useCache('lightspeed')) {
            if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') && substr_count($x14, 'helpdesk') == 0 && substr_count($x14, 'contacts') == 0 && substr_count($x14, 'contact-us') == 0) {
//  if(!$messages = Mage::getModel('core/message_collection')->getItems()) {
//              if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                 //   if (Mage::getSingleton('checkout/cart')->getItemsCount() < 1) {
                        //if (!$this->x0c()) {
                            //        Removing variables from key
                            $arr = parse_url($x14); // path and query variables
                            if (isset($arr['query'])) {
                                parse_str($arr['query'], $data);
                                $remove = explode(',', Mage::getStoreConfig('dfv/options/remove'));
                                $variables = array();
                                foreach ($data as $k => $value) {
                                    if (in_array($k, $remove)) { //  || $value == ''
                                        unset($data[$k]);
                                    } else {
                                        $variables[] = $k . '=' . $value;
                                    }
                                }
                                $arr['query'] = implode('&', $variables);
                            }
                            $x14 = $arr['path'];
                            if (isset($arr['query']) && $arr['query'] != '') {
                                $x14 .= '?' . $arr['query'];
                            }

                            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                                $x14 = 'SECURE_' . $x14;
                            }
                            $x14 = preg_replace('/(\?|&|&&)debug_back=1/s', '', $x14);
                            if ($this->x0e()) {
                                $x14 .= '_' . Mage::app()->getStore()->getCurrentCurrencyCode();
                            }


                            $limit = self::getLimit($x14);
                            if (substr_count($x14, '/rugs-') == 0) {
                                if ($limit) {
                                   $x14 .= '_' . $limit;
                                }
                            }

                            Mage::app()->setUseSessionVar(false);
                            $x13 = Mage::getSingleton('core/url')->sessionUrlVar($x13);
                            $x15 = array((string)$x13, $this->getDisqualifiers(), $this->getDisqualifiedContentPath());
                            $this->x0b();
			$md = md5($x14);
    			$md5 = $md[0].'/'.$md[1].'/'.$md;
                            $this->x10("saving page with key: $x14  " . $md5, true);
                            Mage::getSingleton('lightspeed/server')->save($x14, $x15, $this->getExpires(), $this->getAggregateTags());
                           $record = Mage::getModel('cacherecords/cacherecords')->getCollection()->addFieldToFilter('md5key', $md5)->getFirstItem();

                            if (is_object($record)) {
                                $record->setUrl($x14);
                                $record->setMd5key($md5);
                                $title = '';
                                if (is_file(Mage::getBaseDir() . '/var/lightspeed/' . $md5)) {
                                    $file = file_get_contents(Mage::getBaseDir() . '/var/lightspeed/' . $md5);

                                    if (preg_match("/<title>(.+)<\/title>/i", $file, $m)) {

                                        $title = $m[1];
                                    }
                                }
                                if (substr_count($title, 'Area rugs found for') != 0) {
                                    $title .= ' (' . Mage::getSingleton('catalogsearch/layer')->getProductCollection()->getSize() . ' products found)';
                                }
                                $record->setTitle($title);
                                $record->setFileExist('Yes');
                                $tags = $this->getAggregateTags();
                                if (!is_array($tags)) {
                                    $tags = array($tags);
                                }
                                $record->setMkeys(implode(',', $tags));
                                $record->setCreatedTime(now());
                                $record->save();

                            } 
                        //} else {
                        //    $this->x10("found items in the compare", true);
                        //}
//                    } else {
//                        $this->x10("found items in the cart", true);
//                    }
//                } else {
//                    $this->x10("customer is logged in", true);
//                }
//            } else {
//            $this->x10("session messages found", true);
//                }
            } else {
                $this->x10("page is secure", true);

            }
        } else {
            $this->x10("please enable the 'whole pages' cache checkbox in the cache management panel", true);
        }
        $x13 = preg_replace('/\<!\-\- +nocache.+?\-\-\>/si', "", $x13);
        $x13 = preg_replace('/\<!\-\- endnocache \-\-\>/si', "", $x13);
        return parent::_afterToHtml($x13);
    }

    protected function x0c()
    {
        $x16 = false;
        if (Mage::getSingleton('catalog/session')->getCatalogCompareItemsCount()) {
            if (Mage::getSingleton('catalog/session')->getCatalogCompareItemsCount() > 0) {
                $x16 = true;
            }
        }
        return $x16;
    }

    protected function x0d($x14)
    {
        return Mage::getStoreConfig($x14);
    }

    protected function x0e()
    {
        if ($x17 = Mage::getConfig()->getNode('lightspeed/global/multi_currency')) {
            if ($x17 == '1') {
                return true;
            }
        }
        return false;
    }

    private function x0f()
    {
        $x18 = Mage::getBaseUrl();
        if (preg_match('/127.0.0.1|localhost|192.168|local/', $x18)) {
            return true;
        }
        if ($x19 = $this->x0d('dfv/oej/nfg')) {
            if (preg_match("/$x19/", $x18)) {
                if (($x1a = $this->x0d('dfv/oej/wdf')) && $x14 = $this->x0d('dfv/oej/ntr')) {

                    if (md5($x19 . $x1a) == $x14) {
                        return true;
                    }
                }
            }
        }
        return false;
    }


    public static function getSession()
    {
        return Mage::getSingleton('catalog/session')->getData();
    }


    /*
     Returns sorting order code for category view page
     @return string
    */
    public static function getSort()
    {

        if (preg_match('/order\=(\w+)/', $_SERVER['QUERY_STRING'], $matches)) {
            return strtoupper($matches[1]);
        }


        if ($session = self::getSession()) {
            if (isset($session['sort_order']) && $session['sort_order'] != '') {
                return strtoupper($session['sort_order']);
            }
        }

        return self::DEFAULT_CATALOG_SORT_ORDER;
    }

    /*
     * Returns the items limit for category view page
      @return string
     */
    public static function getLimit($url)
    {
        if (preg_match('/limit\=(\d+)/', $_SERVER['QUERY_STRING'], $matches)) {
 			return strtoupper($matches[1]);
        }
        if ($session = self::getSession()) {
            if (isset($session['limit_page']) && $session['limit_page']) {
                return strtoupper($session['limit_page']);
            }
        }
   
     return self::getLimitFromDatabase($url);
    }

    static function getLimitFromDatabase($url)
    {
 	    return Mage::getStoreconfig('catalog/frontend/grid_per_page', Mage::app()->getStore()->getId());

    }


    private function x10($x1b, $x1c = false)
    {
        if (self::getIsDebugMode()) {
            echo "$x1b<br />";
            if ($x1c) {
                exit;
            }
        }
    }
}

?>
