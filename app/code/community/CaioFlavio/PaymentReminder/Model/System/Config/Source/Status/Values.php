<?php 
	class CaioFlavio_PaymentReminder_Model_System_Config_Source_Status_Values{
		public function toOptionArray(){
			$orderStatusCollection = Mage::getModel('sales/order_status')->getResourceCollection()->getData();

			$status = array(
				array(
					'value' => '', 
					'label'	=> Mage::helper('adminhtml')->__('Por favor escolha')
				)
			);

			foreach($orderStatusCollection as $orderStatus) {
			    $status[] = array (
			        'value' => $orderStatus['status'], 
			        'label' => $orderStatus['label']
			    );
			}

			return $status;
		}
	}