<?php 

function getFavicon($url) { 


$doc = new DOMDocument();
$doc->strictErrorChecking = FALSE;
$doc->loadHTML(file_get_contents($url));
$xml = simplexml_import_dom($doc);
$arr = $xml->xpath('//link[@rel="shortcut]');
echo $arr[0]['href'];

}
