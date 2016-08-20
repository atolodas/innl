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

class Cafepress_CPCore_Model_Tail
{

    var $timestamp = 0;
    var $filesize;
    var $filename;
    var $filearray = array();
    var $outputarray = array();
    var $lines;
    var $lastline;
    var $showlines = 10;
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
        $highlight = "\1";
        if ($format >= HIGHLIGHT_ASTERISK) {
            $highlight = "*".$this->highlight."*";
            $format -= HIGHLIGHT_ASTERISK;
        } else if ($format >= HIGHLIGHT_BOLD) {
            $highlight = "<b>".$this->highlight."</b>";
            $format -= HIGHLIGHT_BOLD;
        } else if ($format >= HIGHLIGHT_SPAN) {
            $highlight = '<span class="highlight">'.$this->highlight.'</span>';
            $format -= HIGHLIGHT_SPAN;
        } else if ($format >= HIGHLIGHT_NOT) {
            $highlight = $this->highlight;
        }

        $pre_output = "";
        $post_output = "";
        $pre_line = "";
        $post_line = "\n";
        switch ($format) {
            case BREAKS:
                $pre_line = "";
                $post_line = "<br/>";
                break;
            case PARAGRAPH:
                $pre_line = "<p>";
                $post_line = "</p>";
                break;
            case UL_LIST:
                $pre_output = "<ul>";
                $post_output = "</ul>";
                $pre_line = "<li>";
                $post_line = "</li>";
                break;
            case OL_LIST:
                $pre_output = "";//"<ol>";
                $post_output = "";
                $pre_line = "<li>";
                $post_line = "</li>";
                break;
            case XML:
                $pre_output = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n<list>";
                $post_output = "</list>";
                $pre_line = "<line>";
                $post_line = "</line>\n";
                break;

            default:
                break;
        }

        $output = $pre_output;
        if($this->lines < $this->showlines) $i = 0;
        else $i = $this->lines - $this->showlines;
        while($i < $this->lines){
            if (isset($this->outputarray[$i])) {
                $output .= $pre_line;
                $output .= ($this->regexpr == '')?$this->outputarray[$i]:str_replace($this->highlight, $highlight, $this->outputarray[$i]);
                $output .= $post_line;
            }
            $i++;
        }
        $output .= $post_output;
        return $output;
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
