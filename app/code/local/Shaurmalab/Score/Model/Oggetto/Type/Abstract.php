<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract model for oggetto type implementation
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_Score_Model_Oggetto_Type_Abstract {
	/**
	 * Oggetto model instance
	 *
	 * @deprecated if use as singleton
	 * @var Shaurmalab_Score_Model_Oggetto
	 */
	protected $_oggetto;

	/**
	 * Oggetto type instance id
	 *
	 * @var string
	 */
	protected $_typeId;

	/**
	 * @deprecated
	 *
	 * @var array
	 */
	protected $_setAttributes;

	/**
	 * @deprecated
	 *
	 * @var array
	 */
	protected $_editableAttributes;

	/**
	 * Is a composite oggetto type
	 *
	 * @var bool
	 */
	protected $_isComposite = false;

	/**
	 * Is a configurable oggetto type
	 *
	 * @var bool
	 */
	protected $_canConfigure = false;

	/**
	 * Whether oggetto quantity is fractional number or not
	 *
	 * @var bool
	 */
	protected $_canUseQtyDecimals = true;

	/**
	 * @deprecated
	 *
	 * @var int
	 */
	protected $_storeFilter = null;

	/**
	 * File queue array
	 *
	 * @var array
	 */
	protected $_fileQueue = array();

	const CALCULATE_CHILD = 0;
	const CALCULATE_PARENT = 1;

	/**
	 * values for shipment type (invoice etc)
	 *
	 */
	const SHIPMENT_SEPARATELY = 1;
	const SHIPMENT_TOGETHER = 0;

	/**
	 * Process modes
	 *
	 * Full validation - all required options must be set, whole configuration
	 * must be valid
	 */
	const PROCESS_MODE_FULL = 'full';

	/**
	 * Process modes
	 *
	 * Lite validation - only received options are validated
	 */
	const PROCESS_MODE_LITE = 'lite';

	/**
	 * Item options prefix
	 */
	const OPTION_PREFIX = 'option_';

	/**
	 * Specify type instance oggetto
	 *
	 * @param   Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return  Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function setOggetto($oggetto) {
		$this->_oggetto = $oggetto;
		return $this;
	}

	/**
	 * Specify type identifier
	 *
	 * @param   string $typeId
	 * @return  Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function setTypeId($typeId) {
		$this->_typeId = $typeId;
		return $this;
	}

	/**
	 * Retrieve score oggetto object
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	public function getOggetto($oggetto = null) {
		if (is_object($oggetto)) {
			return $oggetto;
		}
		return $this->_oggetto;
	}

	/**
	 * Return relation info about used oggettos for specific type instance
	 *
	 * @return Varien_Object Object with information data
	 */
	public function getRelationInfo() {
		return new Varien_Object();
	}

	/**
	 * Retrieve Required children ids
	 * Return grouped array, ex array(
	 *   group => array(ids)
	 * )
	 *
	 * @param int $parentId
	 * @param bool $required
	 * @return array
	 */
	public function getChildrenIds($parentId, $required = true) {
		return array();
	}

	/**
	 * Retrieve parent ids array by requered child
	 *
	 * @param int|array $childId
	 * @return array
	 */
	public function getParentIdsByChild($childId) {
		return array();
	}

	/**
	 * Get array of oggetto set attributes
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	public function getSetAttributes($oggetto = null) {
		return $this->getOggetto($oggetto)->getResource()
		            ->loadAllAttributes($this->getOggetto($oggetto))
		            ->getSortedAttributes($this->getOggetto($oggetto)->getAttributeSetId());
	}

	/**
	 * Compare attribues sorting
	 *
	 * @param Shaurmalab_Score_Model_Oggetto_Attribute $attribute1
	 * @param Shaurmalab_Score_Model_Oggetto_Attribute $attribute2
	 * @return int
	 */
	public function attributesCompare($attribute1, $attribute2) {
		$sort1 = ($attribute1->getGroupSortPath() * 1000) + ($attribute1->getSortPath() * 0.0001);
		$sort2 = ($attribute2->getGroupSortPath() * 1000) + ($attribute2->getSortPath() * 0.0001);

		if ($sort1 > $sort2) {
			return 1;
		} elseif ($sort1 < $sort2) {
			return -1;
		}

		return 0;
	}

	/**
	 * Retrieve oggetto type attributes
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	public function getEditableAttributes($oggetto = null) {
		$cacheKey = '_cache_editable_attributes';
		if (!$this->getOggetto($oggetto)->hasData($cacheKey)) {
			$editableAttributes = array();
			foreach ($this->getSetAttributes($oggetto) as $attributeCode => $attribute) {
				if (!is_array($attribute->getApplyTo())
					|| count($attribute->getApplyTo()) == 0
					|| in_array($this->getOggetto($oggetto)->getTypeId(), $attribute->getApplyTo())) {
					$editableAttributes[$attributeCode] = $attribute;
				}
			}
			$this->getOggetto($oggetto)->setData($cacheKey, $editableAttributes);
		}
		return $this->getOggetto($oggetto)->getData($cacheKey);
	}

	/**
	 * Retrieve oggetto attribute by identifier
	 *
	 * @param   int $attributeId
	 * @return  Mage_Eav_Model_Entity_Attribute_Abstract
	 */
	public function getAttributeById($attributeId, $oggetto = null) {
		foreach ($this->getSetAttributes($oggetto) as $attribute) {
			if ($attribute->getId() == $attributeId) {
				return $attribute;
			}
		}
		return null;
	}

	/**
	 * Check is virtual oggetto
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function isVirtual($oggetto = null) {
		return false;
	}

	/**
	 * Check is oggetto available for sale
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function isSalable($oggetto = null) {
		$salable = $this->getOggetto($oggetto)->getStatus() == Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED;
		if ($salable && $this->getOggetto($oggetto)->hasData('is_salable')) {
			$salable = $this->getOggetto($oggetto)->getData('is_salable');
		} elseif ($salable && $this->isComposite()) {
			$salable = null;
		}

		return (boolean) (int) $salable;
	}

	/**
	 * Prepare oggetto and its configuration to be added to some oggettos list.
	 * Perform standard preparation process and then prepare options belonging to specific oggetto type.
	 *
	 * @param  Varien_Object $buyRequest
	 * @param  Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param  string $processMode
	 * @return array|string
	 */
	protected function _prepareOggetto(Varien_Object $buyRequest, $oggetto, $processMode) {
		$oggetto = $this->getOggetto($oggetto);
		/* @var Shaurmalab_Score_Model_Oggetto $oggetto */
		// try to add custom options
		try {
			$options = $this->_prepareOptions($buyRequest, $oggetto, $processMode);
		} catch (Mage_Core_Exception $e) {
			return $e->getMessage();
		}

		if (is_string($options)) {
			return $options;
		}
		// try to found super oggetto configuration
		// (if oggetto was buying within grouped oggetto)
		$superOggettoConfig = $buyRequest->getSuperOggettoConfig();
		if (!empty($superOggettoConfig['oggetto_id'])
			&& !empty($superOggettoConfig['oggetto_type'])
		) {
			$superOggettoId = (int) $superOggettoConfig['oggetto_id'];
			if ($superOggettoId) {
				if (!$superOggetto = Mage::registry('used_super_oggetto_' . $superOggettoId)) {
					$superOggetto = Mage::getModel('score/oggetto')->load($superOggettoId);
					Mage::register('used_super_oggetto_' . $superOggettoId, $superOggetto);
				}
				if ($superOggetto->getId()) {
					$assocOggettoIds = $superOggetto->getTypeInstance(true)->getAssociatedOggettoIds($superOggetto);
					if (in_array($oggetto->getId(), $assocOggettoIds)) {
						$oggettoType = $superOggettoConfig['oggetto_type'];
						$oggetto->addCustomOption('oggetto_type', $oggettoType, $superOggetto);

						$buyRequest->setData('super_oggetto_config', array(
							'oggetto_type' => $oggettoType,
							'oggetto_id' => $superOggetto->getId(),
						));
					}
				}
			}
		}

		$oggetto->prepareCustomOptions();
		$buyRequest->unsetData('_processing_params'); // One-time params only
		$oggetto->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

		if ($options) {
			$optionIds = array_keys($options);
			$oggetto->addCustomOption('option_ids', implode(',', $optionIds));
			foreach ($options as $optionId => $optionValue) {
				$oggetto->addCustomOption(self::OPTION_PREFIX . $optionId, $optionValue);
			}
		}

		// set quantity in cart
		if ($this->_isStrictProcessMode($processMode)) {
			$oggetto->setCartQty($buyRequest->getQty());
		}
		$oggetto->setQty($buyRequest->getQty());

		return array($oggetto);
	}

	/**
	 * Process oggetto configuaration
	 *
	 * @param Varien_Object $buyRequest
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param string $processMode
	 * @return array|string
	 */
	public function processConfiguration(Varien_Object $buyRequest, $oggetto = null,
		$processMode = self::PROCESS_MODE_LITE) {
		$_oggettos = $this->_prepareOggetto($buyRequest, $oggetto, $processMode);

		$this->processFileQueue();

		return $_oggettos;
	}

	/**
	 * Initialize oggetto(s) for add to cart process.
	 * Advanced version of func to prepare oggetto for cart - processMode can be specified there.
	 *
	 * @param Varien_Object $buyRequest
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param null|string $processMode
	 * @return array|string
	 */
	public function prepareForCartAdvanced(Varien_Object $buyRequest, $oggetto = null, $processMode = null) {
		if (!$processMode) {
			$processMode = self::PROCESS_MODE_FULL;
		}
		$_oggettos = $this->_prepareOggetto($buyRequest, $oggetto, $processMode);
		$this->processFileQueue();
		return $_oggettos;
	}

	/**
	 * Initialize oggetto(s) for add to cart process
	 *
	 * @param Varien_Object $buyRequest
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array|string
	 */
	public function prepareForCart(Varien_Object $buyRequest, $oggetto = null) {
		return $this->prepareForCartAdvanced($buyRequest, $oggetto, self::PROCESS_MODE_FULL);
	}

	/**
	 * Process File Queue
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function processFileQueue() {
		if (empty($this->_fileQueue)) {
			return $this;
		}

		foreach ($this->_fileQueue as &$queueOptions) {
			if (isset($queueOptions['operation']) && $operation = $queueOptions['operation']) {
				switch ($operation) {
					case 'receive_uploaded_file':
						$src = isset($queueOptions['src_name']) ? $queueOptions['src_name'] : '';
						$dst = isset($queueOptions['dst_name']) ? $queueOptions['dst_name'] : '';
					/** @var $uploader Zend_File_Transfer_Adapter_Http */
						$uploader = isset($queueOptions['uploader']) ? $queueOptions['uploader'] : null;

						$path = dirname($dst);
						$io = new Varien_Io_File();
						if (!$io->isWriteable($path) && !$io->mkdir($path, 0777, true)) {
							Mage::throwException(Mage::helper('score')->__("Cannot create writeable directory '%s'.", $path));
						}

						$uploader->setDestination($path);

						if (empty($src) || empty($dst) || !$uploader->receive($src)) {
						/**
						 * @todo: show invalid option
						 */
							if (isset($queueOptions['option'])) {
								$queueOptions['option']->setIsValid(false);
							}
							Mage::throwException(Mage::helper('score')->__("File upload failed"));
						}
						Mage::helper('core/file_storage_database')->saveFile($dst);
						break;
					case 'move_uploaded_file':
						$src = $queueOptions['src_name'];
						$dst = $queueOptions['dst_name'];
						move_uploaded_file($src, $dst);
						Mage::helper('core/file_storage_database')->saveFile($dst);
						break;
					default:
						break;
				}
			}
			$queueOptions = null;
		}

		return $this;
	}

	/**
	 * Add file to File Queue
	 * @param array $queueOptions   Array of File Queue
	 *                              (eg. ['operation'=>'move',
	 *                                    'src_name'=>'filename',
	 *                                    'dst_name'=>'filename2'])
	 */
	public function addFileQueue($queueOptions) {
		$this->_fileQueue[] = $queueOptions;
	}

	/**
	 * Check if current process mode is strict
	 *
	 * @param string $processMode
	 * @return bool
	 */
	protected function _isStrictProcessMode($processMode) {
		return $processMode == self::PROCESS_MODE_FULL;
	}

	/**
	 * Retrieve message for specify option(s)
	 *
	 * @return string
	 */
	public function getSpecifyOptionMessage() {
		return Mage::helper('score')->__('Please specify the oggetto\'s required option(s).');
	}

	/**
	 * Process custom defined options for oggetto
	 *
	 * @param Varien_Object $buyRequest
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param string $processMode
	 * @return array
	 */
	protected function _prepareOptions(Varien_Object $buyRequest, $oggetto, $processMode) {
		$transport = new StdClass;
		$transport->options = array();
		foreach ($this->getOggetto($oggetto)->getOptions() as $_option) {
			/* @var $_option Shaurmalab_Score_Model_Oggetto_Option */
			$group = $_option->groupFactory($_option->getType())
			                 ->setOption($_option)
			                 ->setOggetto($this->getOggetto($oggetto))
			                 ->setRequest($buyRequest)
			                 ->setProcessMode($processMode)
			                 ->validateUserValue($buyRequest->getOptions());

			$preparedValue = $group->prepareForCart();
			if ($preparedValue !== null) {
				$transport->options[$_option->getId()] = $preparedValue;
			}
		}

		$eventName = sprintf('score_oggetto_type_prepare_%s_options', $processMode);
		Mage::dispatchEvent($eventName, array(
			'transport' => $transport,
			'buy_request' => $buyRequest,
			'oggetto' => $oggetto,
		));
		return $transport->options;
	}

	/**
	 * Process oggetto custom defined options for cart
	 *
	 * @deprecated after 1.4.2.0
	 * @see _prepareOptions()
	 *
	 * @param Varien_Object $buyRequest
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	protected function _prepareOptionsForCart(Varien_Object $buyRequest, $oggetto = null) {
		return $this->_prepareOptions($buyRequest, $oggetto, self::PROCESS_MODE_FULL);
	}

	/**
	 * Check if oggetto can be bought
	 *
	 * @param  Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 * @throws Mage_Core_Exception
	 */
	public function checkOggettoBuyState($oggetto = null) {
		if (!$this->getOggetto($oggetto)->getSkipCheckRequiredOption()) {
			foreach ($this->getOggetto($oggetto)->getOptions() as $option) {
				if ($option->getIsRequire()) {
					$customOption = $this->getOggetto($oggetto)
					                     ->getCustomOption(self::OPTION_PREFIX . $option->getId());
					if (!$customOption || strlen($customOption->getValue()) == 0) {
						$this->getOggetto($oggetto)->setSkipCheckRequiredOption(true);
						Mage::throwException(
							Mage::helper('score')->__('The oggetto has required options')
						);
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Prepare additional options/information for order item which will be
	 * created from this oggetto
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	public function getOrderOptions($oggetto = null) {
		$optionArr = array();
		if ($info = $this->getOggetto($oggetto)->getCustomOption('info_buyRequest')) {
			$optionArr['info_buyRequest'] = unserialize($info->getValue());
		}

		if ($optionIds = $this->getOggetto($oggetto)->getCustomOption('option_ids')) {
			foreach (explode(',', $optionIds->getValue()) as $optionId) {
				if ($option = $this->getOggetto($oggetto)->getOptionById($optionId)) {

					$confItemOption = $this->getOggetto($oggetto)
					                       ->getCustomOption(self::OPTION_PREFIX . $option->getId());

					$group = $option->groupFactory($option->getType())
					                ->setOption($option)
					                ->setOggetto($this->getOggetto())
					                ->setConfigurationItemOption($confItemOption);

					$optionArr['options'][] = array(
						'label' => $option->getTitle(),
						'value' => $group->getFormattedOptionValue($confItemOption->getValue()),
						'print_value' => $group->getPrintableOptionValue($confItemOption->getValue()),
						'option_id' => $option->getId(),
						'option_type' => $option->getType(),
						'option_value' => $confItemOption->getValue(),
						'custom_view' => $group->isCustomizedView(),
					);
				}
			}
		}

		if ($oggettoTypeConfig = $this->getOggetto($oggetto)->getCustomOption('oggetto_type')) {
			$optionArr['super_oggetto_config'] = array(
				'oggetto_code' => $oggettoTypeConfig->getCode(),
				'oggetto_type' => $oggettoTypeConfig->getValue(),
				'oggetto_id' => $oggettoTypeConfig->getOggettoId(),
			);
		}

		return $optionArr;
	}

	/**
	 * Save type related data
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function save($oggetto = null) {
		return $this;
	}

	/**
	 * Remove don't applicable attributes data
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 */
	protected function _removeNotApplicableAttributes($oggetto = null) {
		$oggetto = $this->getOggetto($oggetto);
		$eavConfig = Mage::getSingleton('eav/config');
		$entityType = $oggetto->getResource()->getEntityType();
		foreach ($eavConfig->getEntityAttributeCodes($entityType, $oggetto) as $attributeCode) {
			$attribute = $eavConfig->getAttribute($entityType, $attributeCode);
			$applyTo = $attribute->getApplyTo();
			if (is_array($applyTo) && count($applyTo) > 0 && !in_array($oggetto->getTypeId(), $applyTo)) {
				$oggetto->unsetData($attribute->getAttributeCode());
			}
		}
	}

	/**
	 * Before save type related data
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function beforeSave($oggetto = null) {
		$this->_removeNotApplicableAttributes($oggetto);
		$this->getOggetto($oggetto)->canAffectOptions(true);
		return $this;
	}

	/**
	 * Check if oggetto is composite (grouped, configurable, etc)
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function isComposite($oggetto = null) {
		return $this->_isComposite;
	}

	/**
	 * Check if oggetto is configurable
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function canConfigure($oggetto = null) {
		return $this->_canConfigure;
	}

	/**
	 * Check if oggetto qty is fractional number
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function canUseQtyDecimals() {
		return $this->_canUseQtyDecimals;
	}

	/**
	 * Default action to get sku of oggetto
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return string
	 */
	public function getSku($oggetto = null) {
		$sku = $this->getOggetto($oggetto)->getData('sku');
		if ($this->getOggetto($oggetto)->getCustomOption('option_ids')) {
			$sku = $this->getOptionSku($oggetto, $sku);
		}
		return $sku;
	}

	/**
	 * Default action to get sku of oggetto with option
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto Oggetto with Custom Options
	 * @param string $sku Oggetto SKU without option
	 * @return string
	 */
	public function getOptionSku($oggetto = null, $sku = '') {
		$skuDelimiter = '-';
		if (empty($sku)) {
			$sku = $this->getOggetto($oggetto)->getData('sku');
		}
		if ($optionIds = $this->getOggetto($oggetto)->getCustomOption('option_ids')) {
			foreach (explode(',', $optionIds->getValue()) as $optionId) {
				if ($option = $this->getOggetto($oggetto)->getOptionById($optionId)) {

					$confItemOption = $this->getOggetto($oggetto)->getCustomOption(self::OPTION_PREFIX . $optionId);

					$group = $option->groupFactory($option->getType())
					                ->setOption($option)->setListener(new Varien_Object());

					if ($optionSku = $group->getOptionSku($confItemOption->getValue(), $skuDelimiter)) {
						$sku .= $skuDelimiter . $optionSku;
					}

					if ($group->getListener()->getHasError()) {
						$this->getOggetto($oggetto)
						     ->setHasError(true)
						     ->setMessage(
							     $group->getListener()->getMessage()
						     );
					}

				}
			}
		}
		return $sku;
	}
	/**
	 * Default action to get weight of oggetto
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return decimal
	 */
	public function getWeight($oggetto = null) {
		return $this->getOggetto($oggetto)->getData('weight');
	}

	/**
	 * Return true if oggetto has options
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function hasOptions($oggetto = null) {
		if ($this->getOggetto($oggetto)->getHasOptions()) {
			return true;
		}
		if ($this->getOggetto($oggetto)->isRecurring()) {
			return true;
		}
		return false;
	}

	/**
	 * Method is needed for specific actions to change given configuration options values
	 * according current oggetto type logic
	 * Example: the cataloginventory validation of decimal qty can change qty to int,
	 * so need to change configuration item qty option value too.
	 *
	 * @param array         $options
	 * @param Varien_Object $option
	 * @param mixed         $value
	 *
	 * @return object       Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function updateQtyOption($options, Varien_Object $option, $value, $oggetto = null) {
		return $this;
	}

	/**
	 * Check if oggetto has required options
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return bool
	 */
	public function hasRequiredOptions($oggetto = null) {
		if ($this->getOggetto($oggetto)->getRequiredOptions()) {
			return true;
		}
		return false;
	}

	/**
	 * Retrive store filter for associated oggettos
	 *
	 * @return int|Mage_Core_Model_Store
	 */
	public function getStoreFilter($oggetto = null) {
		$cacheKey = '_cache_instance_store_filter';
		return $this->getOggetto($oggetto)->getData($cacheKey);
	}

	/**
	 * Set store filter for associated oggettos
	 *
	 * @param $store int|Mage_Core_Model_Store
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Configurable
	 */
	public function setStoreFilter($store = null, $oggetto = null) {
		$cacheKey = '_cache_instance_store_filter';
		$this->getOggetto($oggetto)->setData($cacheKey, $store);
		return $this;
	}

	/**
	 * Allow for updates of chidren qty's
	 * (applicable for complicated oggetto types. As default returns false)
	 *
	 * @return boolean false
	 */
	public function getForceChildItemQtyChanges($oggetto = null) {
		return false;
	}

	/**
	 * Prepare Quote Item Quantity
	 *
	 * @param mixed $qty
	 * @return float
	 */
	public function prepareQuoteItemQty($qty, $oggetto = null) {
		return floatval($qty);
	}

	/**
	 * Implementation of oggetto specify logic of which oggetto needs to be assigned to option.
	 * For example if oggetto which was added to option already removed from catalog.
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $optionOggetto
	 * @param Mage_Sales_Model_Quote_Item_Option $option
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function assignOggettoToOption($optionOggetto, $option, $oggetto = null) {
		$option->setOggetto($optionOggetto ? $optionOggetto : $this->getOggetto($oggetto));
		return $this;
	}

	/**
	 * Setting specified oggetto type variables
	 *
	 * @param array $config
	 * @return Shaurmalab_Score_Model_Oggetto_Type_Abstract
	 */
	public function setConfig($config) {
		if (isset($config['composite'])) {
			$this->_isComposite = (bool) $config['composite'];
		}

		if (isset($config['can_use_qty_decimals'])) {
			$this->_canUseQtyDecimals = (bool) $config['can_use_qty_decimals'];
		}

		return $this;
	}

	/**
	 * Retrieve additional searchable data from type instance
	 * Using based on oggetto id and store_id data
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	public function getSearchableData($oggetto = null) {
		$oggetto = $this->getOggetto($oggetto);
		$searchData = array();
		if ($oggetto->getHasOptions()) {
			$searchData = Mage::getSingleton('score/oggetto_option')
				->getSearchableData($oggetto->getId(), $oggetto->getStoreId());
		}

		return $searchData;
	}

	/**
	 * Retrieve oggettos divided into groups required to purchase
	 * At least one oggetto in each group has to be purchased
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return array
	 */
	public function getOggettosToPurchaseByReqGroups($oggetto = null) {
		$oggetto = $this->getOggetto($oggetto);
		if ($this->isComposite($oggetto)) {
			return array();
		}
		return array(array($oggetto));
	}

	/**
	 * Prepare selected options for oggetto
	 *
	 * @param  Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param  Varien_Object $buyRequest
	 * @return array
	 */
	public function processBuyRequest($oggetto, $buyRequest) {
		return array();
	}

	/**
	 * Check oggetto's options configuration
	 *
	 * @param  Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param  Varien_Object $buyRequest
	 * @return array
	 */
	public function checkOggettoConfiguration($oggetto, $buyRequest) {
		$errors = array();

		try {
			/**
			 * cloning oggetto because prepareForCart() method will modify it
			 */
			$oggettoForCheck = clone $oggetto;
			$buyRequestForCheck = clone $buyRequest;
			$result = $this->prepareForCart($buyRequestForCheck, $oggettoForCheck);

			if (is_string($result)) {
				$errors[] = $result;
			}
		} catch (Mage_Core_Exception $e) {
			$errors[] = $e->getMessage();
		} catch (Exception $e) {
			Mage::logException($e);
			$errors[] = Mage::helper('score')->__('There was an error while request processing.');
		}

		return $errors;
	}

	/**
	 * Check if Minimum advertise price is enabled at least in one option
	 *
	 * @param Shaurmalab_Score_Model_Oggetto $oggetto
	 * @param int $visibility
	 * @return bool
	 */
	public function isMapEnabledInOptions($oggetto, $visibility = null) {
		return false;
	}
}
