<?php
/**
 * Create oggetto block
 * * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Edit extends Shaurmalab_Score_Block_Oggetto_Abstract  implements Mage_Widget_Block_Interface {

  public $set;
  public $attribute_code;
  public $fieldtype;
  public $image;
  public $alt;

  public function getSetId()
	{

    if(!$this->getSet() && Mage::registry('current_oggetto')) {
      return Mage::registry('current_oggetto')->getAttributeSetId();
    } else {
      $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
              ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
        ->addFieldToFilter('attribute_set_name',$this->getSet())
        ->getFirstItem(); // TODO: add filter by owner when needed
      return $set->getId();
    }
	}


  public function getAttributeByCode() {

    $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->addFieldToFilter('attribute_code',$this -> getData('attribute_code'))
               ->addVisibleFilter()
                ->load();
            $attribute = null;
            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                      $attribute = $child;
                    break;
                }
            }
		return $attribute;


	}

    public function getFieldType() {
      // TODO: do not check fieldtype attribute for default attribute edit template
      if($this->getData('fieldtype')) { return $this->getData('fieldtype'); }
      return $this->getAttributeByCode()->getFrontendInput();
    }




}
