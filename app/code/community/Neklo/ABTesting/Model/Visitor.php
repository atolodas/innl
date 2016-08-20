<?php
class Neklo_ABTesting_Model_Visitor extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('neklo_abtesting/visitor');
    }

    public function logNewVisitor()
    {
        $visitorInfo = $this->getNewVisitorInfo();

        if ($this->validateVisitor()) {
            $data = array(
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
                'customer_id' => 0,
                'visits_count' => 1,
                'visitor_info'  => $visitorInfo,
                'visitor_ip'    => $this->getRealUserIp()
            );

            $visitor = $this;

            $visitor->setData($data)
                    ->save();

            return $visitor->getId();
        }
    }

    public function updateVisitor($id)
    {
        $visitor = $this->loadByVisitorId($id);
        if ($visitor) {
            $visitorInfo = $this->getNewVisitorInfo();

            $data = array(
                'updated_at' => date('Y-m-d h:i:s'),
                'visitor_info' => $visitorInfo,
                'visits_count' => (int)$visitor->getVisitsCount() + 1,
            );


            $visitor->addData($data)
                    ->save();
        }
    }

    public function getMaxId()
    {
        $tableName = $this->getBaseTableName();

        $maxId = Mage::getSingleton('core/resource')->getConnection('core/read')
            ->query("SELECT MAX(visitor_id) as max FROM {$tableName}")
            ->fetch();

        return $maxId['max'];
    }

    public function loadByVisitorId($id)
    {
        $visitor = $this->load($id);
        if ($visitor->getCreatedAt()) {
            return $visitor;
        }
        return false;
    }

    public function updateCustomerIdInLog($customerId, $visitorId)
    {
        $tableName = $this->getBaseTableName();
        Mage::getSingleton('core/resource')->getConnection('core/write')
            ->query("UPDATE {$tableName} set customer_id = {$customerId} where visitor_id = {$visitorId}")
            ;
    }

    public function getBaseTableName()
    {
        return Mage::getSingleton('core/resource')->getTableName('neklo_abtesting/visitor');
    }

    public function getNewVisitorInfo()
    {
        $visitorInfo = array(
            'uri'   => $_SERVER['REQUEST_URI'],
            'client_ip' => $this->getRealUserIp(),
            'user_agent' => @$_SERVER['HTTP_USER_AGENT'],
            'params' => http_build_query(Mage::app()->getRequest()->getParams()),
        );

        $visitorInfo = implode(' | ', $visitorInfo);

        return $visitorInfo;
    }

    public function getRealUserIp()
    {
        switch (true) {
          case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
          case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
          case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
          default : return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function validateVisitor()
    {
        $testCookieName = 'validatingUserAgent';
        $_COOKIE[$testCookieName] = true;
        $visitorInfo = $this->getNewVisitorInfo();

        $visitorId = Mage::getSingleton('core/cookie')->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID);
        $tableName = $this->getBaseTableName();

        $visitorIp = $this->getRealUserIp();

        $action = Mage::app()->getRequest()->getActionName();

        if ($action == 'noRoute') {
            return false;
        }

        if (!Mage::getSingleton('core/cookie')->get($testCookieName)) {
            Mage::log('User agent does not support cookies', null, 'visitors.log');
            return false;
        }

        $params = array_keys(Mage::app()->getRequest()->getParams());
        if (in_array('aw_ajaxcatalog', $params)
            || in_array('isAjax', $params)) {
            return false;
        }

        list($uri, $client_ip, $user_agent) = explode(' | ', $visitorInfo);

        $uri = strtolower($uri);
        $client_ip = trim($client_ip);
        $user_agent = strtolower(trim($user_agent));


        if (preg_match('/(abot|dbot|ebot|hbot|kbot|lbot|mbot|nbot|obot|pbot|rbot|sbot|tbot|vbot|ybot|zbot|bot\.|bot\/|_bot|\.bot|\/bot|\-bot|\:bot|\(bot|crawl|slurp|spider|seek|accoona|acoon|adressendeutschland|ah\-ha\.com|ahoy|altavista|ananzi|anthill|appie|arachnophilia|arale|araneo|aranha|architext|aretha|arks|asterias|atlocal|atn|atomz|augurfind|backrub|bannana_bot|baypup|bdfetch|big brother|biglotron|bjaaland|blackwidow|blaiz|blog|blo\.|bloodhound|boitho|booch|bradley|butterfly|calif|cassandra|ccubee|cfetch|charlotte|churl|cienciaficcion|cmc|collective|comagent|combine|computingsite|csci|curl|cusco|daumoa|deepindex|delorie|depspid|deweb|die blinde kuh|digger|ditto|dmoz|docomo|download express|dtaagent|dwcp|ebiness|ebingbong|e\-collector|ejupiter|emacs\-w3 search engine|esther|evliya celebi|ezresult|falcon|felix ide|ferret|fetchrover|fido|findlinks|fireball|fish search|fouineur|funnelweb|gazz|gcreep|genieknows|getterroboplus|geturl|glx|goforit|golem|grabber|grapnel|gralon|griffon|gromit|grub|gulliver|hamahakki|harvest|havindex|helix|heritrix|hku www octopus|homerweb|htdig|html index|html_analyzer|htmlgobble|hubater|hyper\-decontextualizer|ia_archiver|ibm_planetwide|ichiro|iconsurf|iltrovatore|image\.kapsi\.net|imagelock|incywincy|indexer|infobee|informant|ingrid|inktomisearch\.com|inspector web|intelliagent|internet shinchakubin|ip3000|iron33|israeli\-search|ivia|jack|jakarta|javabee|jetbot|jumpstation|katipo|kdd\-explorer|kilroy|knowledge|kototoi|kretrieve|labelgrabber|lachesis|larbin|legs|libwww|linkalarm|link validator|linkscan|lockon|lwp|lycos|magpie|mantraagent|mapoftheinternet|marvin\/|mattie|mediafox|mediapartners|mercator|merzscope|microsoft url control|minirank|miva|mj12|mnogosearch|moget|monster|moose|motor|multitext|muncher|muscatferret|mwd\.search|myweb|najdi|nameprotect|nationaldirectory|nazilla|ncsa beta|nec\-meshexplorer|nederland\.zoek|netcarta webmap engine|netmechanic|netresearchserver|netscoop|newscan\-online|nhse|nokia6682\/|nomad|noyona|nutch|nzexplorer|objectssearch|occam|omni|open text|openfind|openintelligencedata|orb search|osis\-project|pack rat|pageboy|pagebull|page_verifier|panscient|parasite|partnersite|patric|pear\.|pegasus|peregrinator|pgp key agent|phantom|phpdig|picosearch|piltdownman|pimptrain|pinpoint|pioneer|piranha|plumtreewebaccessor|pogodak|poirot|pompos|poppelsdorf|poppi|popular iconoclast|psycheclone|publisher|python|rambler|raven search|roach|road runner|roadhouse|robbie|robofox|robozilla|rules|salty|sbider|scooter|scoutjet|scrubby|search\.|searchprocess|semanticdiscovery|senrigan|sg\-scout|shai\'hulud|shark|shopwiki|sidewinder|sift|silk|simmany|site searcher|site valet|sitetech\-rover|skymob\.com|sleek|smartwit|sna\-|snappy|snooper|sohu|speedfind|sphere|sphider|spinner|spyder|steeler\/|suke|suntek|supersnooper|surfnomore|sven|sygol|szukacz|tach black widow|tarantula|templeton|\/teoma|t\-h\-u\-n\-d\-e\-r\-s\-t\-o\-n\-e|theophrastus|titan|titin|tkwww|toutatis|t\-rex|tutorgig|twiceler|twisted|ucsd|udmsearch|url check|updated|vagabondo|valkyrie|verticrawl|victoria|vision\-search|volcano|voyager\/|voyager\-hc|w3c_validator|w3m2|w3mir|walker|wallpaper|wanderer|wauuu|wavefire|web core|web hopper|web wombat|webbandit|webcatcher|webcopy|webfoot|weblayers|weblinker|weblog monitor|webmirror|webmonkey|webquest|webreaper|websitepulse|websnarf|webstolperer|webvac|webwalk|webwatch|webwombat|webzinger|wget|whizbang|whowhere|wild ferret|worldlight|wwwc|wwwster|xenu|xget|xift|xirq|yandex|yanga|yeti|yodao|zao\/|zippp|zyborg|\.\.\.\.)/i', $user_agent)) {
            return false;
        }


        if (in_array($client_ip, array('166.78.123.106', '93.84.19.92', '104.254.65.197'))) {
            return false;
        }

        if (!$user_agent) {
            return false;
        }

        if (substr_count($user_agent, 'bot')
            || substr_count($user_agent, 'healthchecker')
            || substr_count($user_agent, 'newrelicpinger')
            || substr_count($user_agent, 'rackspace')
            || substr_count($user_agent, 'panopta')
            || substr_count($user_agent, 'netlyzer')
            || substr_count($user_agent, 'baiduspider')
            || substr_count($user_agent, 'yahoo! slurp')
            || substr_count($user_agent, 'xenu link')
            || substr_count($user_agent, 'appengine-google')
            || substr_count($user_agent, 'megaindex')
            || substr_count($user_agent, 'crawler')
            || substr_count($user_agent, 'spider')
            || substr_count($user_agent, '.php')
            || substr_count($user_agent, 'pinterest.com')
            || substr_count($user_agent, 'apachebench')
        ) {
            return false;
        }


        // Check if banned
        $query = "SELECT * FROM {$tableName} WHERE";
        $binds = array(
                'visitor_ip' => $visitorIp,
                'user_agent' => $user_agent,
        );
        if ($visitorId) {
            $query .=  " ((visitor_ip = :visitor_ip AND user_agent = :user_agent) OR visitor_id = :visitor_id)";
            $binds['visitor_id'] = $visitorId;
        } else {
            $query .=  " visitor_ip = :visitor_ip AND user_agent = :user_agent";
        }

        $bannedUser = Mage::getSingleton('core/resource')->getConnection('core/read')
                ->query($query, $binds)
                ->fetchAll();

        if (isset($bannedUser[0]) && isset($bannedUser[0]['is_banned']) && $bannedUser[0]['is_banned']) {
            return false;
        }

        return true;
    }
}
