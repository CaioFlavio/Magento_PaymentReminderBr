<?php 
	class CaioFlavio_PaymentReminder_Model_System_Config_Source_Payment_Values{
		public function toOptionArray(){
			$payments = Mage::getSingleton('payment/config')->getActiveMethods();

			$methods = array(
				array(	
					'value' => '', 
					'label'	=> Mage::helper('adminhtml')->__('Por favor escolha')
				)
			);

			foreach ($payments as $paymentCode=>$paymentModel) {
				$paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
				$methods[$paymentCode] = array(
				   'label'   => $paymentTitle,
				   'value' => $paymentCode,
				);
			}
			return $methods;
		}
	}