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
// To enable debugging, uncomment the following
// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);
// require_once 'app/Mage.php';
// Mage::setIsDeveloperMode(true);
ini_set("zlib.output_compression", "On");
    ini_set("zlib.output_compression_level", "-1");
$compilerConfig = 'includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}
//sleep(5);
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
require_once 'app/Mage.php';
if (isset($_GET['NOCACHE']) || !PageCache::doYourThing()) {
    include_once('index.php');
}

class PageCache
{
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
    static private $DEFAULT_CATALOG_LIMIT = 48;
    static private $DEFAULT_CATALOG_SORT_ORDER = 'position';

    public static function doYourThing()
    {
        try {
            self::prepareDebugger();
     //       self::verifyConfigurationExists();
            self::loadConfiguration();
            self::redirectAdmin();
            self::initCookie();
            self::renderCachedPage();
            return true;
        } catch (Exception $e) {
           self::report("Error: {$e->getMessage()}", true);
            return false;
        }
    }

    public static function redirectAdmin()
    {
        // detect existance of 'admin' keyword and redirect immediately to index.php
        // todo, toss in some logic to allow custom admin url routes
        if (preg_match('/\/admin(\/|$)/', $_SERVER['REQUEST_URI']) || preg_match('/\/ln(\/|$)/', $_SERVER['REQUEST_URI'])) {
           throw new Exception("admin interface detected");
        }
    }

    public static function initCookie()
    {
        if (!isset($_COOKIE['frontend'])) {
            self::report("first time visitor, I will be creating a cookie from here");
            // create the cookie so Magento doesn't fail
            self::buildCookie();
        } else {
            self::report("not a new visitor, using old cookie");
            self::$isCookieNew = false;
        }
    }

    public static function buildCookie()
    {
        try {
            //require_once 'app/Mage.php';
            //Mage::run();
            $request = new Zend_Controller_Request_Http();
            session_set_cookie_params(
                self::getCookieLifetime()
                , self::getDefaultCookiePath()
                , $request->getHttpHost()
                , false
                , true
            );
            session_name('frontend');
            session_start();
        } catch (Exception $e) {
            self::report("{$e->getMessage()}");
        }
    }

    public static function messageExists()
    {
        $message = false;
        if (!self::$isCookieNew) {
            self::$rawSession = self::getRawSession();
            if (preg_match('/_messages.*?{[^}]*?Mage_Core_Model_Message_(Success|Error|Notice).*?}/s', self::$rawSession) > 0) {
                $message = true;
            }
        }
        return $message;
    }

    public static function initConditions()
    {
        if (self::$initConditions) {
            return;
        }
        // get the session_id from the cookie : $_COOKIE['frontend']
        if (!self::$isCookieNew) {
            $session = self::getSession();
            // see if they are a logged in customer
            if (isset($session['customer_base'])) {
                if (isset($session['customer_base']['id'])) {
                    // ensure they haven't logged out
                    if ((int)$session['customer_base']['id'] >= 1) {
                        self::$conditions[] = 'loggedin';
                    }
                }
            }
            // see if they have started a cart
            if (isset($session['checkout'])) {
                if (isset($session['checkout']['quote_id_1']) && ($quoteId = $session['checkout']['quote_id_1'])) {
                    $sql = "SELECT COUNT(*) FROM sales_flat_quote_item WHERE quote_id = $quoteId";
                    $rresult = mysqli_query(self::$database, $sql);
                    while ($rrow = mysqli_fetch_array($rresult)) {
                        if ((int)$rrow[0] >= 1) {
                            self::$conditions[] = 'cart';
                        }
                        break;
                    }
                }
            }
            //See if they have added items to a compare
            if (isset($session['catalog'])) {
                if (isset($session['catalog']['catalog_compare_items_count'])) {
                    if ($session['catalog']['catalog_compare_items_count'] > 0) {
                        self::$conditions[] = 'compare';
                    }
                }
            }

        }
        self::$initConditions = true;
    }

    public static function prepareSession()
    {
        if (!self::$session) {
            self::$session = @self::unserializeSession(self::getRawSession());
            if (!self::$session) {
                self::report("unable to parse the session, generally this is because the session has expired");
            }
        }
    }

