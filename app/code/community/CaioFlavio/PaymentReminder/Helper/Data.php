<?php 
	class CaioFlavio_PaymentReminder_Helper_Data extends Mage_Core_Helper_Abstract{
		public function getDateFormat($date){
			return new DateTime($date);
		}

		public function getDateInterval($days){
			return new DateInterval('P'.$days.'D');
		}

		public 	function getActiveStores(){
			$stores = Mage::app()->getStores();
			$storeids = array();
			foreach ($stores  as $store) {
				if(Mage::getStoreConfig('paymentreminder/general/active', $store->getId()))
					$storeids[] = $store->getId();
			}
			return $storeids;
		}

	}