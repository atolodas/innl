<?php

class Cafepress_CPCore_Helper_Xml extends Mage_Core_Helper_Abstract {

    /**
     * Formatting xml-string to dom
     * Example: 
     * "<root><foo><bar>baz</bar></foo></root>" to: 
     * <?xml version="1.0"?>
     *   <root>
     *     <foo>
     *       <bar>baz</bar>
     *     </foo>
     *   </root>
     * @param type $string
     * @return type 
     */
    public function xmlstringToXmldom($string) {
        $result = $string;
        try {
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = FALSE;
            $dom->loadXML($string);
            if ($dom instanceof DOMDocument) {
                $dom->formatOutput = TRUE;
                $result = $dom->saveXml();
            } else {
                $result = $xml;
            }
        } catch (Exception $exc) {
            return $string;
        }
        return $result;
    }

    public function removeSpaceFromXML($xml) {
        $xml = trim(
                preg_replace(
                        array("/>[\s]+</", "/>[\s]+{{/", "/}}[\s]+</"), 
                        array("><", ">{{", "}}<"), 
                        $xml
                        )
                );
        return $xml;
    }

    public function removeCommentariesFromXML($xml) {
        $xml = preg_replace(
                array('/<!--(.*)-->/Uis'), 
                array(""), 
                $xml
            );
        return $xml;
    }

}
