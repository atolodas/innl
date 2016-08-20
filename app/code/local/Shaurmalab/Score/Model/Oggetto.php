<?php
class Shaurmalab_Score_Model_Oggetto extends Shaurmalab_Score_Model_Abstract {
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY = 'score_oggetto';

	const CACHE_TAG = 'score_oggetto';
	protected $_cacheTag = 'score_oggetto';
	protected $_eventPrefix = 'score_oggetto';
	protected $_eventObject = 'oggetto';
	protected $_canAffectOptions = false;

	/**
	 * Oggetto type instance
	 *
	 * @var Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	protected $_typeInstance = null;

	/**
	 * Oggetto type instance as singleton
	 */
	protected $_typeInstanceSingleton = null;

	/**
	 * Oggetto link instance
	 *
	 * @var Shaurmalab_Score_Model_Oggetto_Link
	 */
	protected $_linkInstance;

	/**
	 * Oggetto object customization (not stored in DB)
	 *
	 * @var array
	 */
	protected $_customOptions = array();

	/**
	 * Oggetto Url Instance
	 *
	 * @var Shaurmalab_Score_Model_Oggetto_Url
	 */
	protected $_urlModel = null;

	protected static $_url;
	protected static $_urlRewrite;

	protected $_errors = array();

	protected $_optionInstance;

	protected $_options = array();

	/**
	 * Oggetto reserved attribute codes
	 */
	protected $_reservedAttributes;

	/**
	 * Flag for available duplicate function
	 *
	 * @var boolean
	 */
	protected $_isDuplicable = true;

	/**
	 * Flag for get Price function
	 *
	 * @var boolean
	 */
	protected $_calculatePrice = true;

	/**
	 * Initialize resources
	 */
	protected function _construct() {
		$this->_init('score/oggetto');
	}

	/**
	 * Init mapping array of short fields to
	 * its full names
	 *
	 * @return Varien_Object
	 */
	protected function _initOldFieldsMap() {
		$this->_oldFieldsMap = Mage::helper('score')->getOldFieldMap();
		return $this;
	}

	/**
	 * Retrieve Store Id
	 *
	 * @return int
	 */
	public function getStoreId() {
//        if ($this->hasData('store_id')) {
		//            return $this->getData('store_id');
		//        }
		return Mage::app()->getStore()->getId();
	}

	/**
	 * Get collection instance
	 *
	 * @return object
	 */
	public function getResourceCollection() {
		if (empty($this->_resourceCollectionName)) {
			Mage::throwException(Mage::helper('score')->__('The model collection resource name is not defined.'));
		}
		$collection = Mage::getResourceModel($this->_resourceCollectionName);
		$collection->setStoreId($this->getStoreId());
		return $collection;
	}
	public function getCollection() {
		return $this->getResourceCollection();
	}

	/**
	 * Get oggetto url model
	 *
	 * @return Shaurmalab_Score_Model_Oggetto_Url
	 */
	public function getUrlModel() {
		if ($this->_urlModel === null) {
			$this->_urlModel = Mage::getSingleton('score/factory')->getOggettoUrlInstance();
		}
		return $this->_urlModel;
	}

	/**
	 * Validate Oggetto Data
	 *
	 * @todo implement full validation process with errors returning which are ignoring now
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function validate() {
		Mage::dispatchEvent($this->_eventPrefix . '_validate_before', array($this->_eventObject => $this));
		$this->_getResource()->validate($this);
		Mage::dispatchEvent($this->_eventPrefix . '_validate_after', array($this->_eventObject => $this));
		return $this;
	}

	/**
	 * Get oggetto name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_getData('name');
	}

	/**
	 * Get oggetto price throught type instance
	 *
	 * @return unknown
	 */
	public function getPrice() {
		if ($this->_calculatePrice || !$this->getData('price')) {
			return $this->getPriceModel()->getPrice($this);
		} else {
			return $this->getData('price');
		}
	}

	/**
	 * Set Price calculation flag
	 *
	 * @param bool $calculate
	 * @return void
	 */
	public function setPriceCalculation($calculate = true) {
		$this->_calculatePrice = $calculate;
	}

	/**
	 * Get oggetto type identifier
	 *
	 * @return string
	 */
	public function getTypeId() {
		return $this->_getData('type_id');
	}

	/**
	 * Get oggetto status
	 *
	 * @return int
	 */
	public function getStatus() {
		if (is_null($this->_getData('status'))) {
			$this->setData('status', Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);
		}
		return $this->_getData('status');
	}

	/**
	 * Retrieve type instance
	 *
	 * Type instance implement type depended logic
	 *
	 * @param  bool $singleton
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function getTypeInstance($singleton = false) {
		if ($singleton === true) {
			if (is_null($this->_typeInstanceSingleton)) {
				$this->_typeInstanceSingleton = Mage::getSingleton('score/oggetto_type')
				     ->factory($this, true);
			}
			return $this->_typeInstanceSingleton;
		}

		if ($this->_typeInstance === null) {
			$this->_typeInstance = Mage::getSingleton('score/oggetto_type')
			     ->factory($this);
		}
		return $this->_typeInstance;
	}

	/**
	 * Set type instance for external
	 *
	 * @param Shaurmalab_Score_Model_Oggetto_Type_Abstract $instance  Oggetto type instance
	 * @param bool                                     $singleton Whether instance is singleton
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function setTypeInstance($instance, $singleton = false) {
		if ($singleton === true) {
			$this->_typeInstanceSingleton = $instance;
		} else {
			$this->_typeInstance = $instance;
		}
		return $this;
	}

	/**
	 * Retrieve link instance
	 *
	 * @return  Shaurmalab_Score_Model_Oggetto_Link
	 */
	public function getLinkInstance() {
		if (!$this->_linkInstance) {
			$this->_linkInstance = Mage::getSingleton('score/oggetto_link');
		}
		return $this->_linkInstance;
	}

	/**
	 * Retrive oggetto id by sku
	 *
	 * @param   string $sku
	 * @return  integer
	 */
	public function getIdBySku($sku) {
		return $this->_getResource()->getIdBySku($sku);
	}

	/**
	 * Retrieve oggetto category id
	 *
	 * @return int
	 */
	public function getCategoryId() {
		if ($category = Mage::registry('current_category')) {
			return $category->getId();
		}
		return false;
	}

