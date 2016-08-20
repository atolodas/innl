<?php

class Oggetto_Cacherecords_Block_Adminhtml_Renderer_Time extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	 $file = $row->getData('md5key');
    	
    	  $entity = Mage::getModel('cacherecords/cacherecords')->load($row->getdata('cacherecords_id'));
         if(is_file(Mage::getBaseDir().'/var/lightspeed/'.$file)) {
            $entity->setData('created_time', date('Y-m-d h:i:s',filemtime(Mage::getBaseDir().'/var/lightspeed/'.$file)))->save();
            return date('Y-m-d h:i:s',filemtime(Mage::getBaseDir().'/var/lightspeed/'.$file));

        } else {
            $entity->setData('created_time', filemtime(Mage::getBaseDir().'/var/lightspeed/'.$file))->save();
            return '';
        }
        
    }

  
}

?>