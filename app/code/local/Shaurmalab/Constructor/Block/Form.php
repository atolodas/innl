<?php
class Shaurmalab_Constructor_Block_Form extends Mage_Core_Block_Template {

	public function _construct() {
		parent::_construct();
	}

	public function isAdmin() {
		return Mage::helper('constructor')->isAdmin();
	}

	public function getConfigOptions($section) {

	}

	public function getImageUrl($value, $type) {

		switch ($type) {
			case 'logo':
				$this->getSkinUrl($value);
				break;
			case 'favicon':
				$folderName = Mage_Adminhtml_Model_System_Config_Backend_Image_Favicon::UPLOAD_DIR;
				$storeConfig = $value;
				$faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
				$absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

				if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
					$url = $faviconFile;
				} else {
					$url = $this->getSkinUrl('favicon.ico');
				}
				return $url;
				break;

			default:

				break;
		}

	}

	protected function _isFile($filename) {
		if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
			Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
		}
		return is_file($filename);
	}

}