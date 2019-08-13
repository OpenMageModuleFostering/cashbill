<?php

class Platnosci_Cashbill_CashbillController extends Mage_Core_Controller_Front_Action {

	public function redirectAction() {
		$session = Mage::getSingleton('checkout/session');
		
        $session->setCashbillQuoteId($session->getQuoteId());

        $this->getResponse()->setBody($this->getLayout()->createBlock('cashbill/payment_cashbill_redirect')->toHtml());
        $session->unsQuoteId();
	}
	

	
	
	public function returnAction() {
			// Dane punktu otrzymane po zakonczeniu procesu rejestracji
		$service = Mage::getStoreConfig('payment/platnosci_cashbill/shopid');
		$key = Mage::getStoreConfig('payment/platnosci_cashbill/shopkey');
		// Funkcja sprawdzajaca poprawnosc sygnatury
		$data = $_GET;
		
			if( md5($data['service'].$data['orderid'].$data['amount'].$data['userdata'].$data['status'].$key) == $_GET['sign'] )
		{$test = 1;}else{$test = 2;}

		$params = array('n' => $data['userdata'], 'amount' => $data['amount']);
		if( $test == 1 && $_GET['service'] == $service)
		{
		// prawidlowa sygnatura, w zaleznosci od statusu odpowiednia informacja dla klienta
		if( strtoupper($_GET['status']) == 'OK' ) {
		$this->_redirect('cashbill/cashbill/success/', $params);
		}else {
		$this->_redirect('cashbill/cashbill/failure', $params);
		}
		}else {
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getCashbillQuoteId(true));
		$session->addError("wystąpił inny błąd. zgłoś to administratora sklepu.");
		$this->_redirect('checkout/cart');
		}
	}
	

	public function serwerAction() {
		// Dane punktu otrzymane po zakonczeniu procesu rejestracji
		$service = Mage::getStoreConfig('payment/platnosci_cashbill/shopid');
		$key = Mage::getStoreConfig('payment/platnosci_cashbill/shopkey');
		// Funkcja sprawdzajaca poprawnosc sygnatury
		$data = $_POST;
		
			if( md5($data['service'].$data['orderid'].$data['amount'].$data['userdata'].$data['status'].$key) == $_POST['sign'] )
		{$test = 1;}else{$test = 2;}
		
		$params = array(  'n' => $data['userdata'],'amount'=>$data['amount']);
		if( $test == 1 && $_POST['service'] == $service)
		{
		if( strtoupper($_POST['status']) == 'OK' ) {
		$this->_redirect('cashbill/cashbill/hsuccess/', $params);
		}
		else {
		    $this->_redirect('cashbill/cashbill/herror/', $params);
		}
		}
		else {
            echo 'BLAD SYGNATURY';
        }
	}
	
	
	
	public function hsuccessAction() {
		$order_id = $this->getRequest()->getParam('n');
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		if($order->canInvoice()) {
		$order->sendNewOrderEmail();
		$order->addStatusHistoryComment ("Płatność w kwocie ".$this->getRequest()->getParam('amount')." zł została przyjęta przez CashBill.pl", Mage::getStoreConfig('payment/platnosci_cashbill/complete_order_status'));
		$order->save();
		}
		
		$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCashbillQuoteId(true));

        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		
		echo 'OK';
	}
	
	public function herrorAction()
	{
	    $order_id = $this->getRequest()->getParam('n');
	    
	    $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
	    
	    if(!$order->getId()) { return FALSE; }
	    
	    $order->cancel();
	    $order->save();
	    
        echo 'OK';
	}
	

	
	

	public function successAction() {
		$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		//$order_id = $this->getRequest()->getParam('n');
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		if($order->canInvoice()) {
		$order->sendNewOrderEmail();
		$order->addStatusHistoryComment ("Płatność w kwocie ".$this->getRequest()->getParam('amount')." zł została przyjęta przez CashBill.pl", Mage::getStoreConfig('payment/platnosci_cashbill/complete_order_status'));
		$order->save();
		}
		
		$session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getCashbillQuoteId(true));

        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		
		$this->_redirect('checkout/onepage/success');
	}
	
	public function failureAction() {
		//$order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order_id = $this->getRequest()->getParam('n');
		
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		if(!$order->getId()) { return FALSE; }
		
		$order->cancel();
		$order->save();
		
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getCashbillQuoteId(true));
		$session->addError("Płatność za pomocą serwisu Cashbill została zakończona niepowodzeniem.");
		
		$this->_redirect('checkout/cart');
	}
}