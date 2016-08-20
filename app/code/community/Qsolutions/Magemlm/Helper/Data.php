<?php

/**
 * Magemlm
 *
 * @category    Qsolutions
 * @package     Qsolutions_Magemlm
 * @copyright   Copyright (c) 2013 Q-Solutions  (http://www.qsolutions.com.pl)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Qsolutions_Magemlm_Helper_Data
    extends Mage_Core_Helper_Abstract {


	public $maxDepth = 0;


	public function getCustomerName ($customerId) {
		$customerModel = Mage::getModel('customer/customer')->load($customerId);
		return $customerModel->getName();
	}


	public function getReferrerName ($customerId) {
		$customer = Mage::getModel('magemlm/customer')->load($customerId , 'customer_id');
		$referrer = $customer->getReferrerId();
		return $this->getCustomerName($referrer);
	}


	public function getCustomerGender ($customerId) {
		$customerModel = Mage::getModel('customer/customer')->load($customerId);
		return $customerModel->getGender();
	}


	public function getCustomerImage ($custemerId, $size = "50") {
		$customerMlmModel = Mage::getModel('magemlm/customer')->load($custemerId , 'customer_id');
		$image =  $customerMlmModel->getMagemlmImage();

       if ($image) { 
                       $dir = Mage::getBaseDir('media') . DS . 'magemlm';
                       $img = $image;
                   } else { 
                        $dir = Mage::getBaseDir() . '/' . str_replace(Mage::getBaseUrl(), '', $this->getSkinUrl('images/'));
                        $img = 'def_user.jpeg';
                    }
        return Mage::helper('catalog/image')->resizeImg($dir, $img, $size);
	}


	public function compensationSum () {
		$sum	= 0;
		$magemlmLevels 	= Mage::getModel('magemlm/unilevel') -> getCollection();
		foreach ($magemlmLevels as $magemlmLevel) {
			$sum += (double)$magemlmLevel->getLevelCommission();
		}
		return $sum;
	}


	public function getFirstOrderDate () {
    if(!is_object($col = Mage::getModel('sales/order')->getCollection())) return '';
		$orderCollection	= $col->addAttributeToSort('created_at', 'ASC')->setPageSize(1);

		if ($orderCollection->count()) {
			foreach ($orderCollection as $order) {
				return date('Y-m' , strtotime($order->getCreatedAt()));
				exit;
			}
		} else {
			return date('Y-m');
		}
	}


	public function getCustomerCreatedDate () {
		return date('Y-m' , strtotime( Mage::getSingleton('customer/session')->getCustomer()->getCreatedAt()));
	}


	public function countMonths($start, $end) {

		if ($start != $end ) {
		    // $startParsed 	= date_parse_from_format('Y-m', $start);
		    $startMonth 	= date('m' , strtotime($start)) ; // ['month'];
		    $startYear 		= date('Y' , strtotime($start)) ; // ['year']

		    // $endParsed 		= date_parse_from_format('Y-m', $end);
		    $endMonth 		= date('m' , strtotime($end)) ; // ['month']
		    $endYear 		= date('Y' , strtotime($end)) ; //['year'];

		    return ($endYear - $startYear) * 12 + ($endMonth - $startMonth) + 1;
		} else {
			return 0;
		}
	}


	public function getCustomerReferralId ($customerId) {
		$customerMlmModel = Mage::getModel('magemlm/customer')->load($customerId , 'customer_id');
		return $customerMlmModel->getReferrerId();
	}


	public function getCustomerLevel($customerId) {

		Mage::helper('magemlm')->getCustomerReferralId($customerId);
		$level = 0;

		while (Mage::helper('magemlm')->getCustomerReferralId($customerId) || strpos($customerId , "store") ) {
			$customerId = Mage::helper('magemlm')->getCustomerReferralId($customerId);

			$pos = strpos($customerId, 'store');
			if ($pos !== false)  {
				break ;
			}
			$level++;
		}
		// return customer level in system
		return $level+1;
	}

	/**
	 * Checking if customer exists
	 *
	 * @return customerID or NULL
	 */
	public function customerExists ($customerId) {
		return Mage::getModel ('customer/customer')->load($customerId)->getId();
	}


	public function getFrontlineCount ($customerId) {
		$customerMlmModel = Mage::getModel('magemlm/customer')->getCollection()->addFieldToFilter('referrer_id' , array ('eq' => $customerId));
		return $customerMlmModel->count();
	}


	public function structureDepth ($customerId , $currentDepth = 1) {
		$depth = 0;
		$customerChilds = $this->_hasChildren($customerId);
		if ($customerChilds) {
			$collection = Mage::getModel('magemlm/customer')->getCollection()->addFieldToFilter('referrer_id', array('eq' => $customerId));
			foreach ($collection as $customer) {
				if (Mage::helper('magemlm')->customerExists($customer->getCustomerId())) {
					$customerChilds = $this->_hasChildren($customer->getCustomerId());
					$depth 			+= $this->structureDepth($customer->getCustomerId() , $currentDepth+1);
					if ($this->maxDepth < $currentDepth) {
						$this->maxDepth = $currentDepth;
					}
				}
			}
		}
		return $depth;
	}


	public function structureCount ($customerId) {
		$count = 1;
		$customerChilds = $this->_hasChildren($customerId);
		if ($customerChilds) {
			$collection = Mage::getModel('magemlm/customer')->getCollection()->addFieldToFilter('referrer_id', array('eq' => $customerId));
			foreach ($collection as $customer) {
				if (Mage::helper('magemlm')->customerExists($customer->getCustomerId())) {
					$customerChilds = $this->_hasChildren($customer->getCustomerId());
					$count		   += $this->structureCount($customer->getCustomerId());
				}
			}
		} else {
			$count = 1;
		}

		return $count;
	}


	protected function _hasChildren ($customerId) {
		$customerMlmModel = Mage::getModel('magemlm/customer')->getCollection()->addFieldToFilter('referrer_id' , array ('eq' => $customerId));
		return ($customerMlmModel->count() > 0 ? true : false);
	}

	public function getTypeLabel($typeId) { 
		switch ($typeId) {
			case '1':
				return Mage::helper('core')->__('Invite');
				break;
			case '2':
				return Mage::helper('core')->__('Post created');
				break;
			
			default:
				return Mage::helper('core')->__('Unknown');
				break;
		}

	}

	public function getTypeId($type) { 

		switch ($type) {
			case 'Invite':
				return 1;
				break;
			case 'Post created':
				return 2;
				break;
			
			default:
				return 0;
				break;
		}
	}


}


