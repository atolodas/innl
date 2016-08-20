<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Downfileparsresp extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    
    protected $_filesForDelete = array();
    protected $_files = array();

    public function downloadRequestFiles()
    {
        $filesArray = array();
        $filesName = $this->getFiles($this->getRequest());
        
        if ($filesName && (count($filesName)>0)){
            foreach ($filesName as $fileName){
                $fileContent = implode("",  file($fileName)) or $this->fileNotOpen($fileName);
               $filesArray[]  = array(
                    'content' => $fileContent,
                    'filename' => $fileName,
                );
            }
print_r($filesArray);            
            $this->_files = $filesArray;
//            Mage::log('Files:'.implode(', ',$filesName).' was download and saved.',null,'downloadfileparsrespons.log');
            return true;
        } else {
            echo 'File were not saved.';
            Mage::log('Files were not saved.',null,'downloadfileparsrespons.log');
            return false;
        }
        
    } 
    
    private function fileNotOpen($filename)
    {
	die('Can not open '.$filename);
        return Mage::getModel('cpcore/xmlformat_outbound')->fileNotOpen($filename,'downloadfileparsrespons.log');
    }
    
    public function processResponse($cap=false)
    {
        $fileFromDelet = array();
        foreach($this->_files as $key => $file){
            if (Mage::registry('cp_response_filename')){
                Mage::unregister('cp_response_filename');
            }
            Mage::register('cp_response_filename', $file['filename']);
            if($file['content']){
                $result = parent::processResponse($file['content']);
            } else{
                $result = false;
            }
            
            $this->_files[$key]['result_response'] = $result;
            if($result){
                $fileFromDelet[] = $file['filename'];
            }
            Mage::unregister('cp_response_filename');
        }
        $this->_filesForDelete = $fileFromDelet;
    }
       
    public function getFiles($pattern, $type = 'shipment') {
         $result = Mage::getModel('cpcore/xmlformat_outbound')->getFiles($pattern, $type);
         if (!is_array($result)){
             return array();
         }
         return $result;
    }
    
    public function deleteFileAfterSuccess()
    {
        return Mage::getModel('cpcore/xmlformat_outbound')->deleteFilesFromFtp($this->_filesForDelete);
    }
    
    public function getFilesContent()
    {
        return $this->_files;
    }
    
    public function getMethodOfRequest()
    {
        return array('loadFileByFtp');
    }

}


