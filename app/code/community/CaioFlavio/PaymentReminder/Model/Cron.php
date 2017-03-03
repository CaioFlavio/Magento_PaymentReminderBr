<?php 
	class CaioFlavio_PaymentReminder_Model_Cron extends Mage_Core_Model_Abstract{
		protected function getOrders($from, $to, $storeId){
			$paymentMethod = Mage::getStoreConfig('paymentreminder/general/payment_method');
			$status		   = Mage::getStoreConfig('paymentreminder/general/payment_status');
			$orderCollection = Mage::getModel('sales/order')
			->getCollection()
	        ->join(
	            array('payment' => 'sales/order_payment'),
	            'main_table.entity_id = payment.parent_id',
	            array('payment_method' => 'payment.method')
	        )
			->addFieldToFilter('created_at', array(
					'from'  => $from,
					'to'	=> $to,
					'data' 	=> true,
				)
			)
			->addFieldToFilter('payment.method', $paymentMethod)
			->addFieldToFilter('store_id', $storeId)
			->addFieldToFilter('status', $status);

			return $orderCollection;		
		}
		
		protected function sendReminderEmail($storeId){
			$reminderHelper  = Mage::helper('CaioFlavio_PaymentReminder');
			$today 			 = new DateTime(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
			$sendDate		 = new DateTime(Mage::getModel('core/date')->date('Y-m-d 00:00:00'));
			$vencDays		 = Mage::getStoreConfig('paymentreminder/general/expiration_days', $storeId);
			$antecipatedDays = Mage::getStoreConfig('paymentreminder/first_email/antecipate_days', $storeId);
			$emailDifference = $vencDays - $antecipatedDays;
			$emailDay 		 = $sendDate->sub(new DateInterval('P'.$emailDifference.'D'));
			$from 			 = $emailDay->format('Y-m-d 00:00:00');
			$to 			 = $emailDay->format('Y-m-d 23:59:59');
			$orderCollection = $this->getOrders($from, $to, $storeId);
			foreach ($orderCollection as $order) {
				$storeId 		= $order->getStoreId();
				$templateId 	= Mage::getStoreConfig('paymentreminder/first_email/template', $storeId);
				$customerName 	= $order->getCustomerName();
				$customerEmail  = $order->getCustomerEmail();
				$createdAt 		= $reminderHelper->getDateFormat($order->getCreatedAt);

				$emailVars = array(
					'customerName' => $customerName,
					'customerEmail'=> $customerEmail,
					'orderUrl'	   => Mage::getUrl('sales/order/view', 
						array(
							'_store'  => $storeId,
							'order_id'=> $order->getId(),
						)
					),
					'buyDay'		 => $createdAt->format('d/m/Y'),
					'expirationDate' => $createdAt->add($reminderHelper->getDateInterval($vencDays))->format('d/m/Y'),
				);
				Mage::getModel('caioflavio_paymentreminder/email')->send($templateId, $customerEmail, $customerName, $emailVars, $storeId);
			}
		}

		public function reminderSend(){
			$activeStores 	= Mage::helper('CaioFlavio_PaymentReminder')->getActiveStores();
			foreach($activeStores as $storeId){
				try {
					$this->sendReminderEmail($storeId);
				} catch (Exception $e) {
					Mage::log('Erro ao enviar email ...' . $e->getMessage(), null, 'paymentreminder.log');
				}
			}
		}
	}