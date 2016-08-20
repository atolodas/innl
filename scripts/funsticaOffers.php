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
 * @package     Mage_Shell
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once '../shell/abstract.php';

/**
 * Magento Log Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Funstica_Offers extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        $offersSetId = Mage::helper('score/oggetto')->getSetIdByCode('Offer');
        $offersExtraSetId = Mage::helper('score/oggetto')->getSetIdByCode('OfferExtra');
        $offers = Mage::getModel('score/oggetto')->getCollection()
        	->addAttributeToFilter('attribute_set_id',array('in'=>array($offersSetId,$offersExtraSetId)))
			->addAttributeToFilter('visibility',array('neq'=>1))
            ->addAttributeToFilter(array(
            		 array(
		                'attribute' => 'lat',
		                'null'=>true
		            ),
            		  array(
		                'attribute' => 'lng',
		                'null'=>true
		            ),
            		   array(
		                'attribute' => 'city_dict',
		                'null'=>true
		            ),
		            array(
		                'attribute' => 'lat',
		                'eq'=>''
		            ),array(
		                'attribute' => 'lng',
		                'eq'=>''
		            ),array(
		                'attribute' => 'city_dict',
		                'eq'=>''
		            ),
            	))
            ->addAttributeToSelect('*')
            ->addStoreFilter();
            echo count($offers)." offers with missed data found. \n";
           
           foreach ($offers as $offer) {
           		$places = $offer->getRelatedOggettoCollection()
			            ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('Place'))
			            ->addAttributeToFilter('visibility',array('neq'=>1))
			            ->addAttributeToSelect('*')
			            ->addStoreFilter();
			            echo count($places)." places found for Offer {$offer->getName()}\n";
			            if(count($places) == 1) { 
			            	$place = $places->getFirstItem();
			            	echo "Saving data from Place {$place->getName()} to Offer {$offer->getName()}\n";
			            	$offer->addData(array('lat'=>$place->getLat(),'lng'=>$place->getLng(), 'city_dict'=>$place->getCityDict()))->save();
			            	echo "Offer {$offer->getName()} saved \n";
			            }
           }
       
    }

  
}

$shell = new Funstica_Offers();
$shell->run();
