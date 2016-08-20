<?php
define('POSTURL', 'https://qpush.me/pusher/push_site/');
define('CRON_ROOT', '/var/www/html');
require_once (CRON_ROOT.'/app/Mage.php');
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);
ini_set('memory_limit','2048M');
Mage::app('admin')->setUseSessionInUrl(false);


$customers = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
foreach ($customers as $customer) {
    $id = $customer->getId();

        if($customer->getQpushName() && $customer->getQpushCode()) {

$data = json_decode(file_get_contents('http://innl.co/index.php/api/get/random/t/Quote/o/'.$id.'/l/1/f/json'));
$text = strip_tags($data->text);
if(isset($data->quote_author) && $data->quote_author) { 
    $text .= "\n" . $data->quote_author;
}

define('POSTVARS', 'name='.$customer->getQpushName().'&code='.$customer->getQpushCode().'&msg[text]='.$text);



$ch = curl_init(POSTURL);
 curl_setopt($ch, CURLOPT_POST      ,1);
 curl_setopt($ch, CURLOPT_POSTFIELDS    ,POSTVARS);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
 curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
 curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
 $Rec_Data = curl_exec($ch);
  curl_close($ch);
        }

if($customer->getBoxcarToken()) {
$data = json_decode(file_get_contents('http://innl.co/index.php/api/get/random/t/Quote/o/'.$id.'/l/1/f/json'));
$text = strip_tags($data->text);
if(isset($data->quote_author) && $data->quote_author) { 
    $text .= "\n" . $data->quote_author;
}

	curl_setopt_array(
    $curl = curl_init(),
    array(
        CURLOPT_URL => "https://new.boxcar.io/api/notifications",
        CURLOPT_POSTFIELDS => array(
            "user_credentials" => $customer->getBoxcarToken(),
            "notification[title]" => '',
            "notification[long_message]" => $text,
            "notification[sound]" => "bird-1",
            "notification[source_name]" => "Q.INL"
        )));
 
    $ret = curl_exec($curl);
    curl_close($curl);
}


if($customer->getPushalotToken()) {
$data = json_decode(file_get_contents('http://innl.co/index.php/api/get/random/t/Quote/o/'.$id.'/l/1/f/json'));
$text = strip_tags($data->text);
if(isset($data->quote_author) && $data->quote_author) { 
    $text .= "\n" . $data->quote_author;
}

curl_setopt_array($ch = curl_init(), array(
	CURLOPT_URL => "https://pushalot.com/api/sendmessage",
	CURLOPT_POSTFIELDS => array(
		"AuthorizationToken" => $customer->getPushalotToken(),
		"Body" => $text,
	)));
 curl_exec($ch);
curl_close($ch);
}

}
exit;
