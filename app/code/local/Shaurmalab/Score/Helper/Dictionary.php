<?php
class Shaurmalab_Score_Helper_Dictionary extends Mage_Core_Helper_Abstract
{

        public function getTextValue($dictionary, $id) { 

                if(Mage::getSingleton('customer/session')->getData($dictionary.'_dict') && count(explode(',', $id)) > 1) $id = Mage::getSingleton('customer/session')->getData($dictionary.'_dict');
                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $binds = array(
                        'store_id' => Mage::app()->getStore()->getId(),
                        'id'            => $id
                );
                $elements = $readConnection->query("select * from {$dictionary} where store_id = :store_id AND id = :id",$binds)->fetchAll();
                return isset($elements[0])?$elements[0]['title']:'';
        }

}