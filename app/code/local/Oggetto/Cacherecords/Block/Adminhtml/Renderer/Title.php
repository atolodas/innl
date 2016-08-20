<?php

class Oggetto_Cacherecords_Block_Adminhtml_Renderer_Title extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $title = "Can't get title";
    	if(is_file(Mage::getBaseDir().'/var/lightspeed/'.$row->getData('md5key'))) {
            $file = file_get_contents(Mage::getBaseDir().'/var/lightspeed/'.$row->getData('md5key'));

            if(preg_match("/<title>(.+)<\/title>/i",$file,$m)) {
             
                $title = $m[1];
            }
        }
        $entity = Mage::getModel('cacherecords/cacherecords')->load($row->getdata('cacherecords_id'));
        $entity->setData('title',$title)->save();
        return $title;
    }
}

?>