<?php
class Cafepress_CPCore_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_escapeChars = array(
        '&#11;'     => 'aMp!!!!!!!!?!',
        //'&' => 'aMp2!!!!!!!!?!',
        "\n" => "!n!",
        "\r" => "!r!",
        "#"  => "!?!?!?!?!",
        "%" => "????!!!!"
    );

	public function getStatuses() {
		return array (
			1=>'Created',
			2=>'Sent',
			3=>'Received',
			4=>'Returned',
			5=>'Fail Sending'
		);
	}

    public function getStatusId($status) {
            return array_search($status,$this->getStatuses());
    }

    public function encodeXml($data) {
        return $data;
    }

    /**
     * Get month to format 01, 02, .., 12
     * @param type $month
     * @return type string
     */
    public static function formatMonth($month) {
        return sprintf('%02d', $month);
    }

    #TODO INL - URGENT - SET DATA FORMAT AS 2nd PARAM FOR THIS FUNCTION
    public static function formatDate($date) {
        $date = Mage::getModel('core/date')->timestamp($date);
        return date("m/d/y", $date);
    }

    public static function formatTime($date) {
        $date = Mage::getModel('core/date')->timestamp($date);
        return date("H:i", $date);
    }

    public function getStreetLine1($street) {
        $lines = explode('/n',$street);
        return $street[0];
    }

    public function getStreetLine2($street) {
        $lines = explode('/n',$street);
        return $street[1];
    }

    public static function roundMoney($value)
    {
//        return number_format($value*1.00, 2, '.', '');
        // ahtung! Radical solution!
        $value = $value*1.00 + 0.0000000001;
        // radical solution ends
        $value = round($value*1.00,2);
        return $value;
    }

    public static function advancedCardName($value) {
        if(($value=='AE')) {
            return 'AM';
        } elseif($value=='MC') {
            return 'MA';
        }
        return $value;
    }


    public function dataToDatetime($date)
    {
//        $date = Mage::getModel('core/date')->timestamp($date);
//        return date("Y-m-d 00:00:00", $date);
        $time = strtotime($date);
        return strftime("%Y-%m-%d 00:00:00", $time);
    }

    public static function codeShippingMethod($value)
    {

        $value = strtolower($value);
        if(($value=='ups_gnd')||($value=='freeshipping_freeshipping')) {
            return '01';
        } elseif($value=='ups_2da') {
            return '03';
        } elseif($value=='tablerate_bestway') {
            return '04';
        } else {
            return '09';
        }
    }

     public function codeShippingMethodRelastin($value)
    {
        $value = strtolower($value);
        if(($value=='flatrate_flatrate')) {
            return '01';
        } elseif($value=='tablerate_bestway') {
            return '04';
        }
        return $value;
    }

    public function getCodShippingMethod($value)
    {
//        return 'ups';
        if($value=='UPS GROUND') {
            return 'ups_GND';
        } else {
            Mage::log("Shipment method error: method \"$value\" not isset",null,'shipment.log');
            return false;
        }
    }

    public function getCodOrderStatus($value)
    {
        if($value=='Pending') {
            return 'pending';
        } else {
            return $value;
        }
    }

    public function double($data) {
        return $data*2;
    }

    public function round02($value, $round = 2)
    {
        return sprintf('%02d', $value);
    }

    /*Format date
     * from 12/02/10 11:12:18 AM
     * to   2011-10-05 13:47:14
     */
    public function formatDateShip($value)
    {
        $time = strtotime($value);
        return strftime("%Y-%m-%d %T", $time);
    }

    public function datetimeToDate($date) {
        $time = strtotime($date);
        return strftime("%Y-%m-%d", $time);
    }

     public function replaceCharsXML($xml)
    {
        $chars = array();
        $code = array();
        $i = 0;
        $xml = trim($xml);
        foreach($this->_escapeChars as $key => $val) {
            $chars[$i]  = $key;
            $code[$i]   = $val;
            $i++;
        }
        $xml = str_replace($chars,$code,$xml);
        return $xml;
    }

    public function getNumberFromCustomMetod($customNumber)
    {
		$result = Mage::getModel('sales/order')->getCollection()
			->addAttributeToSelect('increment_id')
			->addAttributeToFilter('custom_number', $customNumber)
			->getData('increment_id');
        return $result[0]['increment_id'];
    }

    public function getCountryIsoCode($code) {
        $countryData = Mage::getModel('directory/country')->loadByCode($code);
            return $countryData->getIso3Code();
    }

    public function orderRequestResponse($requestResult, $responseFormat)
    {
        $orderFormat = Mage::getModel('cpcore/xmlformat_format_order');
        $orderFormat->processResponseByRequest($requestResult, $responseFormat);
    }

    private function getFormatXml($object, &$textXml, $breakSymbol, $depth = 0) {
        $children = $object->children();
        if($depth == 0) {
            $textXml .= '&lt;'.$object->getName();
            $namespaces = $object->getDocNamespaces(true);
            if(count($namespaces) > 1) $colon = ':';
            else $colon = '';
            foreach($namespaces as $namespace => $value) {
                $textXml .= ' xmlns'.$colon.$namespace.'="'.$value.'"';
            }
            $attributes = (array)$object->attributes();
            if(isset($attributes['@attributes']))
            {
                foreach($attributes['@attributes'] as $attribute => $value) {
                    $textXml .= ' '.$attribute.'="'.$value.'"';
                }
            }
            $textXml .= '&gt;';
            if($object->count() > 1) {
                $textXml .= $breakSymbol;
            }
        }
        if (is_object($children[0])) {
            foreach ($object->children() as $node) {
                for($i = 0; $i < $depth + 1; $i++) {
                    $textXml .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $textXml .= '&lt;'.$node->getName();
                $attributes = (array)$node->attributes();
                if(isset($attributes['@attributes']))
                {
                    foreach($attributes['@attributes'] as $attribute => $value) {
                        $textXml .= ' '.$attribute.'="'.$value.'"';
                    }
                }
                $textXml .= '&gt;';

                $child = $node->children();
                if(is_object($child[0])) {
                    $textXml .= $breakSymbol;
                }
                $this->getFormatXml($node, $textXml, $breakSymbol, $depth + 1);
                if(is_object($child[0])) {
                    for($i = 0; $i < $depth + 1; $i++) {
                        $textXml .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }
                }
                $textXml .= '&lt;/'.$node->getName().'&gt;'.$breakSymbol;
            }
        } else {
            $textXml .= (string)$object;
        }
        if($depth == 0) {
            $textXml .= '&lt;/'.$object->getName().'&gt;'.$breakSymbol;
        }
    }

    public function formatXml($xml, $breakSymbol = '<br/>')
    {
        if(!$xml) {
            return '';
        }
        try{
            $sXml = @simplexml_load_string($xml);
            if(!$sXml) {
                return '<pre>'.htmlspecialchars($xml).'</pre>';
            }
            $textXml = '';
            $this->getFormatXml($sXml, $textXml, $breakSymbol, 0);
        } catch(Exception $e) {
            $textXml =  '<pre>'.htmlspecialchars($xml).'</pre>';
        }
        return $textXml;
    }

    public function isDeveloper()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/config/wms_dev');
    }

    /**
     *  XML validation by xsd
     * @param type $xml
     * @param type $validateSource
     * @return type
     */
    public function isValidXml($xml, $validateXSD)
    {
        try {
            $xmlDOM = new DOMDocument();
            $xmlDOM->loadXML($xml);

            if (@$xmlDOM->schemaValidateSource($validateXSD)) {
               return true;
            }
            return false;
        }catch(Exception $e) {
            return false;
        }


    }

    /**
     * XML is valid if it's parsed by the DOMDocument without errors
     * @param type $string
     * @return type
     */
    public function isXML($string)
    {
        if (!$string) {
            return false;
        }
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($string);

        $errors = libxml_get_errors();
        if (empty($errors))
        {
            return true;
        }
        Mage::log('XML Error:',null,'wms.log');
        Mage::log($errors,null,'wms.log');
        return false;
    }

    public function addCData($value) {
        if($value != htmlspecialchars($value) || $value != addslashes($value)) {
            return '<![CDATA['.$value.']]>';
        }
        return $value;
    }


    public static function unCdata($string)
    {
        $string = trim($string);
        if ((strpos($string,'<![CDATA[')===0) && (strrpos($string,']]>',-3))) {
            $string = substr($string,9,strlen($string)-12);
        }
        return $string;
    }

    public function checkFormatDate($prevDate, $intervalString) {
        if(!$prevDate) {
            return true;
        }
        $intervalArray = explode(' ', $intervalString);
        $intervalArray[0] = substr($intervalArray[0], 0, strlen($intervalArray[0]) -5);
        $intervalArray[1] = substr($intervalArray[1], 0, strlen($intervalArray[1]) -3);
        $intervalArray[2] = substr($intervalArray[2], 0, strlen($intervalArray[2]) -4);
        $intervalArray[3] = substr($intervalArray[3], 0, strlen($intervalArray[3]) -6);
        $newDate = strtotime($prevDate);
        if($intervalArray[3] != '*') {
            $newDate += $intervalArray[3] * 60;
        }
        if($intervalArray[2] != '*') {
            $newDate += $intervalArray[2] * 60 * 60;
        }
        if($intervalArray[1] != '*') {
            $newDate += $intervalArray[1] * 24 * 60 * 60;
        }
        if($intervalArray[0] != '*') {
            $newDate += $intervalArray[0] * 30 * 24 * 60 * 60;
        }
        if(strtotime("now") >= $newDate) {
            return true;
        } else{
            return false;
        }
    }

    public function sendMail($from, $to, $subject, $body, $file, $filecontent = null) {
        if($filecontent) {
            $attachment = chunk_split(base64_encode($filecontent));
        } else{
            $attachment = chunk_split(base64_encode(file_get_contents($file)));
        }
        $separator = md5(time());
        $eol = PHP_EOL;
        $filename = basename($file);
        $headers = "From: ".$from.$eol;
        $headers .= "MIME-Version: 1.0".$eol;
        $headers .= "Content-Type: multipart/mixed; charset=UTF-8;boundary=\"".$separator."\"".$eol.$eol;
        $headers .= "Content-Transfer-Encoding: 7bit".$eol;
        $headers .= "This is a MIME encoded message.".$eol.$eol;
        $headers .= "--".$separator.$eol;
        $headers .= "Content-Type: application/octet-stream;name=\"".$filename."\"".$eol;
        $headers .= "Content-Transfer-Encoding: base64".$eol;
        $headers .= "Content-Disposition: attachment".$eol.$eol;
        $headers .= $attachment.$eol.$eol;
        $headers .= "--".$separator;
        $headers .= "Content-Type: text/html;charset=UTF-8;boundary=\"".$separator."\"".$eol.$eol;
        $headers .= $body.$eol.$eol;
        $headers .= "--".$separator;
        $headers .= "--".$separator."--";
        $result = mail($to, $subject, $body, $headers);
        return $result;
    }

    public function dumpDb($tables_list = null, $file_path = null) {
        $server = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/host');
        $user = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/username');
        $pass = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/password');
        $db = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

        $SQL = '';

        mysql_connect($server, $user, $pass);
        mysql_select_db($db);
        $tables = mysql_list_tables($db);
        while ($td = mysql_fetch_array($tables))
        {
            if(($tables_list && in_array($td[0], $tables_list)) || (!$tables_list)) {
                $table = $td[0];
                $r = mysql_query("SHOW CREATE TABLE `$table`");
                if ($r)
                {
                    $insert_sql = "";
                    $d = mysql_fetch_array($r);
                    $d[1] .= ";";
                    $SQL[] = "DROP TABLE IF EXISTS `$table`;";
                    $SQL[] .= str_replace("\n", "", $d[1]);
                    $table_query = mysql_query("SELECT * FROM `$table`");
                    $num_fields = mysql_num_fields($table_query);
                    while ($fetch_row = mysql_fetch_array($table_query))
                    {
                        $insert_sql .= "INSERT INTO $table VALUES(";
                        for ($n=1;$n<=$num_fields;$n++)
                        {
                            $m = $n - 1;
                            $insert_sql .= "'".mysql_real_escape_string($fetch_row[$m])."', ";
                        }
                        $insert_sql = substr($insert_sql,0,-2);
                        $insert_sql .= ");\n";
                    }
                    if ($insert_sql!= "")
                    {
                        $SQL[] = $insert_sql;
                    }
                }
            }
        }
        $result = implode("\r", $SQL);
        if($file_path) {
            $file = fopen($file_path, "w");
            fwrite($file, $result);
            fclose($file);
        }
        return $result;
    }

    public function getRequestTooltips($type) {
        $result = array();
        $csv_file = @fopen('http://innativelife.com/wms/'.$type.'/req.csv', 'r');
        if($csv_file) {
            while(($data = fgetcsv($csv_file, 1000, ';')) != FALSE) {
               // TODO: fix Tooltips
               // $result[$data[0]] = array('description' => $data[1], 'example' => $data[2]);
            }
        }
        return json_encode($result);
    }

    public function getResponseTooltips($type) {
        $result = array();
        $csv_file = @fopen('http://innativelife.com/wms/'.$type.'/resp.csv', 'r');
        if($csv_file) {
            while(($data = fgetcsv($csv_file, 1000, ';')) != FALSE) {
                if (isset($data[1]) && isset($data[2])) {
                	// TODO: fix Tooltips
//                    $result[$data[0]] = array('description' => $data[1], 'example' => $data[2]);
                }
            }
        }
        return json_encode($result);
    }

    public static function noSpace($data) {
        return str_replace(
            array("-", " "),
            array('', ''),
            $data);
    }

    protected $createProductAttributes = array(
        'id' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'sellPrice' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'description' =>   array(
            'type'  => 'textarea',
            'style'  => 'width:50em;height:7em;',
            'editable' => true,
            'default'  => '',
        ),
        'storeId' =>   array(
            'type'  => 'hidden',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'sectionId' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'defaultOrientation' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'defaultPerspective' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'sortPriority' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'name' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'merchandiseId' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
    );

    protected $createProductMediaConfigurationAttributes = array(
        'height' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'name' =>   array(
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
        'cpDesignId' =>   array(
            'name'  => 'designId',
            'type'  => 'text',
            'style'  => '',
            'editable' => true,
            'default'  => '',
        ),
    );

    public function getProductCreateAttributes() {
        return $this->createProductAttributes;
    }

    public function getCreateProductMediaConfigurationAttributes() {
        return $this->createProductMediaConfigurationAttributes;
    }

    public function getProductDataFromXml($xml) {
        $result = array('product'=>array(),'mediaConfiguration'=> array());
        $sxml = simplexml_load_string($xml);

        foreach($sxml->attributes() as $key => $val) {
            $result['product'][$key]= $val;
        }
        foreach($sxml->mediaConfiguration[0]->attributes() as $key => $val) {
            $result['mediaConfiguration'][$key]= $val;
        }
        return $result;
    }

    public function getXmlByXmlData($xml_data) {
        $xml = '<?xml version="1.0"?><product><mediaConfiguration/></product>';
        $sxml = simplexml_load_string($xml);

        foreach($xml_data['product'] as $key => $val) {
            $sxml->addAttribute($key, $val);
        }
        foreach($xml_data['product_media'] as $key => $val) {
            $sxml->mediaConfiguration->addAttribute($key, $val);
        }
        return $sxml->asXML();
   }


    public function checkDir($path) {
        if(!is_dir($path)) {
            mkdir($path);
            chmod($path,0777);
        }
    }

    public function downloadImage($image_url) {
        $image_path = Mage::getBaseDir().'/media/cafepress/'.basename($image_url);

        if(!file_exists($image_path)) {
            try {
                copy($image_url, $image_path);
            } catch (Exception $e) {
                Mage::log("Remoute file not exist: {$image_url}", null, 'fbapp.log');
            }
        }
        return $image_path;
    }

    public function checkXmlForCreateProduct($xml) {
        $sxml = simplexml_load_string($xml);
        foreach($sxml as $element) {
            if($element->getName() == 'color') {
                unset($sxml->color);
                break;
            }
            if($element->getName() == 'size') {
                unset($sxml->size);
                break;
            }
        }
        return $sxml->asXML();
    }

    public function getAttributeOptions($attribute_code) {
        $result = array();
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
        foreach($attribute->getSource()->getAllOptions(true, true) as $option) {
            if($option['label']) {
                $result[$option['value']] = $option['label'];
            }
        }
        return $result;
    }

    public function sendCurlPost($url, $params) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($curl, CURLOPT_ENCODING, 1);
        curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt ($curl, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    public function getAchtungState() {
        return  (bool)Mage::getStoreConfig('cafepress_common/achtung/state');
    }

    public function cleanHtml($html) {
      $html = preg_replace('/<script(.*)<\/script>/is', '',$html);
      $html = preg_replace('/<style(.*)<\/style>/is', '',$html);
      $html = preg_replace('/<!--(.*)-->/is', '',$html);
      $html = preg_replace("/\\n\\n/is", "",$html);
      $html = str_replace("'",'',str_replace('"','',$html));
      $html = str_replace("",' ',$html);

      $html = addslashes($html);
      return  $html;
    }

    public function arrayToXml($array, $dom = null) {
      $xml = '';
      if($dom == null) {
          $dom = new DOMDocument('1.0');
          // we want a nice output
          $dom->formatOutput = true;
          $root = $dom->createElement('content');
          $root->setAttribute('xml:id', 'root');
          $root = $dom->appendChild($root);

      }
      foreach($array as $k=>$v) {
        if(is_array($v))  {
          //$xml .= '';
          $dom = $this->arrayToXml($v,$dom);
        } else {
          //$xml .= "<{$k}><!CDATA[{$v}]></{$k}>";
          $root = $dom->getElementById('root'); // root is always same - non ierarhical xml
          $el = $dom->createElement($k);
          $el = $root->appendChild($el);
          $v = rtrim(ltrim($v,'"'),'"');
          $text = $dom->createTextNode($v);
          $text = $el->appendChild($text);
        }
      }
      return $dom;
    }


     public function applyPrecondition($format,$collection,$entityTypeId) {
        if ($format){
                $precondition = $format->getData('precondition');
        }

        if ($precondition && $precondition != '') {
            $conditions = $statuses = explode('+', $precondition);
            foreach ($conditions as $condition) {
                preg_match_all("/(?P<name>[^-]*)-(?P<suf>[^-]*)-(?<values>.*)/i", trim($condition), $matches);
                if (($matches['name'][0] != '') && ($matches['suf'][0] != '')) { //  && ($matches['values'][0] != '')
                    $values = explode(',', $matches['values'][0]);
                    if (count($values)==1) {
                        $values = $matches['values'][0];
                    }

                     $attributeName  = $matches['name'][0];

                      if($attributeName == 'attribute_set_id') {
                        if(!is_array($values)) $values = array($values);
                        foreach($values as $v) {
                            $attributeSetId = Mage::getModel('eav/entity_attribute_set')
                                      ->getCollection()
                                      ->setEntityTypeFilter($entityTypeId)
                                      ->addFieldToFilter('attribute_set_name', $v)
                                      ->getFirstItem()  // TODO: change this if attribute sets will have eq name
                                      ->getId();
                            $vals[] = (int)$attributeSetId;
                                      }
                            if(count($vals)==1) { $valse = (int)$vals[0]; }
                      } else {
                        $vals = $values;
                      }

                //TODO: attribute_set can not contain some of attribute. oggettos and products have differenta attributes

                $collection = $collection->addAttributeToFilter($attributeName, array($matches['suf'][0] => $vals));
                }
            }
        }

     return $collection;
   }
}