	/**
	 * Retrieve oggetto category
	 *
	 * @return Shaurmalab_Score_Model_Category
	 */
	public function getCategory() {
		$category = $this->getData('category');
		if (is_null($category) && $this->getCategoryId()) {
			$category = Mage::getModel('score/category')->load($this->getCategoryId());
			$this->setCategory($category);
		}
		return $category;
	}

	/**
	 * Set assigned category IDs array to oggetto
	 *
	 * @param array|string $ids
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function setCategoryIds($ids) {
		if (is_string($ids)) {
			$ids = explode(',', $ids);
		} elseif (!is_array($ids)) {
			Mage::throwException(Mage::helper('score')->__('Invalid category IDs.'));
		}
		foreach ($ids as $i => $v) {
			if (empty($v)) {
				unset($ids[$i]);
			}
		}

		$this->setData('category_ids', $ids);
		return $this;
	}

	/**
	 * Retrieve assigned category Ids
	 *
	 * @return array
	 */
	public function getCategoryIds() {
		if (!$this->hasData('category_ids')) {
			$wasLocked = false;
			if ($this->isLockedAttribute('category_ids')) {
				$wasLocked = true;
				$this->unlockAttribute('category_ids');
			}
			$ids = $this->_getResource()->getCategoryIds($this);
			$this->setData('category_ids', $ids);
			if ($wasLocked) {
				$this->lockAttribute('category_ids');
			}
		}

		return (array) $this->_getData('category_ids');
	}

	/**
	 * Retrieve oggetto categories
	 *
	 * @return Varien_Data_Collection
	 */
	public function getCategoryCollection() {
		return $this->_getResource()->getCategoryCollection($this);
	}

	/**
	 * Retrieve oggetto websites identifiers
	 *
	 * @return array
	 */
	public function getWebsiteIds() {
		if (!$this->hasWebsiteIds()) {
			$ids = $this->_getResource()->getWebsiteIds($this);
			$this->setWebsiteIds($ids);
		}
		return $this->getData('website_ids');
	}

	/**
	 * Get all sore ids where oggetto is presented
	 *
	 * @return array
	 */
	public function getStoreIds() {
		if (!$this->hasStoreIds()) {
			$storeIds = array();
			if ($websiteIds = $this->getWebsiteIds()) {
				foreach ($websiteIds as $websiteId) {
					$websiteStores = Mage::app()->getWebsite($websiteId)->getStoreIds();
					$storeIds = array_merge($storeIds, $websiteStores);
				}
			}
			$this->setStoreIds($storeIds);
		}
		return $this->getData('store_ids');
	}

	/**
	 * Retrieve oggetto attributes
	 * if $groupId is null - retrieve all oggetto attributes
	 *
	 * @param int  $groupId   Retrieve attributes of the specified group
	 * @param bool $skipSuper Not used
	 * @return array
	 */
	public function getAttributes($groupId = null, $skipSuper = false) {
		$oggettoAttributes = $this->getTypeInstance(true)->getEditableAttributes($this);
		if ($groupId) {
			$attributes = array();
			foreach ($oggettoAttributes as $attribute) {
				if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
					$attributes[] = $attribute;
				}
			}
		} else {
			$attributes = $oggettoAttributes;
		}

