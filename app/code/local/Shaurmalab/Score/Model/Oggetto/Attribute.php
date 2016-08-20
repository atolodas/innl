<?php
class Shaurmalab_Score_Model_Oggetto_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
    }



    public function toOptionArray(){
        $attributes = $this->getAttributes();
        foreach($attributes as $attr) {
            $option_array[]= array('value'=>$attr, 'label' => $attr);
        }
        return $option_array;
    }

    public function getAttributes() {

    /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
    // TODO: filter by oggetto type;
    $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setSortOrder()
            ->load();
		$attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
                $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                  // TODO: add filter by attribute visibility
                  $attributes[] = $child->getAttributeCode();
					      }
            }
        }
		return $attributes;


	}

    public function loadAttribute($code) {

        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        // TODO: filter by oggetto type;
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setSortOrder()
            ->load();
        $attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    // TODO: add filter by attribute visibility
                    if( $child->getAttributeCode() == $code) return $child;
                }
            }
        }
        return false;


    }

}
