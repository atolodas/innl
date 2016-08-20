<?php

class Oggetto_Cacherecords_Block_Adminhtml_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$file = $row->getData('md5key');
        $entity = Mage::getModel('cacherecords/cacherecords')->load($row->getdata('cacherecords_id'));
//    	if(is_file(Mage::getBaseDir().'/var/lightspeed/'.$file)) {
//           // $entity->setData('file_exist','Yes')->save();
//            'Yes';
//
//        } else {
//          //  $entity->setData('file_exist','No')->save();
//            return 'No';
//        }
        return $entity->getFileExist();
    }
}

?>