		return $attributes;
	}

	/**
	 * Check oggetto options and type options and save them, too
	 */
	protected function _beforeSave() {
		$this->cleanCache();
		$this->setTypeHasOptions(false);
		$this->setTypeHasRequiredOptions(false);

		$this->getTypeInstance(true)->beforeSave($this);

		$hasOptions = false;
		$hasRequiredOptions = false;

		$oggettoAttributes = $this->getTypeInstance(true)->getEditableAttributes($this);
		$attributes = array();
		foreach ($oggettoAttributes as $attribute) {
			if ($attribute->getIsUnique()) {
				if (!$attribute->getEntity()->checkAttributeUniqueValue($attribute, $this)) {
					$label = $attribute->getFrontend()->getLabel();
					Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
				}
			}
		}

		/**
		 * $this->_canAffectOptions - set by type instance only
		 * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
		 * or in type instance as well
		 */
		$this->canAffectOptions($this->_canAffectOptions && $this->getCanSaveCustomOptions());
		if ($this->getCanSaveCustomOptions()) {
			$options = $this->getOggettoOptions();
			if (is_array($options)) {
				$this->setIsCustomOptionChanged(true);
				foreach ($this->getOggettoOptions() as $option) {
					$this->getOptionInstance()->addOption($option);
					if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
						$hasOptions = true;
					}
				}
				foreach ($this->getOptionInstance()->getOptions() as $option) {
					if ($option['is_require'] == '1') {
						$hasRequiredOptions = true;
						break;
					}
				}
			}
		}

		/**
		 * Set true, if any
		 * Set false, ONLY if options have been affected by Options tab and Type instance tab
		 */
		if ($hasOptions || (bool) $this->getTypeHasOptions()) {
			$this->setHasOptions(true);
			if ($hasRequiredOptions || (bool) $this->getTypeHasRequiredOptions()) {
				$this->setRequiredOptions(true);
			} elseif ($this->canAffectOptions()) {
				$this->setRequiredOptions(false);
			}
		} elseif ($this->canAffectOptions()) {
			$this->setHasOptions(false);
			$this->setRequiredOptions(false);
		}
		parent::_beforeSave();
	}

	/**
	 * Check/set if options can be affected when saving oggetto
	 * If value specified, it will be set.
	 *
	 * @param   bool $value
	 * @return  bool
	 */
	public function canAffectOptions($value = null) {
		if (null !== $value) {
			$this->_canAffectOptions = (bool) $value;
		}
		return $this->_canAffectOptions;
	}

	/**
	 * Saving oggetto type related data and init index
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _afterSave() {
		$this->getLinkInstance()->saveOggettoRelations($this);
		$this->getTypeInstance(true)->save($this);

		/**
		 * Oggetto Options
		 */
		$this->getOptionInstance()->setOggetto($this)
		     ->saveOptions();

		$result = parent::_afterSave();

		Mage::getSingleton('index/indexer')->processEntityAction(
			$this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
		);
		return $result;
	}

	/**
	 * Clear chache related with oggetto and protect delete from not admin
	 * Register indexing event before delete oggetto
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _beforeDelete() {
		//$this->_protectFromNonAdmin();
		$this->cleanCache();
		Mage::getSingleton('index/indexer')->logEvent(
			$this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
		);
		return parent::_beforeDelete();
	}

	/**
	 * Init indexing process after oggetto delete commit
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _afterDeleteCommit() {
		parent::_afterDeleteCommit();
		Mage::getSingleton('index/indexer')->indexEvents(
			self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
		);
	}

	/**
	 * Load oggetto options if they exists
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _afterLoad() {
		parent::_afterLoad();
		/**
		 * Load oggetto options
		 */
		if ($this->getHasOptions()) {
			foreach ($this->getOggettoOptionsCollection() as $option) {
				$option->setOggetto($this);
				$this->addOption($option);
			}
		}
		return $this;
	}

	/**
	 * Retrieve resource instance wrapper
	 *
	 * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto
	 */
	protected function _getResource() {
		return parent::_getResource();
	}

	/**
	 * Clear cache related with oggetto id
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function cleanCache() {
		Mage::app()->cleanCache('score_oggetto_' . $this->getId());
		return $this;
	}

	/**
	 * Get oggetto price model
	 *
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Price
	 */
	public function getPriceModel() {
		return Mage::getSingleton('score/oggetto_type')->priceFactory($this->getTypeId());
	}

	/**
	 * Get oggetto group price
	 *
	 * @return float
	 */
	public function getGroupPrice() {
		return $this->getPriceModel()->getGroupPrice($this);
	}

	/**
	 * Get oggetto tier price by qty
	 *
	 * @param   double $qty
	 * @return  double
	 */
	public function getTierPrice($qty = null) {
		return $this->getPriceModel()->getTierPrice($qty, $this);
	}

	/**
	 * Count how many tier prices we have for the oggetto
	 *
	 * @return  int
	 */
	public function getTierPriceCount() {
		return $this->getPriceModel()->getTierPriceCount($this);
	}

	/**
	 * Get formated by currency tier price
	 *
	 * @param   double $qty
	 * @return  array || double
	 */
	public function getFormatedTierPrice($qty = null) {
		return $this->getPriceModel()->getFormatedTierPrice($qty, $this);
	}

	/**
	 * Get formated by currency oggetto price
	 *
	 * @return  array || double
	 */
	public function getFormatedPrice() {
		return $this->getPriceModel()->getFormatedPrice($this);
	}

	/**
	 * Sets final price of oggetto
	 *
	 * This func is equal to magic 'setFinalPrice()', but added as a separate func, because in cart with bundle
	 * oggettos it's called very often in Item->getOggetto(). So removing chain of magic with more cpu consuming
	 * algorithms gives nice optimization boost.
	 *
	 * @param float $price Price amount
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function setFinalPrice($price) {
		$this->_data['final_price'] = $price;
		return $this;
	}

	/**
	 * Get oggetto final price
	 *
	 * @param double $qty
	 * @return double
	 */
	public function getFinalPrice($qty = null) {
		$price = $this->_getData('final_price');
		if ($price !== null) {
			return $price;
		}
		return $this->getPriceModel()->getFinalPrice($qty, $this);
	}

	/**
	 * Returns calculated final price
	 *
	 * @return float
	 */
	public function getCalculatedFinalPrice() {
		return $this->_getData('calculated_final_price');
	}

	/**
	 * Returns minimal price
	 *
	 * @return float
	 */
	public function getMinimalPrice() {
		return max($this->_getData('minimal_price'), 0);
	}

	/**
	 * Returns special price
	 *
	 * @return float
	 */
	public function getSpecialPrice() {
		return $this->_getData('special_price');
	}

	/**
	 * Returns starting date of the special price
	 *
	 * @return mixed
	 */
	public function getSpecialFromDate() {
		return $this->_getData('special_from_date');
	}

	/**
	 * Returns end date of the special price
	 *
	 * @return mixed
	 */
	public function getSpecialToDate() {
		return $this->_getData('special_to_date');
	}