    public static function get($key)
    {
        switch (self::$cacheEngine) {
            case 'memcached':
                return self::$cacheData['server']->get($key);
                break;
            case 'files':
 $md = md5($key);
                $filename =   $md[0].'/'.$md[1].'/'.$md; //MD5($key);
                if (is_file(self::$cacheData['path'] . "/" . $filename) && $data = @file_get_contents(self::$cacheData['path'] . "/" . $filename)) {

                    $data = unserialize(trim($data));
                    return $data;
                }
                break;
        }
        return false;
    }

    public static function getCachedPage()
    {
        $key = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $key = 'SECURE_' . $key;
        }
        $key = preg_replace('/(\?|&|&&)debug_front=1/s', '', $key);
        if (self::$multiCurrency) {
            self::report("configuration set to use multi_currency");
            $key .= '_' . self::getCurrencyCode();
        }

//        Removing variables from key
//        echo $key;
        $arr = parse_url($key); // path and query variables
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
        $key = $arr['path'];
        if (isset($arr['query']) && $arr['query'] != '') {
            $key .= '?' . $arr['query'];
        }
//        echo "<br/>".$key;

        if (substr_count($key, '/rugs-') == 0) {
            if (self::getLimit()) {
                $key .= '_' . self::getLimit();
            }
//            if(self::getSort()) {
//                $key .= '_' . self::getSort();
//            }
        }
if(isset($_COOKIE['anchor'])) {
$key .= '#'.$_COOKIE['anchor'];
setcookie("anchor", "", time()-3600);
}
 $md = md5($key);
                $filename =   $md[0].'/'.$md[1].'/'.$md; //MD5($key);
        self::report("attempting to fetch url: $key " . $filename);


