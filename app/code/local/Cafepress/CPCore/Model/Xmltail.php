<?php

define("HIGHLIGHT_NOT",0);
define("HIGHLIGHT_SPAN",100);
define("HIGHLIGHT_BOLD",200);
define("HIGHLIGHT_ASTERISK",300);
define("PLAIN",1);
define("BREAKS",2);
define("PARAGRAPH",4);
define("UL_LIST",8);
define("OL_LIST",16);
define("XML",32);

class Cafepress_CPCore_Model_Xmltail
{
	var $timestamp = 0;
	var $filesize;
	var $filename;
	var $filearray = array();
	var $outputarray = array();
	var $lines;
	var $lastline;
	var $showlines = 50;
	var $regexp = '';
	var $highlight;
	public $regexpr = '';

	function __construct($filename){
		if (file_exists($filename)) {
			$this->filename = $filename;
			$this->updatestats();
		} else
			return null;
	}

	function updatestats(){
		$new_timestamp = filemtime($this->filename);

		// check for change
		if ($new_timestamp > $this->timestamp){
			$this->filesize = filesize($this->filename);
			$this->openFile();
			$this->timestamp = $new_timestamp;
		}
	}

	function output($format = PLAIN){
		if ($format >= HIGHLIGHT_ASTERISK) {
			$format -= HIGHLIGHT_ASTERISK;
		} else if ($format >= HIGHLIGHT_BOLD) {
			$format -= HIGHLIGHT_BOLD;
		} else if ($format >= HIGHLIGHT_SPAN) {
			$format -= HIGHLIGHT_SPAN;
		} else if ($format >= HIGHLIGHT_NOT) {
		}

		$pre_output = "";
		$post_output = "";
		switch ($format) {
			case UL_LIST:
				$pre_output = "<ul>";
				$post_output = "</ul>";
				break;
			case OL_LIST:
				$pre_output = "";
				$post_output = "";
				break;
			case XML:
				$pre_output = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<list>";
				$post_output = "</list>";
				break;

			default:
				break;
		}
		$output = $pre_output;
        $i = 0;
        while(isset($this->outputarray[$i])) {
            $output .= $this->outputarray[$i];
            $i++;
        }
		$output .= $post_output;

        return Mage::helper('cpcore')->formatXml($output, '<br/>');
	}

	function setGrepRegExpr($regexp){
		$this->regexpr = '~('.$regexp.')~';
		$this->doGrep();
	}

	function setGrep($searchword){
		$this->regexpr = "~.*(".$searchword.").*~i";
		$this->highlight = $searchword;
		$this->doGrep();
	}

	function setGrepi($string){
		$this->regexpr = "~.*(".$string.").*~i";
		$this->highlight = $string;
		$this->doGrep();
	}

	function openFile(){
		$this->filearray = $this->outputarray = file($this->filename);
		$this->lines = count($this->filearray);
	}

	function doGrep(){
		if (strlen($this->regexpr) > 3)
			$this->outputarray = preg_grep($this->regexpr,$this->filearray);
	}

	function setNumberOfLines($lines){
		$this->showlines = $lines;
	}
}