/*******************************************************************************
 ** Linked oggettos API
 */
	/**
	 * Retrieve array of related roducts
	 *
	 * @return array
	 */
	public function getRelatedOggettos() {
		if (!$this->hasRelatedOggettos()) {
			$oggettos = array();
			$collection = $this->getRelatedOggettoCollection();
			foreach ($collection as $oggetto) {
				$oggettos[] = $oggetto;
			}
			$this->setRelatedOggettos($oggettos);
		}
		return $this->getData('related_oggettos');
	}

	/**
	 * Retrieve related oggettos identifiers
	 *
	 * @return array
	 */
	public function getRelatedOggettoIds() {
		if (!$this->hasRelatedOggettoIds()) {
			$ids = array();
			foreach ($this->getRelatedOggettos() as $oggetto) {
				$ids[] = $oggetto->getId();
			}
			$this->setRelatedOggettoIds($ids);
		}
		return $this->getData('related_oggetto_ids');
	}

	/**
	 * Retrieve collection related oggetto
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
	 */
	public function getRelatedOggettoCollection() {
		$collection = $this->getLinkInstance()->useRelatedLinks()
		                   ->getOggettoCollection()
		                   ->setIsStrongMode();

		// TODO: add loading of childs by attribute Set
		$collection->setOggetto($this);
		return $collection;
	}

	/**
	 * Retrieve collection related oggetto
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
	 */
	public function getParentOggettoCollection() {
		$collection = $this->getLinkInstance()->useRelatedLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addChildIdFilter();
		// $collection->joinAttributes();
		$parent_ids = $collection->getColumnValues('oggetto_id');
		//print_r($parent_ids); die;

		if (!count($parent_ids)) {
			$parent_ids[] = 0; // to return 0-size collection
		}
		// TODO: add loading of parents by attribute Set
		$collection = Mage::getModel('score/oggetto')->getCollection()->addIdFilter($parent_ids);
		return $collection;
	}

	/**
	 * Retrieve collection related link
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
	 */
	public function getRelatedLinkCollection() {
		$collection = $this->getLinkInstance()->useRelatedLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addOggettoIdFilter();
		$collection->joinAttributes();
		return $collection;
	}

	/**
	 * Retrieve collection related link
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
	 */
	public function getRelatedIds() {
		$collection = $this->getLinkInstance()->useRelatedLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addOggettoIdFilter();
		//$collection->joinAttributes();
		return $collection->getColumnValues('linked_oggetto_id');
	}

	/**
	 * Retrieve array of up sell oggettos
	 *
	 * @return array
	 */
	public function getUpSellOggettos() {
		if (!$this->hasUpSellOggettos()) {
			$oggettos = array();
			foreach ($this->getUpSellOggettoCollection() as $oggetto) {
				$oggettos[] = $oggetto;
			}
			$this->setUpSellOggettos($oggettos);
		}
		return $this->getData('up_sell_oggettos');
	}

	/**
	 * Retrieve up sell oggettos identifiers
	 *
	 * @return array
	 */
	public function getUpSellOggettoIds() {
		if (!$this->hasUpSellOggettoIds()) {
			$ids = array();
			foreach ($this->getUpSellOggettos() as $oggetto) {
				$ids[] = $oggetto->getId();
			}
			$this->setUpSellOggettoIds($ids);
		}
		return $this->getData('up_sell_oggetto_ids');
	}

	/**
	 * Retrieve collection up sell oggetto
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection
	 */
	public function getUpSellOggettoCollection() {
		$collection = $this->getLinkInstance()->useUpSellLinks()
		                   ->getOggettoCollection()
		                   ->setIsStrongMode();
		$collection->setOggetto($this);
		return $collection;
	}

	/**
	 * Retrieve collection up sell link
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
	 */
	public function getUpSellLinkCollection() {
		$collection = $this->getLinkInstance()->useUpSellLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addOggettoIdFilter();
		$collection->joinAttributes();
		return $collection;
	}

	/**
	 * Retrieve array of cross sell oggettos
	 *
	 * @return array
	 */
	public function getCrossSellOggettos() {
		if (!$this->hasCrossSellOggettos()) {
			$oggettos = array();
			foreach ($this->getCrossSellOggettoCollection() as $oggetto) {
				$oggettos[] = $oggetto;
			}
			$this->setCrossSellOggettos($oggettos);
		}
		return $this->getData('cross_sell_oggettos');
	}

	/**
	 * Retrieve cross sell oggettos identifiers
	 *
	 * @return array
	 */
	public function getCrossSellOggettoIds() {
		if (!$this->hasCrossSellOggettoIds()) {
			$ids = array();
			foreach ($this->getCrossSellOggettos() as $oggetto) {
				$ids[] = $oggetto->getId();
			}
			$this->setCrossSellOggettoIds($ids);
		}
		return $this->getData('cross_sell_oggetto_ids');
	}

	/**
	 * Retrieve collection cross sell oggetto
	 *
	 * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Link_Oggetto_Collection
	 */
	public function getCrossSellOggettoCollection() {
		$collection = $this->getLinkInstance()->useCrossSellLinks()
		                   ->getOggettoCollection()
		                   ->setIsStrongMode();
		$collection->setOggetto($this);
		return $collection;
	}

	/**
	 * Retrieve collection cross sell link
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
	 */
	public function getCrossSellLinkCollection() {
		$collection = $this->getLinkInstance()->useCrossSellLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addOggettoIdFilter();
		$collection->joinAttributes();
		return $collection;
	}

	/**
	 * Retrieve collection grouped link
	 *
	 * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
	 */
	public function getGroupedLinkCollection() {
		$collection = $this->getLinkInstance()->useGroupedLinks()
		                   ->getLinkCollection();
		$collection->setOggetto($this);
		$collection->addLinkTypeIdFilter();
		$collection->addOggettoIdFilter();
		$collection->joinAttributes();
		return $collection;
	}

