<?php
/**
 * Create oggetto block
 * * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Create extends Mage_Core_Block_Template
{
	public $set;

    /**
     * Prepare collection with new oggettos
     *
     * @return Mage_Core_Block_Abstract
     */
    public function _toHtml()
    {

         if($this->getOnlyRegistered() && !Mage::getSingleton('customer/session')->isLoggedIn()) {
          Mage::getSingleton('customer/session')->setBeforeAuthUrl('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
          $url = Mage::helper('adminhtml')->getUrl('customer/account/login', array(''));
          Mage::app()->getFrontController()->getResponse()
                ->setRedirect($url)
                ->sendResponse();
        } else {
         return parent::_toHtml();
        }
    }

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

	public function getAttributes() {

		$setId = $this->getSetId();
		/* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
		$attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
                $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->addStoreLabel(Mage::app()->getStore()->getId())
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()

                               // ->checkConfigurableOggettos()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                    if($child->getIsVisibleOnFront() || $child->getIsPublic()) {
                      $attributes[] = $child;
                    }
                }
            }
        }
		return $attributes;


	}

    public function getAttributesPerGroup() {

        $setId = $this->getSetId();
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        $attributes = array();


        $groupArray = array();
        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                // ->checkConfigurableOggettos()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                    if($child->getIsVisibleOnFront()) {
                        $groupArray[$node->getAttributeGroupName()][] = $child;
                    }
                }
            }
        }
        return $groupArray;


    }


}
