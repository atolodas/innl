<?php
class Shaurmalab_Constructor_Block_Admin extends Mage_Core_Block_Template {

	public function _construct() {
		parent::_construct();
	}

	public function isAdmin() {
		return Mage::helper('constructor')->isAdmin();
	}

	public function getPages() {
		$pages = Mage::getModel('cms/page')->getResourceCollection()
		                                   ->addStoreFilter(Mage::app()->getStore()->getId())
		; // Mage::app()->getWebsite()->getStoreIds()
		$pages->setFirstStoreFlag(true);
		return $pages;
	}

	public function getBlocks() {
		return Mage::getModel('cms/block')->getResourceCollection()
		                                  ->addStoreFilter(Mage::app()->getStore()->getId()); // Mage::app()->getWebsite()->getStoreIds()
	}

	public function getTemplates() { 
			$collection = Mage::getModel('dcontent/templates')->getResourceCollection()
		          ->addFieldToFilter('store_id', array('like'=>'%'.Mage::app()->getStore()->getId().'%')); 
		    return $collection;
	}

	public function getObjects() {
		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
		->addFieldToFilter('store_id', Mage::app()->getWebsite()->getStoreIds())
		->load();
		 $attributeSetsByName = array();
        foreach ($sets as $id => $attributeSet) {
        	$name = $attributeSet->getAttributeSetName();
            $data = array(
            	'name'=>$name,
            	'id' => $attributeSet->getId(),
            	'identifier' => Mage::helper('score')->getSetCode($attributeSet)
            	);
            $object = new Varien_Object();
            $object->setData($data);
            $attributeSetsByName[$id] = $object; 
        }
		return $attributeSetsByName;
	}

	public function getAttributes($objectId) { 
		$groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($objectId)
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

	public function getWidgets() { 
		$allWidgets = array();

		$blocks = $this->getBlocks();
		$blocksSelect = "<div><form id='selectBlock'><select name='block_id' onChange='chooseWidget(this.form.id)'>";
		$blocksSelect.="<option value=''>{$this->__('Choose block')}</option>";
		foreach ($blocks as $block) {
			$blocksSelect.="<option value='{$block->getIdentifier()}'>{$block->getTitle()}</option>";
		}
		$blocksSelect.= "</select></div></form>";

        $allWidgets = //Mage::getModel('widget/widget')->getWidgetsArray();
        	array(
        		array('code'=>"{{block type='cms/block' block_id=''}}",'name'=>$this->__('CMS Static Block'),'description'=>$this->__('Contents of a Static Block').addslashes($blocksSelect)),
        		array('code'=>"{{block type='core/template' template='score/oggetto/likes.phtml' likes_counter='[[likes_counter]]' oid='[[entity_id]]'}}",'name'=>$this->__('Likes widget'),'description'=>$this->__('Likes widget')),
        		array('code'=>"{{block type='core/template' template='score/oggetto/addtowishlist.phtml' oid='[[entity_id]]'}}",'name'=>$this->__('Bookmark widget'),'description'=>$this->__('Bookmark widget')),
        		array('code'=>'','name'=>$this->__(''),'description'=>$this->__('')),
        		array('code'=>'','name'=>$this->__(''),'description'=>$this->__('')),
        		array('code'=>'','name'=>$this->__(''),'description'=>$this->__('')),
        		array('code'=>'','name'=>$this->__(''),'description'=>$this->__('')),
        );

        	//{{block type="core/template" template="score/oggetto/addtowishlist.phtml" oid="[[entity_id]]" }}
//{{block type="core/template" template="score/oggetto/likes.phtml" likes="[[likes]]" likes_counter="[[likes_counter]]" oid="[[entity_id]]"}}

//         oggetto_attribute_edit score/oggetto_edit
// cms_page_link cms/widget_page_link
// cms_static_block cms/widget_block
// score_category_link score/category_widget_link
// catalog_category_link catalog/category_widget_link
// new_oggettos score/oggetto_widget_new
// new_products catalog/product_widget_new
// score_oggetto_link score/oggetto_widget_link
// catalog_product_link catalog/product_widget_link
// newsfeedwidget_list newsfeedwidget/List
// dcontent_oggettos_list dcontent/oggettos
// dcontent_list dcontent/dcontent
// dcontent_categories dcontent/categories 
        return $allWidgets;
	}

	public function getImages() {
		$imgdir = Mage::getBaseDir('media') . DS . 'wysiwyg' . DS . Mage::registry('scode') . DS;
		//Pick your folder
		$allowed_types = array('png', 'jpg', 'jpeg', 'gif');
		//Allowed types of files
		if(!is_dir($imgdir)) { mkdir($imgdir); chmod($imgdir, 0777); }
		$dimg = opendir($imgdir); //Open directory
		$images = array();
		while ($imgfile = readdir($dimg)) {
			if (in_array(strtolower(substr($imgfile, -3)), $allowed_types) OR in_array(strtolower(substr($imgfile, -4)), $allowed_types))
			/*If the file is an image add it to the array*/ {$images[] = $imgfile;}
		}
		return $images;
	}

}