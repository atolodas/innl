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
 * Score oggetto media api
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Attribute_Media_Api extends Shaurmalab_Score_Model_Api_Resource
{
    /**
     * Attribute code for media gallery
     *
     */
    const ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed mime types for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png'
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'oggetto_store_id';
    }

    /**
     * Retrieve images for oggetto
     *
     * @param int|string $oggettoId
     * @param string|int $store
     * @return array
     */
    public function items($oggettoId, $store = null, $identifierType = null)
    {
        $oggetto = $this->_initOggetto($oggettoId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($oggetto);

        $galleryData = $oggetto->getData(self::ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            return array();
        }

        $result = array();

        foreach ($galleryData['images'] as &$image) {
            $result[] = $this->_imageToArray($image, $oggetto);
        }

        return $result;
    }

    /**
     * Retrieve image data
     *
     * @param int|string $oggettoId
     * @param string $file
     * @param string|int $store
     * @return array
     */
    public function info($oggettoId, $file, $store = null, $identifierType = null)
    {
        $oggetto = $this->_initOggetto($oggettoId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($oggetto);

        if (!$image = $gallery->getBackend()->getImage($oggetto, $file)) {
            $this->_fault('not_exists');
        }

        return $this->_imageToArray($image, $oggetto);
    }

    /**
     * Create new image for oggetto and return image filename
     *
     * @param int|string $oggettoId
     * @param array $data
     * @param string|int $store
     * @return string
     */
    public function create($oggettoId, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $oggetto = $this->_initOggetto($oggettoId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($oggetto);

        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
            $this->_fault('data_invalid', Mage::helper('score')->__('The image is not specified.'));
        }

        if (!isset($this->_mimeTypes[$data['file']['mime']])) {
            $this->_fault('data_invalid', Mage::helper('score')->__('Invalid image type.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent) {
            $this->_fault('data_invalid', Mage::helper('score')->__('The image contents is not valid base64 data.'));
        }

        unset($data['file']['content']);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();

        if (isset($data['file']['name']) && $data['file']['name']) {
            $fileName  = $data['file']['name'];
        } else {
            $fileName  = 'image';
        }
        $fileName .= '.' . $this->_mimeTypes[$data['file']['mime']];

        $ioAdapter = new Varien_Io_File();
        try {
            // Create temporary directory for api
            $ioAdapter->checkAndCreateFolder($tmpDirectory);
            $ioAdapter->open(array('path'=>$tmpDirectory));
            // Write image file
            $ioAdapter->write($fileName, $fileContent, 0666);
            unset($fileContent);

            // try to create Image object - it fails with Exception if image is not supported
            try {
                new Varien_Image($tmpDirectory . DS . $fileName);
            } catch (Exception $e) {
                // Remove temporary directory
                $ioAdapter->rmdir($tmpDirectory, true);

                throw new Mage_Core_Exception($e->getMessage());
            }

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage(
                $oggetto,
                $tmpDirectory . DS . $fileName,
                null,
                true
            );

            // Remove temporary directory
            $ioAdapter->rmdir($tmpDirectory, true);

            $gallery->getBackend()->updateImage($oggetto, $file, $data);

            if (isset($data['types'])) {
                $gallery->getBackend()->setMediaAttribute($oggetto, $data['types'], $file);
            }

            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_created', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('not_created', Mage::helper('score')->__('Cannot create image.'));
        }

        return $gallery->getBackend()->getRenamedImage($file);
    }

    /**
     * Update image data
     *
     * @param int|string $oggettoId
     * @param string $file
     * @param array $data
     * @param string|int $store
     * @return boolean
     */
    public function update($oggettoId, $file, $data, $store = null, $identifierType = null)
    {
        $data = $this->_prepareImageData($data);

        $oggetto = $this->_initOggetto($oggettoId, $store, $identifierType);

        $gallery = $this->_getGalleryAttribute($oggetto);

        if (!$gallery->getBackend()->getImage($oggetto, $file)) {
            $this->_fault('not_exists');
        }

        if (isset($data['file']['mime']) && isset($data['file']['content'])) {
            if (!isset($this->_mimeTypes[$data['file']['mime']])) {
                $this->_fault('data_invalid', Mage::helper('score')->__('Invalid image type.'));
            }

            $fileContent = @base64_decode($data['file']['content'], true);
            if (!$fileContent) {
                $this->_fault('data_invalid', Mage::helper('score')->__('Image content is not valid base64 data.'));
            }

            unset($data['file']['content']);

            $ioAdapter = new Varien_Io_File();
            try {
                $fileName = Mage::getBaseDir('media'). DS . 'score' . DS . 'oggetto' . $file;
                $ioAdapter->open(array('path'=>dirname($fileName)));
                $ioAdapter->write(basename($fileName), $fileContent, 0666);

            } catch(Exception $e) {
                $this->_fault('not_created', Mage::helper('score')->__('Can\'t create image.'));
            }
        }

        $gallery->getBackend()->updateImage($oggetto, $file, $data);

        if (isset($data['types']) && is_array($data['types'])) {
            $oldTypes = array();
            foreach ($oggetto->getMediaAttributes() as $attribute) {
                if ($oggetto->getData($attribute->getAttributeCode()) == $file) {
                     $oldTypes[] = $attribute->getAttributeCode();
                }
            }

            $clear = array_diff($oldTypes, $data['types']);

            if (count($clear) > 0) {
                $gallery->getBackend()->clearMediaAttribute($oggetto, $clear);
            }

            $gallery->getBackend()->setMediaAttribute($oggetto, $data['types'], $file);
        }

        try {
            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove image from oggetto
     *
     * @param int|string $oggettoId
     * @param string $file
     * @return boolean
     */
    public function remove($oggettoId, $file, $identifierType = null)
    {
        $oggetto = $this->_initOggetto($oggettoId, null, $identifierType);

        $gallery = $this->_getGalleryAttribute($oggetto);

        if (!$gallery->getBackend()->getImage($oggetto, $file)) {
            $this->_fault('not_exists');
        }

        $gallery->getBackend()->removeImage($oggetto, $file);

        try {
            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_removed', $e->getMessage());
        }

        return true;
    }


    /**
     * Retrieve image types (image, small_image, thumbnail, etc...)
     *
     * @param int $setId
     * @return array
     */
    public function types($setId)
    {
        $attributes = Mage::getModel('score/oggetto')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
            if ($attribute->isInSet($setId)
                && $attribute->getFrontendInput() == 'media_image') {
                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'code'         => $attribute->getAttributeCode(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Prepare data to create or update image
     *
     * @param array $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        return $data;
    }

    /**
     * Retrieve gallery attribute from oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($oggetto)
    {
        $attributes = $oggetto->getTypeInstance(true)
            ->getSetAttributes($oggetto);

        if (!isset($attributes[self::ATTRIBUTE_CODE])) {
            $this->_fault('not_media');
        }

        return $attributes[self::ATTRIBUTE_CODE];
    }

    /**
     * Retrie
     * ve media config
     *
     * @return Shaurmalab_Score_Model_Oggetto_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('score/oggetto_media_config');
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    protected function _imageToArray(&$image, $oggetto)
    {
        $result = array(
            'file'      => $image['file'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => array()
        );


        foreach ($oggetto->getMediaAttributes() as $attribute) {
            if ($oggetto->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Retrieve oggetto
     *
     * @param int|string $oggettoId
     * @param string|int $store
     * @param  string $identifierType
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _initOggetto($oggettoId, $store = null, $identifierType = null)
    {
        $oggetto = Mage::helper('score/oggetto')->getOggetto($oggettoId, $this->_getStoreId($store), $identifierType);
        if (!$oggetto->getId()) {
            $this->_fault('oggetto_not_exists');
        }

        return $oggetto;
    }
} // Class Shaurmalab_Score_Model_Oggetto_Attribute_Media_Api End
