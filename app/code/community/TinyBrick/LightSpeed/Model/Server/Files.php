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

class TinyBrick_LightSpeed_Model_Server_Files
{
	protected $_server;
	
	public function getServer()
	{
		if (!isset($this->_server)){
			//verify that the file system exists and that we have rights to it.	
			$folder = Mage::getConfig()->getNode('lightspeed/cache/path');
			if (!isset($folder) || $folder == "")
				$folder = "var/lightspeed";
			rtrim($folder, "/");
		//	if (!is_dir($folder)){
		//		mkdir($folder, 0777);
		//	}
			$this->_server = $folder . "/";
		}
		return $this->_server;
	}
	
	public function save($key, $data, $expires=0, $tags=array())
	{
            if($tags == null) {
                $tags = array();
            }
              $server = $this->getServer();
		if(substr_count($key, 'sendfriend')==0 && substr_count($key, '/checkout') == 0 && substr_count($key, '/customer') == 0
            && substr_count($key, '/product_compare') == 0) {
		//open file for overwrite
                 $md = md5($key); 
		$filename =   $md[0].'/'.$md[1].'/'.$md; //MD5($key);
		if(!is_dir($this->getServer() .$md[0])) mkdir($this->getServer() .$md[0]);	
		if(!is_dir($this->getServer() .$md[0].'/'.$md[1])) mkdir($this->getServer() .$md[0].'/'.$md[1]);
		$server = str_replace('lightspeed','light',$server);
		$file = fopen($server . $md, 'w');
                fwrite($file, serialize($data));
		fclose($file);
		rename($server . $md,$this->getServer().$filename);
		//fill in tag files
		foreach($tags as $tag){
		$md =  MD5($tag);
		$filename =   $md[0].'/'.$md[1].'/'.$md; 	
 		if(!is_dir($this->getServer() .$md[0])) mkdir($this->getServer() .$md[0]);
                if(!is_dir($this->getServer() .$md[0].'/'.$md[1])) mkdir($this->getServer() .$md[0].'/'.$md[1]);
               
		$tagfile = $server.$md;
		$file = fopen($tagfile, 'a');
		fwrite($file, $tagfile . "\r\n");
		fclose($file);
 		rename($server . $md,$this->getServer().$filename);
		}
              }
	}
	
	public function cleanByTag($tag)
	{
		return $this->clean(array($tag));
	}
	
	public function clean($tags=array())
	{
		try{
			if(count($tags) && !in_array('LIGHTSPEED', $tags)){
				foreach($tags as $tag){
					$tagfile = $this->getServer() . MD5($tag);
					if (is_file($tagfile)){
						$file = fopen($tagfile, 'r');
						while($line = fgets($file)){
							//delete file with name of $line :)
							$filename = preg_replace("/\r|\n/", "", $this->getServer() . $line);
							if (is_file($filename)){
								unlink($filename);
							}
						}
						//delete the tag file
						unlink($tagfile);
					}
				}
			}else{
				//delete the tags folder
				$files = glob($this->getServer() . "*");
				foreach($files as $file) unlink($file);
			}
		}catch(Exception $e){
			return false;
		}
	}
}
