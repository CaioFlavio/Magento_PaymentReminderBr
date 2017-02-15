<?php 
	class CaioFlavio_PaymentReminder_Model_Email extends Mage_Core_Model_Abstract{
		public function send($templateId, $customerEmail, $customerName, $emailVars, $storeId){
			$sender = array(
				'name'  =>  Mage::getStoreConfig('trans_email/ident_sales/name', $storeId),
				'email' =>  Mage::getStoreConfig('trans_email/ident_sales/email', $storeId)
			);			
			$translate = Mage::getSingleton('core/translate');
			Mage::getModel('core/email_template')
				->sendTransactional($templateId, $sender, $customerEmail, $customerName, $emailVars, $storeId);
			$translate->setTranslateInline(true);
		}
	}