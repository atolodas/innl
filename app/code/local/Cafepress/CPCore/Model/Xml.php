<?php

class Cafepress_CPCore_Model_Xml
{
    public function fetchFiles()
    {
        $inboundFiles = array();
        $outboundFiles = array();

        $inboundPath = $this->_getXmlDir().'inbound'.DS;
        $outboundPath = $this->_getXmlDir().'outbound'.DS;

        if(file_exists($inboundPath) && is_dir($inboundPath)) {
            foreach (new DirectoryIterator($inboundPath) as $fileInfo) {
                if($fileInfo->isDot()){
                    continue;
                }

                if(preg_match('/[(.log)(.logs)]$/', $fileInfo->getFilename())){
                    $inboundFiles [] = array('file' => $fileInfo->getPathname(), 'filename'=>$fileInfo->getFilename());
                }
            }
        }

        if (file_exists($outboundPath) && is_dir($outboundPath)) {
            foreach (new DirectoryIterator($outboundPath) as $fileInfo) {
                if($fileInfo->isDot()){
                    continue;
                }

                if(preg_match('/[(.log)(.logs)]$/', $fileInfo->getFilename())){
                    $outboundFiles [] = array('file' => $fileInfo->getPathname(), 'filename'=>$fileInfo->getFilename());
                }
            }
        }

        $xmlFiles = array('inbound' => $inboundFiles, 'outbound' => $outboundFiles);

        return $xmlFiles;
    }

    protected function _getXmlDir()
    {
        return Mage::getBaseDir('media').DS.'xmls'.DS;
    }
}
