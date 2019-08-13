<?php

class Platnosci_Cashbill_Model_Payment_Cashbill extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'platnosci_cashbill';
    protected $_formBlockType = 'cashbill/form_cashbill';
    protected $_infoBlockType = 'cashbill/info_cashbill';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;

    var $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (is_null($this->_options)) {
            foreach ($this->_channels as $key => $value) {
                $this->_options[] = array(
                    'label' => $value,
                    'value' => $key
                );
            }
        }
        if(!$isMultiselect){
            array_unshift($this->_options, array('value'=>'', 'label'=>''));
        }
        return $this->_options;
    }
    
  
    
    public function getText()
    {
        return $this->getConfigData("text");
    }
    
    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('cashbill/cashbill/redirect');
    }
    
    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getRedirectionFormData(){
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $billing  = $order->getBillingAddress();
        $payment  = $order->getPayment()->getData();
        $store  = Mage::app()->getStore($this->getStoreId());
		
		$street = join(',',$billing->getStreet());
		
		$sign = md5($this->getShopID().$order->getBaseGrandTotal().'Zamówienie nr: '.$order_id.'PL'.$order_id.'Magento'.$billing->getFirstname().$billing->getLastname().$order->getCustomerEmail().$billing->getTelephone().$street.$billing->getCity().$billing->getPostcode().'Polska'.$this->getShopKey());
		
        $redirectionFormData = array(
            "service" => $this->getShopID(), //Identyfikator Punktu Płatności nadany w procesie tworzenia
            "amount" => $order->getBaseGrandTotal(), //kwota transakcji
			"lang" => 'PL',
			"desc" => "Zamówienie nr: ".$order_id, //Opis transakcji
			"userdata" => $order_id, //Dane dodatkowe - id zamówienia
			"ref"=> 'Magento',
			"sign" => $sign, //Podpis wysyłanych danych 
			"forname"  => $billing->getFirstname(),
            "surname" => $billing->getLastname(),
            "email" => $order->getCustomerEmail(),
            "tel" => $billing->getTelephone(),
            "street" =>  $street,
            "city" => $billing->getCity(),
            "postcode" => $billing->getPostcode(),
			"country" => 'Polska',
        );

        return (array)@$redirectionFormData;
    }
    
    public function getShopID(){
        return Mage::getStoreConfig('payment/platnosci_cashbill/shopid');
    }
	
	public function getShopKey(){
        return Mage::getStoreConfig('payment/platnosci_cashbill/shopkey');
    }

    
    public function getPaymentURI(){
		return 'https://pay.cashbill.pl/form/pay.php';
    }
    
   
    
}