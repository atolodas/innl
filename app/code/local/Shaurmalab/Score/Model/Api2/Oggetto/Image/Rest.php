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
 * Abstract API2 class for oggetto images resource
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_Score_Model_Api2_Oggetto_Image_Rest extends Shaurmalab_Score_Model_Api2_Oggetto_Rest
{
    /**
     * Attribute code for media gallery
     */
    const GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Allowed MIME types for image
     *
     * @var array
     */
    protected $_mimeTypes = array(
        'image/jpg'  => 'jpg',
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/png'  => 'png'
    );

    /**
     * Retrieve oggetto image data for customer and guest roles
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $imageData = array();
        $imageId = (int)$this->getRequest()->getParam('image');
        $galleryData = $this->_getOggetto()->getData(self::GALLERY_ATTRIBUTE_CODE);

        if (!isset($galleryData['images']) || !is_array($galleryData['images'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        foreach ($galleryData['images'] as $image) {
            if ($image['value_id'] == $imageId && !$image['disabled']) {
                $imageData = $this->_formatImageData($image);
                break;
            }
        }
        if (empty($imageData)) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $imageData;
    }

    /**
     * Retrieve oggetto images data for customer and guest
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $images = array();
        $galleryData = $this->_getOggetto()->getData(self::GALLERY_ATTRIBUTE_CODE);
        if (isset($galleryData['images']) && is_array($galleryData['images'])) {
            foreach ($galleryData['images'] as $image) {
                if (!$image['disabled']) {
                    $images[] = $this->_formatImageData($image);
                }
            }
        }
        return $images;
    }

    /**
     * Retrieve media gallery
     *
     * @throws Mage_Api2_Exception
     * @return Shaurmalab_Score_Model_Oggetto_Attribute_Backend_Media
     */
    protected function _getMediaGallery()
    {
        $attributes = $this->_getOggetto()->getTypeInstance(true)->getSetAttributes($this->_getOggetto());

        if (!isset($attributes[self::GALLERY_ATTRIBUTE_CODE])
            || !$attributes[self::GALLERY_ATTRIBUTE_CODE] instanceof Mage_Eav_Model_Entity_Attribute_Abstract
        ) {
            $this->_critical('Requested oggetto does not support images', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $galleryAttribute = $attributes[self::GALLERY_ATTRIBUTE_CODE];
        /** @var $mediaGallery Shaurmalab_Score_Model_Oggetto_Attribute_Backend_Media */
        $mediaGallery = $galleryAttribute->getBackend();
        return $mediaGallery;
    }

    /**
     * Create image data representation for API
     *
     * @param array $image
     * @return array
     */
    protected function _formatImageData($image)
    {
        $result = array(
            'id'        => $image['value_id'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'url'       => $this->_getMediaConfig()->getMediaUrl($image['file']),
            'types'     => $this->_getImageTypesAssignedToOggetto($image['file'])
        );
        return $result;
    }

    /**
     * Retrieve image types assigned to oggetto (base, small, thumbnail)
     *
     * @param string $imageFile
     * @return array
     */
    protected function _getImageTypesAssignedToOggetto($imageFile)
    {
        $types = array();
        foreach ($this->_getOggetto()->getMediaAttributes() as $attribute) {
            if ($this->_getOggetto()->getData($attribute->getAttributeCode()) == $imageFile) {
                $types[] = $attribute->getAttributeCode();
            }
        }
        return $types;
    }

    /**
     * Retrieve media config
     *
     * @return Shaurmalab_Score_Model_Oggetto_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('score/oggetto_media_config');
    }

    /**
     * Create file name from received data
     *
     * @param array $data
     * @return string
     */
    protected function _getFileName($data)
    {
        $fileName = 'image';
        if (isset($data['file_name']) && $data['file_name']) {
            $fileName = $data['file_name'];
        }
        $fileName .= '.' . $this->_getExtensionByMimeType($data['file_mime_type']);
        return $fileName;
    }

    /**
     * Retrieve file extension using MIME type
     *
     * @throws Mage_Api2_Exception
     * @param string $mimeType
     * @return string
     */
    protected function _getExtensionByMimeType($mimeType)
    {
        if (!array_key_exists($mimeType, $this->_mimeTypes)) {
            $this->_critical('Unsuppoted image MIME type', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $this->_mimeTypes[$mimeType];
    }

    /**
     * Get file URI by its id. File URI is used by media backend to identify image
     *
     * @throws Mage_Api2_Exception
     * @param int $imageId
     * @return string
     */
    protected function _getImageFileById($imageId)
    {
        $file = null;
        $mediaGalleryData = $this->_getOggetto()->getData('media_gallery');
        if (!isset($mediaGalleryData['images'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        foreach ($mediaGalleryData['images'] as $image) {
            if ($image['value_id'] == $imageId) {
                $file = $image['file'];
                break;
            }
        }
        if (!($file && $this->_getMediaGallery()->getImage($this->_getOggetto(), $file))) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $file;
    }
}