/*******************************************************************************
 ** Media API
 */
	/**
	 * Retrive attributes for media gallery
	 *
	 * @return array
	 */
	public function getMediaAttributes() {
		if (!$this->hasMediaAttributes()) {
			$mediaAttributes = array();
			foreach ($this->getAttributes() as $attribute) {
				if ($attribute->getFrontend()->getInputType() == 'media_image') {
					$mediaAttributes[$attribute->getAttributeCode()] = $attribute;
				}
			}
			$this->setMediaAttributes($mediaAttributes);
		}
		return $this->getData('media_attributes');
	}

	/**
	 * Retrive media gallery images
	 *
	 * @return Varien_Data_Collection
	 */
	public function getMediaGalleryImages() {
		if (!$this->hasData('media_gallery_images') && is_array($this->getMediaGallery('images'))) {
			$images = new Varien_Data_Collection();
			foreach ($this->getMediaGallery('images') as $image) {
				if ($image['disabled']) {
					continue;
				}
				$image['url'] = $this->getMediaConfig()->getMediaUrl($image['file']);
				$image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
				$image['path'] = $this->getMediaConfig()->getMediaPath($image['file']);
				$images->addItem(new Varien_Object($image));
			}
			$this->setData('media_gallery_images', $images);
		}

		return $this->getData('media_gallery_images');
	}

	/**
	 * Add image to media gallery
	 *
	 * @param string        $file              file path of image in file system
	 * @param string|array  $mediaAttribute    code of attribute with type 'media_image',
	 *                                          leave blank if image should be only in gallery
	 * @param boolean       $move              if true, it will move source file
	 * @param boolean       $exclude           mark image as disabled in oggetto page view
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function addImageToMediaGallery($file, $mediaAttribute = null, $move = false, $exclude = true) {
		$attributes = $this->getTypeInstance(true)->getSetAttributes($this);
		if (!isset($attributes['media_gallery'])) {
			return $this;
		}
		$mediaGalleryAttribute = $attributes['media_gallery'];
		/* @var $mediaGalleryAttribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
		$mediaGalleryAttribute->getBackend()->addImage($this, $file, $mediaAttribute, $move, $exclude);
		return $this;
	}

	/**
	 * Retrive oggetto media config
	 *
	 * @return Shaurmalab_Score_Model_Oggetto_Media_Config
	 */
	public function getMediaConfig() {
		return Mage::getSingleton('score/oggetto_media_config');
	}

	/**
	 * Create duplicate
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function duplicate() {
		$this->getWebsiteIds();
		$this->getCategoryIds();

		/* @var $newOggetto Shaurmalab_Score_Model_Oggetto */
		$newOggetto = Mage::getModel('score/oggetto')->setData($this->getData())
		                                             ->setIsDuplicate(true)
		                                             ->setOriginalId($this->getId())
		                                             ->setSku(null)
		                                             ->setStatus(Shaurmalab_Score_Model_Oggetto_Status::STATUS_DISABLED)
		                                             ->setCreatedAt(null)
		                                             ->setUpdatedAt(null)
		                                             ->setId(null)
		                                             ->setStoreId(Mage::app()->getStore()->getId());

		Mage::dispatchEvent(
			'score_model_oggetto_duplicate',
			array('current_oggetto' => $this, 'new_oggetto' => $newOggetto)
		);

		/* Prepare Related*/
		$data = array();
		$this->getLinkInstance()->useRelatedLinks();
		$attributes = array();
		foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
			if (isset($_attribute['code'])) {
				$attributes[] = $_attribute['code'];
			}
		}
		foreach ($this->getRelatedLinkCollection() as $_link) {
			$data[$_link->getLinkedOggettoId()] = $_link->toArray($attributes);
		}
		$newOggetto->setRelatedLinkData($data);

		/* Prepare UpSell*/
		$data = array();
		$this->getLinkInstance()->useUpSellLinks();
		$attributes = array();
		foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
			if (isset($_attribute['code'])) {
				$attributes[] = $_attribute['code'];
			}
		}
		foreach ($this->getUpSellLinkCollection() as $_link) {
			$data[$_link->getLinkedOggettoId()] = $_link->toArray($attributes);
		}
		$newOggetto->setUpSellLinkData($data);

		/* Prepare Cross Sell */
		$data = array();
		$this->getLinkInstance()->useCrossSellLinks();
		$attributes = array();
		foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
			if (isset($_attribute['code'])) {
				$attributes[] = $_attribute['code'];
			}
		}
		foreach ($this->getCrossSellLinkCollection() as $_link) {
			$data[$_link->getLinkedOggettoId()] = $_link->toArray($attributes);
		}
		$newOggetto->setCrossSellLinkData($data);

		/* Prepare Grouped */
		$data = array();
		$this->getLinkInstance()->useGroupedLinks();
		$attributes = array();
		foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
			if (isset($_attribute['code'])) {
				$attributes[] = $_attribute['code'];
			}
		}
		foreach ($this->getGroupedLinkCollection() as $_link) {
			$data[$_link->getLinkedOggettoId()] = $_link->toArray($attributes);
		}
		$newOggetto->setGroupedLinkData($data);

		$newOggetto->save();

		$this->getOptionInstance()->duplicate($this->getId(), $newOggetto->getId());
		$this->getResource()->duplicate($this->getId(), $newOggetto->getId());

		// TODO - duplicate oggetto on all stores of the websites it is associated with
		/*if ($storeIds = $this->getWebsiteIds()) {
		foreach ($storeIds as $storeId) {
		$this->setStoreId($storeId)
		->load($this->getId());

		$newOggetto->setData($this->getData())
		->setSku(null)
		->setStatus(Shaurmalab_Score_Model_Oggetto_Status::STATUS_DISABLED)
		->setId($newId)
		->save();
		}
		}*/
		return $newOggetto;
	}

	/**
	 * Is oggetto grouped
	 *
	 * @return bool
	 */
	public function isSuperGroup() {
		return $this->getTypeId() == Shaurmalab_Score_Model_Oggetto_Type::TYPE_GROUPED;
	}

	/**
	 * Alias for isConfigurable()
	 *
	 * @return bool
	 */
	public function isSuperConfig() {
		return $this->isConfigurable();
	}
	/**
	 * Check is oggetto grouped
	 *
	 * @return bool
	 */
	public function isGrouped() {
		return $this->getTypeId() == Shaurmalab_Score_Model_Oggetto_Type::TYPE_GROUPED;
	}

	/**
	 * Check is oggetto configurable
	 *
	 * @return bool
	 */
	public function isConfigurable() {
		return $this->getTypeId() == Shaurmalab_Score_Model_Oggetto_Type::TYPE_CONFIGURABLE;
	}

	/**
	 * Whether oggetto configurable or grouped
	 *
	 * @return bool
	 */
	public function isSuper() {
		return $this->isConfigurable() || $this->isGrouped();
	}

	/**
	 * Returns visible status IDs in catalog
	 *
	 * @return array
	 */
	public function getVisibleInScoreStatuses() {
		return Mage::getSingleton('score/oggetto_status')->getVisibleStatusIds();
	}

	/**
	 * Retrieve visible statuses
	 *
	 * @return array
	 */
	public function getVisibleStatuses() {
		return Mage::getSingleton('score/oggetto_status')->getVisibleStatusIds();
	}

	/**
	 * Check Oggetto visilbe in catalog
	 *
	 * @return bool
	 */
	public function isVisibleInScore() {
		return in_array($this->getStatus(), $this->getVisibleInScoreStatuses());
	}

	/**
	 * Retrieve visible in site visibilities
	 *
	 * @return array
	 */
	public function getVisibleInSiteVisibilities() {
		return Mage::getSingleton('score/oggetto_visibility')->getVisibleInSiteIds();
	}

	/**
	 * Check Oggetto visible in site
	 *
	 * @return bool
	 */
	public function isVisibleInSiteVisibility() {
		return in_array($this->getVisibility(), $this->getVisibleInSiteVisibilities());
	}

	/**
	 * Checks oggetto can be duplicated
	 *
	 * @return boolean
	 */
	public function isDuplicable() {
		return $this->_isDuplicable;
	}

	/**
	 * Set is duplicable flag
	 *
	 * @param boolean $value
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function setIsDuplicable($value) {
		$this->_isDuplicable = (boolean) $value;
		return $this;
	}

	/**
	 * Check is oggetto available for sale
	 *
	 * @return bool
	 */
	public function isSalable() {
		Mage::dispatchEvent('score_oggetto_is_salable_before', array(
			'oggetto' => $this,
		));

		$salable = $this->isAvailable();

		$object = new Varien_Object(array(
			'oggetto' => $this,
			'is_salable' => $salable,
		));
		Mage::dispatchEvent('score_oggetto_is_salable_after', array(
			'oggetto' => $this,
			'salable' => $object,
		));
		return $object->getIsSalable();
	}

	/**
	 * Check whether the oggetto type or stock allows to purchase the oggetto
	 *
	 * @return bool
	 */
	public function isAvailable() {
		return $this->getTypeInstance(true)->isSalable($this)
		|| Mage::helper('score/oggetto')->getSkipSaleableCheck();
	}

	/**
	 * Is oggetto salable detecting by oggetto type
	 *
	 * @return bool
	 */
	public function getIsSalable() {
		$oggettoType = $this->getTypeInstance(true);
		if (method_exists($oggettoType, 'getIsSalable')) {
			return $oggettoType->getIsSalable($this);
		}
		if ($this->hasData('is_salable')) {
			return $this->getData('is_salable');
		}

		return $this->isSalable();
	}

	/**
	 * Check is a virtual oggetto
	 * Data helper wrapper
	 *
	 * @return bool
	 */
	public function isVirtual() {
		return $this->getIsVirtual();
	}

	/**
	 * Whether the oggetto is a recurring payment
	 *
	 * @return bool
	 */
	public function isRecurring() {
		return $this->getIsRecurring() == '1';
	}

	/**
	 * Alias for isSalable()
	 *
	 * @return bool
	 */
	public function isSaleable() {
		return $this->isSalable();
	}

	/**
	 * Whether oggetto available in stock
	 *
	 * @return bool
	 */
	public function isInStock() {
		return $this->getStatus() == Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED;
	}

	/**
	 * Get attribute text by its code
	 *
	 * @param $attributeCode Code of the attribute
	 * @return string
	 */
	public function getAttributeText($attributeCode) {
		return $this->getResource()
		            ->getAttribute($attributeCode)
		            ->getSource()
		            ->getOptionText($this->getData($attributeCode));
	}

	/**
	 * Returns array with dates for custom design
	 *
	 * @return array
	 */
	public function getCustomDesignDate() {
		$result = array();
		$result['from'] = $this->getData('custom_design_from');
		$result['to'] = $this->getData('custom_design_to');

		return $result;
	}

	/**
	 * Retrieve Oggetto URL
	 *
	 * @param  bool $useSid
	 * @return string
	 */
	public function getOggettoUrl($useSid = null) {
		return $this->getUrlModel()->getOggettoUrl($this, $useSid);
	}


	/**
	 * Retrieve Oggetto URL
	 *
	 * @param  bool $useSid
	 * @return string
	 */
	public function getUrl($useSid = null) {
		return  $this->getOggettoUrl($useSid = null);
	}

	/**
	 * Retrieve URL in current store
	 *
	 * @param array $params the route params
	 * @return string
	 */
	public function getUrlInStore($params = array()) {
		return $this->getUrlModel()->getUrlInStore($this, $params);
	}

	/**
	 * Formats URL key
	 *
	 * @param $str URL
	 * @return string
	 */
	public function formatUrlKey($str) {
		return $this->getUrlModel()->formatUrlKey($str);
	}

	/**
	 * Retrieve Oggetto Url Path (include category)
	 *
	 * @param Shaurmalab_Score_Model_Category $category
	 * @return string
	 */
	public function getUrlPath($category = null) {
		return $this->getUrlModel()->getUrlPath($this, $category);
	}

	/**
	 * Save current attribute with code $code and assign new value
	 *
	 * @param string $code  Attribute code
	 * @param mixed  $value New attribute value
	 * @param int    $store Store ID
	 * @return void
	 */
	public function addAttributeUpdate($code, $value, $store) {
		$oldValue = $this->getData($code);
		$oldStore = $this->getStoreId();

		$this->setData($code, $value);
		$this->setStoreId($store);
		$this->getResource()->saveAttribute($this, $code);

		$this->setData($code, $oldValue);
		$this->setStoreId($oldStore);
	}

	/**
	 * Renders the object to array
	 *
	 * @param array $arrAttributes Attribute array
	 * @return array
	 */
	public function toArray(array $arrAttributes = array()) {
		$data = parent::toArray($arrAttributes);
		if ($stock = $this->getStockItem()) {
			$data['stock_item'] = $stock->toArray();
		}
		unset($data['stock_item']['oggetto']);
		return $data;
	}

	/**
	 * Same as setData(), but also initiates the stock item (if it is there)
	 *
	 * @param array $data Array to form the object from
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function fromArray($data) {
		if (isset($data['stock_item'])) {
			if (Mage::helper('score')->isModuleEnabled('Mage_CatalogInventory')) {
				$stockItem = Mage::getModel('cataloginventory/stock_item')
					->setData($data['stock_item'])
					->setOggetto($this);
				$this->setStockItem($stockItem);
			}
			unset($data['stock_item']);
		}
		$this->setData($data);
		return $this;
	}

	/**
	 * @deprecated after 1.4.2.0
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function loadParentOggettoIds() {
		return $this->setParentOggettoIds(array());
	}

	/**
	 * Delete oggetto
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function delete() {
		parent::delete();
		Mage::dispatchEvent($this->_eventPrefix . '_delete_after_done', array($this->_eventObject => $this));
		return $this;
	}

	/**
	 * Returns request path
	 *
	 * @return string
	 */
	public function getRequestPath() {
		if (!$this->_getData('request_path')) {
			$this->getOggettoUrl();
		}
		return $this->_getData('request_path');
	}

	/**
	 * Custom function for other modules
	 * @return string
	 */

	public function getGiftMessageAvailable() {
		return $this->_getData('gift_message_available');
	}

	/**
	 * Returns rating summary
	 *
	 * @return mixed
	 */
	public function getRatingSummary() {
		return $this->_getData('rating_summary');
	}

	/**
	 * Check is oggetto composite
	 *
	 * @return bool
	 */
	public function isComposite() {
		return $this->getTypeInstance(true)->isComposite($this);
	}

	/**
	 * Check if oggetto can be configured
	 *
	 * @return bool
	 */
	public function canConfigure() {
		$options = $this->getOptions();
		return !empty($options) || $this->getTypeInstance(true)->canConfigure($this);
	}

	/**
	 * Retrieve sku through type instance
	 *
	 * @return string
	 */
	public function getSku() {
		return $this->getTypeInstance(true)->getSku($this);
	}

	/**
	 * Retrieve weight throught type instance
	 *
	 * @return unknown
	 */
	public function getWeight() {
		return $this->getTypeInstance(true)->getWeight($this);
	}

	/**
	 * Retrieve option instance
	 *
	 * @return Shaurmalab_Score_Model_Oggetto_Option
	 */
	public function getOptionInstance() {
		if (!$this->_optionInstance) {
			$this->_optionInstance = Mage::getSingleton('score/oggetto_option');
		}
		return $this->_optionInstance;
	}

	/**
	 * Retrieve options collection of oggetto
	 *
	 * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Option_Collection
	 */
	public function getOggettoOptionsCollection() {
		$collection = $this->getOptionInstance()
		                   ->getOggettoOptionCollection($this);

		return $collection;
	}

	/**
	 * Add option to array of oggetto options
	 *
	 * @param Shaurmalab_Score_Model_Oggetto_Option $option
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function addOption(Shaurmalab_Score_Model_Oggetto_Option $option) {
		$this->_options[$option->getId()] = $option;
		return $this;
	}

	/**
	 * Get option from options array of oggetto by given option id
	 *
	 * @param int $optionId
	 * @return Shaurmalab_Score_Model_Oggetto_Option | null
	 */
	public function getOptionById($optionId) {
		if (isset($this->_options[$optionId])) {
			return $this->_options[$optionId];
		}

		return null;
	}

	/**
	 * Get all options of oggetto
	 *
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}

	/**
	 * Retrieve is a virtual oggetto
	 *
	 * @return bool
	 */
	public function getIsVirtual() {
		return $this->getTypeInstance(true)->isVirtual($this);
	}

	/**
	 * Add custom option information to oggetto
	 *
	 * @param   string $code    Option code
	 * @param   mixed  $value   Value of the option
	 * @param   int    $oggetto Oggetto ID
	 * @return  Shaurmalab_Score_Model_Oggetto
	 */
	public function addCustomOption($code, $value, $oggetto = null) {
		$oggetto = $oggetto ? $oggetto : $this;
		$option = Mage::getModel('score/oggetto_configuration_item_option')
			->addData(array(
				'oggetto_id' => $oggetto->getId(),
				'oggetto' => $oggetto,
				'code' => $code,
				'value' => $value,
			));
		$this->_customOptions[$code] = $option;
		return $this;
	}

	/**
	 * Sets custom options for the oggetto
	 *
	 * @param array $options Array of options
	 * @return void
	 */
	public function setCustomOptions(array $options) {
		$this->_customOptions = $options;
	}

	/**
	 * Get all custom options of the oggetto
	 *
	 * @return array
	 */
	public function getCustomOptions() {
		return $this->_customOptions;
	}

	/**
	 * Get oggetto custom option info
	 *
	 * @param   string $code
	 * @return  array
	 */
	public function getCustomOption($code) {
		if (isset($this->_customOptions[$code])) {
			return $this->_customOptions[$code];
		}
		return null;
	}

	/**
	 * Checks if there custom option for this oggetto
	 *
	 * @return bool
	 */
	public function hasCustomOptions() {
		if (count($this->_customOptions)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check availability display oggetto in category
	 *
	 * @param   int $categoryId
	 * @return  bool
	 */
	public function canBeShowInCategory($categoryId) {
		return $this->_getResource()->canBeShowInCategory($this, $categoryId);
	}

	/**
	 * Retrieve category ids where oggetto is available
	 *
	 * @return array
	 */
	public function getAvailableInCategories() {
		return $this->_getResource()->getAvailableInCategories($this);
	}

	/**
	 * Retrieve default attribute set id
	 *
	 * @return int
	 */
	public function getDefaultAttributeSetId() {
		return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
	}

	/**
	 * Return Score Oggetto Image helper instance
	 *
	 * @return Shaurmalab_Score_Helper_Image
	 */
	protected function _getImageHelper() {
		return Mage::helper('score/image');
	}

	/**
	 * Return re-sized image URL
	 *
	 * @deprecated since 1.1.5
	 * @return string
	 */
	public function getImageUrl() {
		return (string) $this->_getImageHelper()->init($this, 'image')->resize(265);
	}

	/**
	 * Return re-sized small image URL
	 *
	 * @deprecated since 1.1.5
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function getSmallImageUrl($width = 88, $height = 77) {
		return (string) $this->_getImageHelper()->init($this, 'small_image')->resize($width, $height);
	}

	/**
	 * Return re-sized thumbnail image URL
	 *
	 * @deprecated since 1.1.5
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function getThumbnailUrl($width = 75, $height = 75) {
		return (string) $this->_getImageHelper()->init($this, 'thumbnail')->resize($width, $height);
	}

	/**
	 *  Returns system reserved attribute codes
	 *
	 *  @return array Reserved attribute names
	 */
	public function getReservedAttributes() {
		if ($this->_reservedAttributes === null) {
			$_reserved = array('position');
			$methods = get_class_methods(__CLASS__);
			foreach ($methods as $method) {
				if (preg_match('/^get([A-Z]{1}.+)/', $method, $matches)) {
					$method = $matches[1];
					$tmp = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $method));
					$_reserved[] = $tmp;
				}
			}
			$_allowed = array(
				'type_id', 'calculated_final_price', 'request_path', 'rating_summary',
			);
			$this->_reservedAttributes = array_diff($_reserved, $_allowed);
		}
		return $this->_reservedAttributes;
	}

	/**
	 *  Check whether attribute reserved or not
	 *
	 *  @param Shaurmalab_Score_Model_Oggetto_Attribute $attribute Attribute model object
	 *  @return boolean
	 */
	public function isReservedAttribute($attribute) {
		return $attribute->getIsUserDefined()
		&& in_array($attribute->getAttributeCode(), $this->getReservedAttributes());
	}

	/**
	 * Set original loaded data if needed
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return Varien_Object
	 */
	public function setOrigData($key = null, $data = null) {
		if (Mage::app()->getStore()->isAdmin()) {
			return parent::setOrigData($key, $data);
		}

		return $this;
	}

	/**
	 * Reset all model data
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function reset() {
		$this->unlockAttributes();
		$this->_clearData();
		return $this;
	}

	/**
	 * Get cahce tags associated with object id
	 *
	 * @return array
	 */
	public function getCacheIdTags() {
		$tags = parent::getCacheIdTags();
		$affectedCategoryIds = $this->getAffectedCategoryIds();
		if (!$affectedCategoryIds) {
			$affectedCategoryIds = $this->getCategoryIds();
		}
		foreach ($affectedCategoryIds as $categoryId) {
			$tags[] = Shaurmalab_Score_Model_Category::CACHE_TAG . '_' . $categoryId;
		}
		return $tags;
	}

	/**
	 * Check for empty SKU on each oggetto
	 *
	 * @param  array $oggettoIds
	 * @return boolean|null
	 */
	public function isOggettosHasSku(array $oggettoIds) {
		$oggettos = $this->_getResource()->getOggettosSku($oggettoIds);
		if (count($oggettos)) {
			foreach ($oggettos as $oggetto) {
				if (!strlen($oggetto['sku'])) {
					return false;
				}
			}
			return true;
		}
		return null;
	}

	/**
	 * Parse buyRequest into options values used by oggetto
	 *
	 * @param  Varien_Object $buyRequest
	 * @return Varien_Object
	 */
	public function processBuyRequest(Varien_Object $buyRequest) {
		$options = new Varien_Object();

		/* add oggetto custom options data */
		$customOptions = $buyRequest->getOptions();
		if (is_array($customOptions)) {
			$options->setOptions(array_diff($buyRequest->getOptions(), array('')));
		}

		/* add oggetto type selected options data */
		$type = $this->getTypeInstance(true);
		$typeSpecificOptions = $type->processBuyRequest($this, $buyRequest);
		$options->addData($typeSpecificOptions);

		/* check correctness of oggetto's options */
		$options->setErrors($type->checkOggettoConfiguration($this, $buyRequest));

		return $options;
	}

	/**
	 * Get preconfigured values from oggetto
	 *
	 * @return Varien_Object
	 */
	public function getPreconfiguredValues() {
		$preconfiguredValues = $this->getData('preconfigured_values');
		if (!$preconfiguredValues) {
			$preconfiguredValues = new Varien_Object();
		}

		return $preconfiguredValues;
	}

	/**
	 * Prepare oggetto custom options.
	 * To be sure that all oggetto custom options does not has ID and has oggetto instance
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function prepareCustomOptions() {
		foreach ($this->getCustomOptions() as $option) {
			if (!is_object($option->getOggetto()) || $option->getId()) {
				$this->addCustomOption($option->getCode(), $option->getValue());
			}
		}

		return $this;
	}

	/**
	 * Clearing references on oggetto
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _clearReferences() {
		$this->_clearOptionReferences();
		return $this;
	}

	/**
	 * Clearing oggetto's data
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _clearData() {
		foreach ($this->_data as $data) {
			if (is_object($data) && method_exists($data, 'reset')) {
				$data->reset();
			}
		}

		$this->setData(array());
		$this->setOrigData();
		$this->_customOptions = array();
		$this->_optionInstance = null;
		$this->_options = array();
		$this->_canAffectOptions = false;
		$this->_errors = array();

		return $this;
	}

	/**
	 * Clearing references to oggetto from oggetto's options
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _clearOptionReferences() {
		/**
		 * unload oggetto options
		 */
		if (!empty($this->_options)) {
			foreach ($this->_options as $key => $option) {
				$option->setOggetto();
				$option->clearInstance();
			}
		}

		return $this;
	}

	/**
	 * Retrieve oggetto entities info as array
	 *
	 * @param string|array $columns One or several columns
	 * @return array
	 */
	public function getOggettoEntitiesInfo($columns = null) {
		return $this->_getResource()->getOggettoEntitiesInfo($columns);
	}

	/**
	 * Checks whether oggetto has disabled status
	 *
	 * @return bool
	 */
	public function isDisabled() {
		return $this->getStatus() == Shaurmalab_Score_Model_Oggetto_Status::STATUS_DISABLED;
	}

	public function isLocked() {
		return in_array($this->getLock(), array(1, "1", true));
	}

	public function getOwnerObject() {
		return Mage::getModel('score/customer')->load($this->getOwner());
	}

	public function addImageToGalleryByUrl($image_url) {
		//if(!$image_url) return $this;
		try {
			$image_type = substr(strrchr($image_url, "."), 1); //find the image extension
			$filename = md5($image_url) . '.' . $image_type; //give a new name, you can modify as per your requirement
			$filepath = Mage::getBaseDir('media') . DS . 'import' . DS . $filename; //path for temp storage folder: ./media/import/

			$ch = curl_init($image_url);
			$fp = fopen($filepath, 'wb');
			chmod($filepath, 755);
			$proxy = '190.37.218.96:8080';
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			$mediaAttribute = array(
				'thumbnail',
				'small_image',
				'image',
			);

			$attributes = $this->getTypeInstance(true)->getSetAttributes($this);
			if (!isset($attributes['media_gallery'])) {
				return $this;
			}
			$mediaGalleryAttribute = $attributes['media_gallery'];

			$mediaGalleryAttribute->getBackend()->addImage($this, $filepath, $mediaAttribute, 0, 0);
		} catch (Exception $e) {echo $e->getMessage();}
		return $this;
	}

	public function availableForSave() {
		// Only owner can edit attributes for now.
		if ($this->getOwner() == Mage::getSingleton('customer/session')->getCustomerId() && Mage::getSingleton('customer/session')->getCustomerId() != 0) {
			return true;
		}
		// SuperAdmin can do it too
		if(Mage::helper('constructor')->isSuperAdmin()) {
			return true;
		}

		return false;
	}

	public function availableForView() {

	}

	public function checkOggettoReminder() {

		$remind_in = $this->getAttributeText('remind_in');
		$date = strtotime($this->getCreatedAt()); // Oggetto created
		$date = strtotime("+" . $remind_in, $date); // should remind about it at

		date_default_timezone_set('UTC');
		echo '<br/>Oggetto #' . $this->getId() . '<br/>; Remind in ' . $this->getAttributeText('remind_in') . ';<br/> Created: ' . $this->getCreatedAt() . ';<br/> Remind in: ' . date('Y-m-d h:i:s', $date) . ';<br/> Now: ' . now();

		if (now() > date('Y-m-d h:i:s', $date)) {
			echo "<br/> Going to remind ya!";
			$customer = Mage::getModel('customer/customer')->load($this->getOwner());
			if (Mage::helper('score')->sendMailByCode('Page reminder', $this, $customer)) {
				echo "Reminder sent";
				$this->setReminded(1)->save();
			}
		}
		return $this;
	}



	public function getAvailableObjects($setId) {
		$collection = $this->getCollection()
		->addAttributeToFilter('attribute_set_id', $setId)
		->addAttributeToFilter('visibility', array('neq'=>1))
		;

			$collection->addAttributeToSelect(array('name'), 'left');
		return $collection;
	}

	public function getSetName() {
		return Mage::getModel('score/config')->getAttributeSetName('score_oggetto', $this->getAttributeSetId());
	}

	public function getRegion() {
		return Mage::getModel('score/oggetto')->getAvailableObjects(22)->addAttributeToFilter('entity_id', $this->getRegionId())->getFirstItem()->getTitle();
	}

	public function getLocation() {
		return Mage::getModel('score/oggetto')->getAvailableObjects(21)->addAttributeToFilter('entity_id', $this->getLocationId())->getFirstItem()->getTitle();
	}

}