        if (substr_count($key, '/checkout') == 0 && substr_count($key, '/customer') == 0
            && substr_count($key, '/product_compare') == 0 && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')
        ) {

            if ($data = self::get($key)) {
                if (self::messageExists()) {
                    self::report("a global message exists, we must not allow a cached page", true);
                    return false;
                }
                if (isset($data[1]) && $data[1]) {
                    $disqualified = true;
                    if ($data[1] == '*') { // auto disqualify when messages exist in the session
                        self::report("disqualified because the disqualifier is *");
                        $disqualified = true;
                    } else {
                        self::initConditions();
                        $disqualifiers = explode(",", $data[1]);
                        if ($count = count($disqualifiers)) {
                            for ($i = 0; $i < $count; $i++) {
                                if (in_array($disqualifiers[$i], self::$conditions)) {
                                    self::report("disqualified with {$disqualifiers[$i]}");
                                    $disqualified = true;
                                    break 1;
                                }
                            }
                        }
                    }
                    if ($disqualified) {
                        // handle dynamic content retrieval here
                        if (isset($data[2]) && $data[2]) {


                            self::report("1 attempting to retrieve hole punched content from {$data[2]}");
                            $_SERVER['originalRequestUri'] = $_SERVER['REQUEST_URI'];
                            $_SERVER['REQUEST_URI'] = self::$request_path . "/" . $data[2];
 if (isset($_GET['FULLCACHE'])) {
                                    return   $data[0];
                                } else {
                                    ob_start();
                                    Mage::run();
                                    $content = ob_get_clean();
                                    self::$holeContent = Zend_Json::decode($content);
                                    return self::fillNoCacheHoles($data[0]);
                                }                        
} else {
                            $data[2] = 'lightspeedcontent/hole/index';
                            self::report("2 attempting to retrieve hole punched content from {$data[2]}");
                            $_SERVER['originalRequestUri'] = $_SERVER['REQUEST_URI'];
                            $_SERVER['REQUEST_URI'] = self::$request_path . "/" . $data[2];

                            ob_start();
                            Mage::run();
                            $content = ob_get_clean();
                            self::$holeContent = Zend_Json::decode($content);
                            return self::fillNoCacheHoles($data[0]);
                            //self::report("valid disqualifiers without hole punch content... bummer", true);
                            //return false;
                        }
                    } else {
                        //return $data[0];
                        $data[2] = 'lightspeedcontent/hole/index';
                        self::report("3 attempting to retrieve hole punched content from {$data[2]}");
                        $_SERVER['originalRequestUri'] = $_SERVER['REQUEST_URI'];
                        $_SERVER['REQUEST_URI'] = self::$request_path . "/" . $data[2];
                        ob_start();
                        Mage::run();
                        $content = ob_get_clean();
                        self::$holeContent = Zend_Json::decode($content);
                        return self::fillNoCacheHoles($data[0]);
                    }
                } else {
                    //return $data[0];
                    $data[2] = 'lightspeedcontent/hole/index';
                    self::report("4 attempting to retrieve hole punched content from {$data[2]}");
                    $_SERVER['originalRequestUri'] = $_SERVER['REQUEST_URI'];
                    $_SERVER['REQUEST_URI'] = self::$request_path . "/" . $data[2];

                    ob_start();
                    Mage::run();
                    $content = ob_get_clean();
                    self::$holeContent = Zend_Json::decode($content);
                    return self::fillNoCacheHoles($data[0]);
                }
            } else {
                self::report("No match found in the cache store", true);
                return false;

            }
        } else {
            $data = self::get($key);
            return $data[0];


        }
    }

    public static function getDefaultCookiePath()
    {
        $path = "/";
        try {
            $sql = "SELECT value FROM core_config_data WHERE path = 'web/cookie/cookie_path' AND scope = 'default' AND scope_id = 0";
            $result = mysqli_query(self::$database, $sql);
            while ($row = mysqli_fetch_array($result)) {
                if (isset($row[0])) {
                    $path = $row[0];
                }
            }
        } catch (Exception $e) {

        }

        return $path;
    }

    public static function getCurrencyCode()
    {
        $currencyCode = '';
        $session = self::getSession();
        if ($session && isset($session[self::getStoreCode()])) {
            self::report("found the session data for store code: " . self::getStoreCode());
            if (isset($session[self::getStoreCode()]['currency_code'])) {
                self::report("found a currency code in the session: " + $session[self::getStoreCode()]['currency_code']);
                $currencyCode = $session[self::getStoreCode()]['currency_code'];
            }
        }
        if (!$currencyCode) {
            self::report("defaulting to default currency code: " . self::getDefaultCurrencyCode());
            $currencyCode = self::getDefaultCurrencyCode();
        }
        return $currencyCode;
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
//        if ($session = self::getSession()) {
//            if (isset($session['catalog']['sort_order']) && $session['catalog']['sort_order']!='') {
//                return strtoupper($session['catalog']['sort_order']);
//            }
//        }

        return self::$DEFAULT_CATALOG_SORT_ORDER;
    }

    /*
     * Returns the items limit for category view page

      @return string
    */
    public static function getLimit()
    {
        if (preg_match('/limit\=(\d+)/', $_SERVER['QUERY_STRING'], $matches)) {
            return strtoupper($matches[1]);
        }
        if ($session = self::getSession()) {
            if (isset($session['catalog']['limit_page'])) {
                return strtoupper($session['catalog']['limit_page']);
            }
        }
        return 28;// self::getLimitFromDatabase();
    }

    public function getLimitFromDatabase()
    {
        return Mage::getStoreconfig('catalog/frontend/grid_per_page', Mage::app()->getstore()->getId());
    }

    public static function getSession()
    {
        if (!self::$session) {
            self::prepareSession();
        }
        return self::$session;
    }

    public static function getRawSession()
    {
        if (!self::$rawSession) {
            switch (self::$sessionType) {
                case 'db':
                    $sql = "SELECT session_data FROM core_session WHERE session_id = '{$_COOKIE['frontend']}'";
                    $result = mysqli_query(self::$sessionConfig['connection'], $sql);
                    if (count($result)) {
                        while ($row = mysqli_fetch_array($result)) {
                            return $row[0];
                        }
                    }
                    break;
                case 'memcached':
                    return self::$sessionConfig['server']->get($_COOKIE['frontend']);
                    break;
                case 'files':
                default:
                    if (is_file(self::$sessionConfig['path'] . "/" . "sess_" . $_COOKIE['frontend'])) {
                        return file_get_contents(self::$sessionConfig['path'] . "/" . "sess_" . $_COOKIE['frontend']);
                        break;
                    }
            }
        }
        return self::$rawSession;
    }

    public static function unserializeSession($data)
    {
        $result = false;
        if ($data) {
            $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/', $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $numElements = count($vars);
            for ($i = 0; $numElements > $i && $vars[$i]; $i++) {
                $result[$vars[$i]] = unserialize($vars[++$i]);
            }
        }
        return $result;
    }

    public static function fillNoCacheHoles($html)
    {
        $content = preg_replace_callback('/(\<!\-\- +nocache.+?\-\-\>).*?(\<!\-\- endnocache \-\-\>)/si', 'PageCache::replaceNoCacheBlocks', $html);
        if ($content) {
            return $content;
        } else {
            return $html;
        }
    }

    public static function replaceNoCacheBlocks($matches)
    {
        // $matches[0] is the whole block
        // $matches[1] is the <!-- nocache -->
        // $matches[2] is the <!-- endnocache -->
        // print_r($matches);

        if ($matches[0] && $matches[1] != '' && $matches[2] != '') {
            $key = self::getAttributeValue('key', $matches[1]);
            if (isset(self::$holeContent[$key])) {
                return self::$holeContent[$key];
            } else {
                return $matches[0];
            }
        } else {
            return $matches[0];
        }
    }

    public static function getAttributeValue($attribute, $html)
    {
        preg_match('/(\s*' . $attribute . '=\s*".*?")|(\s*' . $attribute . '=\s*\'.*?\')/', $html, $matches);

        if (count($matches)) {
            $match = $matches[0];
            $match = preg_replace('/ +/', "", $match);
            $match = str_replace($attribute . "=", "", $match);
            $match = str_replace('"', "", $match);
            return $match;
        } else {
            return false;
        }
    }

    public static function sanitizePage($page)
    {
        $page = preg_replace('/\<!\-\- +nocache.+?\-\-\>/si', "", $page);
        $page = preg_replace('/\<!\-\- endnocache \-\-\>/si', "", $page);
        return $page;
    }

    public static function getCookieLifetime()
    {
        $lifetime = 3600;
        try {
            $sql = "SELECT value FROM core_config_data WHERE path = 'web/cookie/cookie_lifetime' AND scope = 'default' AND scope_id = 0";
            $result = mysqli_query(self::$database, $sql);
            while ($row = mysqli_fetch_array($result)) {
                if (isset($row[0])) {
                    $lifetime = (int)$row[0];
                }
            }
        } catch (Exception $e) {

        }

        return $lifetime;
    }

    public static function report($message, $term = false)
    {
        if (self::$debugMode) {
            echo "$message<br />";
            if ($term) {
                exit;
            }
        }
    }

    public static function prepareDebugger()
    {
        if (isset($_GET['debug_front']) && $_GET['debug_front'] == '1') {
            self::$debugMode = true;
        }
    }

    public static function verifyConfigurationExists()
    {
        if (!file_exists('app/etc/local.xml')) {
            throw new Exception('cannot find local.xml at app/etc/local.xml');
        }
    }

    public static function loadConfiguration()
    {
        $config = simplexml_load_file('app/etc/local.xml');
        $nodeFound = false;
        foreach ($config->children() as $child) {
            if ($child->getName() == 'lightspeed') {
                $nodeFound = true;
                foreach ($child->children() as $child2) {
                    switch ($child2->getName()) {
                        case 'global':
                            self::report("found the global db node");
                            self::$database = mysqli_connect((string)$child2->connection->host, (string)$child2->connection->username, (string)$child2->connection->password);
                            mysqli_select_db(self::$database, (string)$child2->connection->dbname);

                            self::$request_path = (string)$child2->request_path;
                            self::$request_path = rtrim(trim(self::$request_path), '/');
                            if ($child2->multi_currency) {
                                self::$multiCurrency = (int)$child2->multi_currency;
                            }
                            self::$limit = true;
                            break;
                        case 'session':
                            switch ((string)$child2->type) {
                                case 'memcached':
                                    // self::report("Session store is memcached");
                                    if (!class_exists('Memcache')) {
                                        throw new Exception('Memcache extension not installed, but configured for use in local.xml');
                                    }
                                    self::$sessionType = 'memcached';
                                    self::$sessionConfig['server'] = new Memcache();
                                    foreach ($child2->servers->children() as $server) {
                                        self::$sessionConfig['server']->addServer(
                                            (string)$server->host
                                            , (int)$server->port
                                            , (bool)$server->persistant
                                        );
                                    }
                                    break;
                                case 'db':
                                    // self::report("session store is db");
                                    self::$sessionType = 'db';
                                    self::$sessionConfig['connection'] = mysqli_connect((string)$child2->connection->host, (string)$child2->connection->username, (string)$child2->connection->password);
                                    mysqli_select_db(self::$sessionConfig['connection'], (string)$child2->connection->dbname);
                                    break;
                                case 'files':
                                default:
                                    // self::report("session store is files");
                                    self::$sessionType = 'files';
                                    self::$sessionConfig['path'] = (string)$child2->path;
                                    if (!self::$sessionConfig['path']) {
                                        self::$sessionConfig['path'] = 'var/session';
                                    }
                                    break;
                            }
                            break;
                        case 'cache':
                            switch ((string)$child2->type) {
                                case 'memcached':
                                    // self::report("cache engine is memcached");
                                    if (!class_exists('Memcache')) {
                                        throw new Exception('Memcache extension not installed, but configured for use in local.xml');
                                    }
                                    self::$cacheEngine = 'memcached';
                                    self::$cacheData['server'] = new Memcache();
                                    foreach ($child2->servers->children() as $server) {
                                        self::$cacheData['server']->addServer(
                                            (string)$server->host
                                            , (int)$server->port
                                            , (bool)$server->persistant
                                        );
                                    }
                                    break;
                                case 'files':
                                default:
                                    // self::report("cache engine is files");
                                    self::$cacheEngine = 'files';
                                    self::$cacheData['path'] = (string)$child2->path;
                                    if (!self::$cacheData['path']) {
                                        self::$cacheData['path'] = 'var/lightspeed';
                                    }
                                    break;
                            }
                            break;
                    }
                }
            }
        }

        if (!$nodeFound) {
            throw new Exception("local.xml does not contain <lightspeed> node");
        }
    }

    public static function renderCachedPage()
    {
        if ($page = self::getCachedPage()) {
            self::report("success!, I'm about to spit out a cached page, look out.", true);
            self::prepareHeaders();
            echo self::sanitizePage($page);
        } else {
            throw new Exception("no cache matches at this url.");
        }
    }

    public static function prepareHeaders()
    {
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate, no-store, post-check=0, pre-check=0");
    }

    public static function getStoreCode()
    {
        if (!self::$storeCode) {
            if (!self::getSession()) {
                self::report("session data is false, setting store code to: store_default");
                self::$storeCode = 'store_default';
            } else {
                foreach (array_keys(self::getSession()) as $_key) {
                    if (substr($_key, 0, 5) == 'store') {
                        self::$storeCode = $_key;
                        self::report("found a match in the session data for store code, setting store code to: $_key");
                        break;
                    }
                }

                self::$storeCode = 'store_default';
                self::report("setting store code to: store_default");
            }
        }
        return self::$storeCode;
    }

    public static function getDefaultCurrencyCode()
    {
        if (!self::$defaultCurrencyCode) {
            $sql = "SELECT value FROM core_config_data WHERE path = 'currency/options/default'";
            $result = mysqli_query(self::$database, $sql);
            if (count($result)) {
                while ($row = mysqli_fetch_array($result)) {
                    self::$defaultCurrencyCode = $row[0];
                }
            }
        }
        return self::$defaultCurrencyCode;
    }

    public static function getCategorySort($cid)
    {
        $sql = "SELECT value FROM catalog_category_entity_varchar WHERE attribute_id='99' AND entity_id='{$cid}'";
        $result = mysqli_query(self::$database, $sql);
        if (count($result)) {
            while ($row = mysqli_fetch_array($result)) {
                self::$sort = $row[0];
            }
        }
        return self::$sort;
    }
}
